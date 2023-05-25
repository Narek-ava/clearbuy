<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Attribute;
use App\Models\Badge;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Currency;
use App\Models\OS;
use App\Models\Product;
use App\Models\ProductContent;
use App\Models\ProductDealPrice;
use App\Models\ProductImage;
use App\Models\ProductPrice;
use App\Models\ProductPriceChange;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Models\Website;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

use App\Http\Helpers\CSVExport;
use App\Http\Helpers\Scrapper\AmazonScrapper;
use App\Http\Requests\Product as Requests;
use App\Events\ProductRetailerAdded;
use App\Models\ProductAttributeValue;
use App\Models\Rating;
use App\Models\RatingProduct;
use App\Services\ZapierService;

class ProductController extends BaseItemController {

    protected $baseUrl = '/admin/products';
    protected $source_item;    //save default variant model without id
    protected $source_item_id; //save default variant id
    protected $source_request; //save default variant request
    protected $dirty_fields;   //determine if default variant fields have been changed
    protected $current_variant = true; //determine if current save only (for images)
    public const COLUMNS = [
        "name",
        "sku",
        "model",
        "model_family",
        "price_msrp",
        "currency_msrp",
        "price_current",
        "currency_current",
        "size_length",
        "size_width",
        "size_height",
        "weight",
        "date_publish",
        "is_promote",
        "excerpt",
        "summary_main",
        "reasons_to_buy",
        "full_overview",
        "seo_keywords",
        "tags",
        "category_id",
        "brand_id"
    ];

    public function list(Requests\ListRequest $request) {

        $items = Product::orderByColumn($request->sort, $request->order)
            ->where('parent_id', null)
            ->with(['category.os', 'brand', 'specifications']);

        if ($request->get('search')) {
            $items->where('name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('sku', 'LIKE', '%' . $request->search . '%')
                ->orWhere('model', 'LIKE', '%' . $request->search . '%')
                ->orWhere('model_family', 'LIKE', '%' . $request->search . '%')
                ->orWhere('excerpt', 'LIKE', '%' . $request->search . '%')
                ->orWhere('summary_main', 'LIKE', '%' . $request->search . '%')
                ->orWhere('reasons_to_buy', 'LIKE', '%' . $request->search . '%')
                ->orWhere('full_overview', 'LIKE', '%' . $request->search . '%')
                // ->orWhere('tags', 'LIKE', '%' . $request->search . '%')
                ->orWhere('price_current', 'LIKE', '%' . $request->search . '%')
                ->orWhere('price_msrp', 'LIKE', '%' . $request->search . '%')
                ->orWhere('created_at', 'LIKE', '%' . $request->search . '%')
                ->orWhere('date_publish', 'LIKE', '%' . $request->search . '%')
                ->orWhereHas('brand', function(Builder $query) use($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%');
                })
                ->orWhereHas('category', function(Builder $query) use($request) {
                    $query->where('name', 'LIKE', '%' . $request->search . '%');
                });
        }

        //filter by pending/draft via select box
        if ($request->session()->exists('product_listing_pending_draft')) {

            $type = $request->session()->get('product_listing_pending_draft');

            if (!empty($type)) {
                $formRequest = new Requests\SaveRequest;
                $formRequest->setRules(null);
                $saveRules = $formRequest->rules();

                $requiredFields = collect($saveRules)->filter(function ($value, $key) {
                    return Str::contains($value, 'required'); //return only required
                })->reject(function ($value, $key) {
                    return Str::contains($key, '*'); //reject relations
                })->toArray();

                switch ($type) {

                    case 'draft': //required fields aren't filled

                        if (!empty($requiredFields)) {
                            foreach ($requiredFields as $k => $v) {
                                $items->orWhereNull($k);
                            }
                        }
                        break;

                    case 'pending': //no retailer links and/or have urgency field

                        //$items->orWhereNotNull('urgency'); //prior have urgency field
                        $items->doesnthave('deal')->doesnthave('prices');
                        break;
                }
            }
        }

        //filter by category via select box
        if ($request->session()->exists('product_listing_category')) {

            $cat_id = $request->session()->get('product_listing_category');

            if (!empty($cat_id)) {
                $items->whereHas('category', function (Builder $query) use ($cat_id) {
                    $query->where('id', $cat_id);
                });
            }
        }

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);
        $listData['categories'] = Category::all();
        $listData['brands'] = Brand::all();
        //$listData['attrTypes'] = Attribute::types()->flip();

        return view('product.list', $listData);
    }

    public function form(Requests\GetFormRequest $request) {

        $formData = $this->getFormData($request);

        if ($request->copy_id) {
            $formData['item'] = Product::findOrFail($request->copy_id);
            $formData['is_copy'] = true;
        } else {
            $formData['item'] = Product::findOrNew($request->id);
        }

        $formData['brands'] = Brand::all();
        $formData['currencies'] = Currency::all();
        $formData['categories'] = Category::listWithFullPath();
        $formData['websites'] = Website::all();
        $formData['badges'] = Badge::all();
        $formData['tags'] = Tag::all();
        $formData['contentTypes'] = ProductContent::allowedTypes();
        $formData['attributeKinds'] = Attribute::kindsList();

        // Ratings for New product or Existing product
        if(!$formData['item']) {
            $formData['allRatings'] = Rating::orderBy(['sort_order', 'id'])->get();
        } else {
            $ratings = count($formData['item']->ratings) ? $formData['item']->ratings : Rating::orderBy('sort_order')->get();
            $ratingsIdArr = $ratings->pluck('id');
            $ratings = $ratings->merge(Rating::whereNotIn('id', $ratingsIdArr)->get());
            $formData['allRatings'] = $ratings->sortBy(['sort_order', 'id']);
        }

        return view('product.form', $formData);
    }

    public function save(Requests\SaveRequest $request) {

        $item = Product::firstOrNew(['id' => $request->id]);
        $isItemExist = $item->id ? true : false;

        if (!is_null($request->id)) { //existing product
            if (!is_null($request->variant_id)) { //not default variant
                if (intval($request->variant_id) != intval($item->variant_id)) {

                    if (is_null($item->parent_id)) { //first child creation

                        $children = Product::where('parent_id', $request->id)->get();

                        if ($children->contains('variant_id', $request->variant_id)) {

                            $variant_name = ProductVariant::where('id', $request->variant_id)->value('name');

                            return back()->withErrors([
                                'name' => 'A copy of product with variant ' . $variant_name . ' already exist!'
                            ])->withInput();
                        }

                        $item = new Product; //create new copy (child)
                        $item->parent_id = $request->id;
                    }
                }
            } else { //save source parent item for comparison with children

                $this->source_item = $item->replicate(); //copy without id
                $this->source_item_id = $item->id;
                $this->source_request = $request;
            }
        }

        $oldMsrp = $item->price_msrp;
        $oldMsrpCurrency = $item->msrpCurrency; //object

        // Is current original product
        $isOriginalProduct = ($item->id && !$this->source_item_id) || ($item->id == $this->source_item_id) ? true : false;

        //general
        $item->name = $request->name;
        $item->sku = $request->sku;
        $item->asin = $request->asin;
        $item->rating = $request->rating;
        $item->model = $request->model;
        $item->model_family = $request->model_family;
        $item->price_msrp = $request->price_msrp;
        $item->price_current = $request->price_current;
        $item->size_length = $request->size_length;
        $item->size_width = $request->size_width;
        $item->size_height = $request->size_height;
        $item->weight = $request->weight;
        $item->date_publish = $request->date_publish;
        $item->is_promote = !!$request->is_promote;
        $item->review_url = $request->review_url ?? null;
        $item->buyers_guide_url = $request->buyers_guide_url ?? null;

        //description
        $item->tagline = $request->tagline;
        $item->excerpt = $request->excerpt;
        $item->summary_main = $request->summary_main;
        $item->reasons_to_buy = $request->reasons_to_buy;
        $item->full_overview = $request->full_overview;
        $item->product_url = $request->product_url;
        $item->pros = $request->pros;
        $item->cons = $request->cons;

        $item->tags()->detach();
        if ($request->tags) {
            foreach ($request->tags as $tag) {
                $item->tags()->attach($tag);
            }
        }

        if (is_null($item->parent_id)) {
            $item_fields = collect($item->attributesToArray()); //get item fields

            //to exclude from the request for children (save only changed parent fields)
            $this->dirty_fields = $item_fields->filter(function ($value, $key) use ($item) {
                return $item->isDirty($key);
            });
        }

        $item->save();

        // Only for new product
        if(!$isItemExist) {
            ZapierService::productCreated($item->id);
        } elseif($isOriginalProduct) { // Only for existing parent product
            ZapierService::productUpdated($item->id);
        }

        //variant
        if (!is_null($request->variant_id)) {
            try {
                $variant = ProductVariant::findOrFail($request->variant_id);
                $item->variant()->associate($variant);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            }
        }

        //images
        if($this->current_variant) {
            $item->images()->delete();
            if ($request->images) {
                foreach ($request->images as $order => $path) {
                    $item->images()->save(new ProductImage(['path' => $path, 'order' => $order]));
                }
            }
        }

        //contents
        $item->contents()->delete();

        if ($request->contents) {
            foreach ($request->contents as $content) {
                $item->contents()->save(new ProductContent($content));
            }
        }

        //relations
        try {
            $currency = Currency::findOrFail($request->currency_msrp);
            $item->msrpCurrency()->associate($currency);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {

            if (!$request->draft) {
                return back()->withErrors([
                    'currency_msrp' => 'Selected msrp currency does not exist'
                ])->withInput();
            }
        }

        // try {
        //     $currency = Currency::findOrFail($request->currency_current);
        //     $item->currentCurrency()->associate($currency);
        // } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
        //     if(!$request->draft){
        //         return back()->withErrors([
        //             'currency_current' => 'Selected current currency does not exist'
        //         ])->withInput();
        //     }
        // }


        try {
            $category = Category::findOrFail($request->category_id);
            $item->category()->associate($category);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {

            if (!$request->draft) {
                return back()->withErrors([
                    'category_id' => 'Selected category does not exist'
                ])->withInput();
            }
        }

        try {
            $brand = Brand::findOrFail($request->brand_id);
            $item->brand()->associate($brand);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {

            if (!$request->draft) {
                return back()->withErrors([
                    'brand_id' => 'Selected brand does not exist'
                ])->withInput();
            }
        }

        $item->releasedWithOS()->dissociate();
        if ($request->released_with_os) {
            $os = OS::find($request->released_with_os);
            if ($os) {
                $item->releasedWithOS()->associate($os);
            }
        }

        $item->updatableToOS()->sync($request->updatable_to_os);
        $item->similarProducts()->sync($request->similar);
        $item->websites()->sync($request->websites);
        $item->badges()->sync($request->badges);

        //save attributes values
        //no more blind deletion bcs there may be attributes from another group type (repeatable/non-repeatable)
        //if($isOriginalProduct) ProductAttributeValue::where('product_id', $item->id)->delete();


        if (is_array($request->product_attributes)) {
            $ar_attributes = [];
            $attrTypes = Attribute::types()->flip();


            foreach ($request->product_attributes as $id => $value) {
                $attr = Attribute::findOrFail($id);

                $ar_attributes[$id] = [
                    'attribute_option_id' => $attr->type == $attrTypes->get('single option') ? $value : null,
                    'value_numeric' => ($attr->type == $attrTypes->get('numeric') OR $attr->type == $attrTypes->get('decimal')) ? $value : null,
                    'value_text' => $attr->type == $attrTypes->get('string') ? $value : null,
                    'value_boolean' => $attr->type == $attrTypes->get('boolean') ? $value : null,
                    'value_date' => $attr->type == $attrTypes->get('datetime') ? $value : null
                ];
            }
            if(!empty($ar_attributes)) $item->attributes()->syncWithoutDetaching($ar_attributes);
        }

        if (is_array($request->product_attributes_multiple)) { //multiple options

            foreach ($request->product_attributes_multiple as $k => $v) {
                $item->attributes()->detach($v['key']);
            }

            foreach ($request->product_attributes_multiple as $k => $v) {
                $item->attributes()->attach([
                    $v['key'] => ['attribute_option_id' => $v['val']]
                ]);
            }

            // TODO: deal with last item deletion
        }

        if($isOriginalProduct) {
            $oldRatings = RatingProduct::where('product_id', $item->id)->get();
            $newRatingsArr = [];
            foreach($request->ratings as $id => $value) {
                if((float) $value) {
                    $newRatingsArr[$id] = [
                        'product_id' => $item->id,
                        'rating_id' => $id,
                        'admin_rating' => $value,
                        'user_rating' => $oldRatings->firstWhere('rating_id', $id)->user_rating ?? 0,
                        'votes' => $oldRatings->firstWhere('rating_id', $id)->votes ?? 0
                    ];
                }
            }

            $item->ratings()->sync($newRatingsArr);
        }

        // Log::channel('import')->info('PRODUCT: '.$item->id);

        $was_created = false;

        //retailer deals prices
        if($isOriginalProduct) {

            $isPendingBefore = $item->is_pending;

            if($request->deal_prices != null && is_array($request->deal_prices)) {
                $ids = [];

                foreach ($request->deal_prices as $key => $linkArr) {
                    $link = (object) $linkArr;

                    if(Currency::find($link->currency_id) == null || Agent::find($link->agent_id) == null) continue;

                    $data = [
                        'agent_id' => $link->agent_id,
                        //'product_id' => $item->id,
                        'currency_id' => $link->currency_id,
                        'is_hot' => (bool) ($link->is_hot ?? false),
                        'is_free' => (bool) ($link->is_free ?? false),
                        'recommended' => (bool) ($link->recommended ?? false),
                        'price' => $link->price,
                        'original_price' => $link->original_price,
                        'coupon_code' => $link->coupon_code ?? null,
                        'retailer_custom_text' => $link->retailer_custom_text ?? null,
                        'url' => $link->url ?? null,
                        'expiry_date' => $link->expiry_date,
                        'expiry_notification' => false
                    ];


                    // Create deals for Default and Variants
                    foreach(array_unique([$request->id, $item->id]) as $id) {
                        $dealItem = ProductDealPrice::updateOrCreate(
                            ['agent_id' => $link->agent_id, 'product_id' => $id],
                            $data
                        );

                        if($dealItem->getRawOriginal('created_at') == $dealItem->getRawOriginal('updated_at')) {
                            ZapierService::dealCreated($dealItem->id);
                        } else {
                            ZapierService::dealUpdated($dealItem->id);
                        }
                    }
                    $ids[] = $dealItem->id;
                }

                $item->deal()->whereNotIn('id', $ids)->delete();
            } else {
                $item->deal()->delete();

            }

            if($isPendingBefore && $item->deal()->count()) ZapierService::productMovesFromPendingToNormal($item->id);

            // retailer product prices
            if($request->product_prices != null && is_array($request->product_prices)) {
                $ids = [];
                foreach ($request->product_prices as $key => $linkArr) {
                    $link = (object)$linkArr;

                    if(Currency::find($link->currency_id) == null || Agent::find($link->agent_id) == null) continue;

                    $data = [
                        'agent_id' => $link->agent_id,
                        //'product_id' => $item->id,
                        'currency_id' => $link->currency_id,
                        'current_msrp' => $link->current_msrp,
                        'original_msrp' => $link->original_msrp,
                        'url' => $link->url ?? null,
                        'recommended' => (bool) ($link->recommended ?? false)
                    ];

                    // Create prices for Default and Variants
                    foreach([$request->id, $item->id] as $id) {
                        $pricesItem = ProductPrice::updateOrCreate(
                            ['agent_id' => $link->agent_id, 'product_id' => $id],
                            $data
                        );
                    }

                    $ids[] = $pricesItem->id;
                }
                $item->prices()->whereNotIn('id', $ids)->delete();
            } else {
                $item->prices()->delete();
            }
        }


        // TODO: define that deal or price was created

        if($was_created) {
            try{
                ProductRetailerAdded::dispatch($item); //send email to request submitter
            }catch(\Exception $e){}
        }


        $item->save();
        $item->refresh();

        //price_change

        // if (($oldPrice !== null && $oldPrice != $item->price_current) ||
        // ($oldPriceCurrency !== null && $oldPriceCurrency->id != $item->currentCurrency->id))
        // {
        //     $item->priceChanges()->save(ProductPriceChange::create([
        //         'product_id' => $item->id,
        //         'price_type' => 'current',
        //         'price_old' => $oldPrice,
        //         'currency_old_id' => $oldPriceCurrency->id ?? null,
        //         'price_new' => $item->price_current,
        //         'currency_new_id' => $item->currentCurrency->id,
        //         'reason' => 'Changed from form'
        //     ]));
        // }


        if (($oldMsrp !== null && $oldMsrp != $item->price_msrp) ||
            ($oldMsrpCurrency !== null && $oldMsrpCurrency->id != $item->msrpCurrency->id)
        ) {
            $item->priceChanges()->save(ProductPriceChange::create([
                'product_id' => $item->id,
                'price_type' => 'msrp',
                'price_old' => $oldMsrp,
                'currency_old_id' => $oldMsrpCurrency->id ?? null,
                'price_new' => $item->price_msrp,
                'currency_new_id' => $item->msrpCurrency->id,
                'reason' => 'Changed from form'
            ]));
        }

        // set amazon price scrapper when first time product is being created
        if ($request->id == null && $request->asin != null) {
            try {
                $amazonAgent = \App\Models\Agent::select('id')->where('name', 'amazon')->first();
                if ($amazonAgent != null) {
                    $scrapper = new AmazonScrapper([$request->asin]);
                    $details = $scrapper->getItems();

                    if (!empty($details[0])) {
                        $detail = (object) $details[0];
                        $data = [
                            'product_id' => $item->id,
                            'agent_id' => $amazonAgent->id,
                            'current_msrp' => $detail->amount,
                            'original_msrp' => null,
                            'currency_id' => $detail->currency,
                            'url' => $detail->url
                        ];
                        if ($detail->savings != null) {
                            $data['original_msrp'] = $detail->amount + $detail->savings;
                        }
                        $item->prices()->save(ProductPrice::create($data));
                    }
                }
            } catch (\Exception $e) {
                //handle error
            }
        }


        //apply changes for children
        if (is_null($item->parent_id)) {

            if(!is_null($this->source_item_id)) {

              $children = Product::where('parent_id', $this->source_item_id)->get(); //get children

              if($children->isNotEmpty()) {

                  $parent_fields = $this->source_item->attributesToArray();
                  $modified_fields =  $this->dirty_fields->toArray();

                  foreach($children as $child) {

                      $child_fields = collect($child->attributesToArray());
                      $child_fields['backUrl'] = $request->backUrl;
                      $ar_exclude = ['id', 'name', 'submitter_email'];

                      $child_fields->transform(function($item, $key) use ($parent_fields, $ar_exclude, $modified_fields) {
                          if(!in_array($key, $ar_exclude)) {
                              if(array_key_exists($key, $modified_fields)) {
                                  if($parent_fields[$key] == $item) {
                                       //save modified parent fields to children
                                       $item = $modified_fields[$key] ?? $item;
                                  }
                              }
                          }
                          return $item;
                      });


                      $child_request = $this->source_request::capture(); //get parent request clone
                      $child_request->replace($child_fields->toArray()); //set new fields

                      if($this->source_request->draft) $child_request->merge(['draft' => true]); //add field


                      //only new deals inheritance for child
                      if(is_array($this->source_request->deal_prices)) {

                          $child_deals = $child->deal->toArray();

                          $child_deals_agents = $child->deal->map(function($item){
                              return $item['agent_id'];
                          })->toArray();

                          foreach ($this->source_request->deal_prices as $deal_val) {
                              if(!in_array($deal_val['agent_id'], $child_deals_agents)) {
                                  array_push($child_deals, $deal_val);
                              }
                          }

                          if(!empty($child_deals)) $child_request->merge(['deal_prices' => $child_deals]);
                      }

                      //only new prices inheritance for child
                      if(is_array($this->source_request->product_prices)) {

                          $child_prices = $child->prices->toArray();

                          $child_prices_agents = $child->prices->map(function($item){
                              return $item['agent_id'];
                          })->toArray();

                          foreach ($this->source_request->product_prices as $price_val) {
                              if(!in_array($price_val['agent_id'], $child_prices_agents)) {
                                  array_push($child_prices, $price_val);
                              }
                          }

                          if(!empty($child_prices)) $child_request->merge(['product_prices' => $child_prices]);
                      }

                      $this->current_variant = false;

                      $this->save($child_request); //save child
                  }
              }

            }
        }

        if($request->next_variant) { //go to selected variant

            $next_variant = ($request->next_variant == 'default') ? $item->parent_id : $request->next_variant;

            session()->flash('status', 'success');
            session()->flash('message', 'previous variant saved successfully');
            return redirect()->to('/admin/product?id='.$next_variant.'&backUrl='.urlencode($request->backUrl));

        }

        if ($request->id) { //existing item

            session()->flash('status', 'success');
            session()->flash('message', 'saved successfully');
            return back();

        }else{ //new item

            return redirect('/admin/product?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);
        }


    }

    public function export() {
        $exporter = new CSVExport(new Product);
        $exporter->merge(['images', 'similar_products', 'updatable_to_os', 'websites'])->export('products');
        return redirect()->back();
    }

    public function delete(Requests\DeleteRequest $request) {

        try { //remove products/variants

            $prods = Product::whereIn('id', $request->items)->with('variant')->get();

            Product::whereIn('id', $request->items)->delete();

            $prods = $prods->reject(function($item){
                return $item->variant_id = null;
            })->map(function($item){
                return $item->variant_id;
            });

            if ($prods->isNotEmpty()) ProductVariant::whereIn('id', $prods)->delete();

        } catch (\Illuminate\Database\QueryException $ex) {
            if ($request->ajax()) {
                return $request->session()->flash('message', 'Unable to delete. Selected items has records in product_price_change table.');
            }
            return back()->withErrors([
                'delete' => $ex->getMessage()
            ])->withInput();
        }
        if ($request->ajax()) {
            $request->session()->flash('status', 'success');
            return $request->session()->flash('message', 'Deleted successfully');
        }
        return redirect($request->backUrl)->with([
            'status' => 'success',
            'message' => 'Deleted successfully'
        ]);
    }

    public function getProduct(Request $request) {

        $request->merge([
            'product_id' => explode(',', $request->product_id),
            'retailers' => explode(',', $request->retailers),
            'variants' => explode(',', $request->variants)
        ]);
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|array',
            'product_id.*' => 'numeric',
            'retailers' => 'sometimes|nullable|array',
            'retailers.*' => 'numeric',
            'price_type' => 'nullable|string',
            'style' => 'nullable|string',
            'short_code' => 'sometimes|nullable',
            'site' => 'sometimes|nullable',
            'page_link' => 'sometimes|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'server' => $validator->getMessageBag()->toArray()
            ], 400); // 400 being the HTTP code for an invalid request.
        }

        // Save link tracking data
        if($request->short_code && $request->site && $request->page_link) {
            try {
                (new ShortcodeStatsController)->linkTrackingSaveData(new Request([
                    'short_code' => $request->short_code,
                    'site' => $request->site,
                    'page_link' => $request->page_link
                ]));
            } catch(\Exception $error) {
                Log::error($error->getMessage());
            }
        }

        $product_ids = $request->product_id;
        $variants = $request->variants ?? [];
        $style = $request->style;
        $price_type = $request->price_type ?? '';
        $agents = $request->retailers[0] !== '' ? $request->retailers :  [];
        $numberOfRecords = $style == 'large' ? 6 : 1; //6 for large widget 1 for other widgets

        //return response()->json($agents);

        if (!empty($variants)) {
            //get product variants
            $v = Product::whereIn('variant_id', $variants)->get();
            // get variants products parent_id
            $parents_ids = $v->map(function ($variant) {
                return $variant->parent_id;
            });

            // remove parent_id from product_ids
            $parents_ids->each(function ($id) use (&$product_ids) {

                if (($key = array_search($id, $product_ids)) !== false) {

                    unset($product_ids[$key]);
                }
            })->toArray();

            //dd($parents_ids->merge($product_ids));

            // get variants product.id
            $variant_ids = $v->map(function ($variant) {
                return $variant->id;
            })->toArray();

            // merge product_id and variants product.id
            $product_ids = array_merge($product_ids, $variant_ids);
        }

        try {
            $allData = [];

//             $products = Product::has('deal', '>=', 0)
//                 ->orWhereHas('deal', function(Builder $query) {
//                     $query->whereNull('expiry_date')
//                         ->orWhere('expiry_date', '>', now());
//                 })
//                 ->whereIn('id', $product_ids)
//                 ->get();

            $products = Product::whereIn('id', $product_ids)->get();


            foreach ($products as $product) {
                $reasonTOBuy = $product->reasons_to_buy != "" ? explode('|', $product->reasons_to_buy) : null;

                $data = [
                    'name' => $product->name,
                    'rating' => $product->rating ?? '',
                    'review_url' => $product->review_url,
                    'buyers_guide_url' => $product->buyers_guide_url,
                    'tagline' => $product->tagline,
                    'excerpt' => $product->excerpt,
                    'summary' => $product->summary_main,
                    'reason_to_buy' => $reasonTOBuy
                ];

                if ($product->images->count()) {
                    $data['image'] = $product->productFirstImage();
                }

                if ($price_type == '') {
                    $deal_prices = $this->getDealPrices($product->id, $agents,  $numberOfRecords);
                    if (count($deal_prices)) {
                        $data['prices'] = $deal_prices;
                    } else {
                        $product_prices = $this->getProductPrices($product->id, $agents,  $numberOfRecords);
                        $data['prices'] = $product_prices;
                    }
                } else {
                    if ($price_type == 'deal') {
                        $deal_prices = $this->getDealPrices($product->id, $agents,  $numberOfRecords);
                        $data['prices'] = $deal_prices;
                    } else if ($price_type == 'product') {
                        $product_prices = $this->getProductPrices($product->id, $agents,  $numberOfRecords);
                        $data['prices'] = $product_prices;
                    }
                }



                if (!isset($data['prices']) || empty($data['prices'])) {
                    $data['prices'][] = [
                        'agent_name' => $product->brand->name,
                        'price' => $product->price_current,
                        'original_price' => $product->price_msrp,
                        'currency' => $product->currentCurrency->symbol,
                        'url' => $product->product_url,
                    ];
                }

                $allData[] = $data;
            }

            return $allData;
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'server' => "File: {$e->getFile()}, Line: {$e->getLine()}",
            ]);
        }
    }

    public function getProductPrices(int $product_id, array $agents, int $numberOfRecords): array {
        // get all products
        $all_product_prices = ProductPrice::where('product_id', $product_id)
            ->when(count($agents), function ($query) use ($agents) {
                return $query->whereIn('agent_id', $agents);
            })
            ->orderBy('recommended', 'DESC')
            ->orderBy('current_msrp')
            ->get();

        // if total records is less than or equals to the needed numberOfRecords, return all.
        if ($all_product_prices->count() <= $numberOfRecords) {
            $product_prices = $all_product_prices;
        } else {

            //check if any recommended prices available
            if ($all_product_prices->where('recommended', true)->count()) {
                // get recommended prices
                $product_prices = $all_product_prices->filter(function ($deal) {
                    return $deal->recommended;
                })->sortBy('current_msrp')->slice(0, $numberOfRecords);

                //if above retrived records are less than the needed numberOfRecords
                if ($product_prices->count() < $numberOfRecords) {

                    // exlude the records that we already got
                    $notInIds = $product_prices->map(function ($deal) {
                        return $deal->id;
                    })->toArray();

                    // get more records cheapest first
                    $all_product_prices->whereNotIn('id', $notInIds)->sortBy('current_msrp')->slice(0, $numberOfRecords - $product_prices->count())->each(function ($deal) use ($product_prices) {
                        $product_prices->push($deal);
                    });
                }
            } else {
                //get records
                $product_prices = $all_product_prices->slice(0, $numberOfRecords);
            }
        }
        $data = [];
        foreach ($product_prices as $price) {

            $data[] = [
                'agent_name' => $price->agent->name,
                'price' => $price->current_msrp,
                'original_price' => $price->original_msrp,
                'currency' => $price->currency->symbol,
                'url' => $price->url,
            ];
        }


        return $data;
    }

    public function getDealPrices(int $product_id, array $agents, int $numberOfRecords): array {

        $all_deal_prices = ProductDealPrice::where('product_id', $product_id)
            ->where(function($query) {
                $query->whereNull('expiry_date')
                    ->orWhere('expiry_date', '>', now());
            })
            ->when(count($agents), function ($query) use ($agents) {
                return $query->whereIn('agent_id', $agents);
            })
            ->orderBy('recommended', 'DESC')
            ->orderBy('price')
            ->get();

        if ($all_deal_prices->count() <= $numberOfRecords) {
            $deal_prices = $all_deal_prices;
        } else {

            if ($all_deal_prices->where('recommended', true)->count()) {

                $deal_prices = $all_deal_prices->filter(function ($deal) {
                    return $deal->recommended || $deal->is_hot;
                })->sortBy('price')->slice(0, $numberOfRecords);

                if ($deal_prices->count() < $numberOfRecords) {
                    $notInIds = $deal_prices->map(function ($deal) {
                        return $deal->id;
                    })->toArray();

                    $all_deal_prices->whereNotIn('id', $notInIds)->sortBy('price')->slice(0, $numberOfRecords - $deal_prices->count())->each(function ($deal) use ($deal_prices) {
                        $deal_prices->push($deal);
                    });
                }
            } else {
                $deal_prices = $all_deal_prices->slice(0, $numberOfRecords);
            }
        }
        $data = [];
        foreach ($deal_prices as $price) {

            $data[] = [
                'agent_name' => $price->agent->name,
                'price' => $price->price,
                'original_price' => $price->original_price,
                'currency' => $price->currency->symbol,
                'coupon_code' => $price->coupon_code,
                'retailer_custom_text' => $price->retailer_custom_text,
                'is_hot' => (bool) ($price->is_hot ?? false),
                'is_free' => (bool) ($price->is_free ?? false),
                'url' => $price->url,
            ];
        }

        return $data;
    }

    public function getAssociateProductInfo(Product $product) {
        try {
            $data = [
                'name' => $product->name,
                'summary' => $product->summary_main,
                'pros' => $product->pros,
                'cons' => $product->cons,
                'rating' => $product->rating
            ];

            $retailers = [];

            foreach ($product->prices as $price) {
                $retailers[] = [
                    'agent_name' => $price->agent->name,
                    'price' => $price->current_msrp,
                    'original_price' => $price->original_msrp,
                    'currency' => $price->currency->symbol,
                    'url' => $price->url,
                ];
            }

            foreach ($product->deal as $price) {
                $retailers[] = [
                    'agent_name' => $price->agent->name,
                    'price' => $price->price,
                    'original_price' => $price->original_price,
                    'currency' => $price->currency->symbol,
                    'coupon_code' => $price->coupon_code ?? '',
                    'retailer_custom_text' => $price->retailer_custom_text ?? '',
                    'is_hot' => (bool) ($price->is_hot ?? false),
                    'is_free' => (bool) ($price->is_free ?? false),
                    'url' => $price->url,
                ];
            }

            $data['retailers'] = $retailers;

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => true, 'message' => $e->getMessage()]);
        }
    }
}
