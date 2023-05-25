<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductPrice;
use App\Models\User;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

use App\Http\Helpers\Scrapper\AmazonScrapper;
use App\Http\Requests\ProductRequest as Requests;
use App\Mail\ProductRequestMail;
use App\Services\ZapierService;
use Image;

class ProductRequestController extends BaseItemController
{
    protected $baseUrl = '/admin/product_request';

    private const URGENCY = [
        ['key'=>'', 'value'=> 'How fast do you need this product added'],
        ['key'=> 1, 'value' => '1 hour'],
        ['key'=> 2, 'value' => '3 hours'],
        ['key'=> 3, 'value' => '12 hours'],
        ['key'=> 4, 'value' => '1 day'],
        ['key'=> 5, 'value' => '2 days'],
    ];

    public function list(Requests\ListRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        return view('product_request.list', ['product'=>$product]);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Product::find($request->id);

        $formData['brands'] = Brand::all();
        $formData['currencies'] = Currency::all();
        $formData['agents'] = Agent::all();
        $formData['urgency_options'] = collect(self::URGENCY);

        return view('product_request.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    { 
        $item = Product::firstOrNew(['id' => $request->id]);

        //general
        $item->name = $request->name;
        $item->reasons_to_buy = $request->reasons_to_buy;
        $item->excerpt = $request->excerpt;
        $item->summary_main = $request->summary_main;
        $item->asin = $request->asin;
        $item->price_msrp = $request->price_msrp;
        $item->notes = $request->notes;

        $user = auth()->user();
        $user->product_request_mailing = 1; //set for mailing
        $user->save();

        $item->submitter_id = $user->id;

        if($request->urgency !== null && !in_array( $request->urgency, array_column( self::URGENCY, 'key' ) )){
            return back()->withErrors([
                'urgency' => 'Invalid urgency option'
            ])->withInput();
        }
        if($request->urgency !== null){
            $urgency = implode('',array_map(function($arr) use($request) { if($arr['key'] == $request->urgency) return $arr['value']; }, self::URGENCY));
            $inHour = strpos($urgency, 'Hours');
            $duration = (int) preg_replace('/Hour|Hours|Day|Days/','', $urgency);
            $item->urgency = $inHour ? \Carbon\Carbon::now()->addHours($duration)->timestamp : \Carbon\Carbon::now()->addDays($duration)->timestamp;
        }


        //relations
        try {
            $currency = Currency::findOrFail($request->currency_msrp);
            $item->msrpCurrency()->associate($currency);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return back()->withErrors([
                'currency_msrp' => 'Selected currency does not exist'
            ])->withInput();
        }

        if($request->brand) {
            try {
                $brand = Brand::findOrFail($request->brand);
                $item->brand()->associate($brand);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
                return back()->withErrors([
                    'brand' => 'Selected brand does not exist'
                ])->withInput();
            }
        }

        $item->save();

        ZapierService::productRequested($item->id);

        if (isset($request->price_tracking)) {
            // set amazon price scrappe when first time product is being created
            if ($request->asin != null) {
                try {
                    $amazonAgent = Agent::select('id')->where('name', 'amazon')->first();
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
                        }else{
                            return redirect($request->backUrl)->withErrors([
                                'status' => 'error',
                                'message' => 'Price tracking failed for this product'
                            ])->withInput();
                        }
                    }
                } catch (\Exception $e) {
                    return redirect($request->backUrl)->withErrors([
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ])->withInput();
                }
            }
        } else {
            // retailer product prices
            $data = [
                'agent_id' => $request->agent_id,
                'product_id' => $item->id,
                'currency_id' => $request->currency_id,
                'current_msrp' => null,
                'original_msrp' => $request->original_msrp,
                'url' => $request->url ?? null,
                'recommended' => false
            ];

            if(!is_null($request->price_msrp)) {
                $item->prices()->save(ProductPrice::create($data));
            }
        }

        //images

        if ($request->product_image) {

            $file = $request->file('product_image');

            $name = $file->getClientOriginalName();
            //$name = $file->hashName();
            $as = $request->asin ? $request->asin.'/' : '';

            $img = Image::make($file); //make image for resize


            if($img->width() > 1920 || $img->height() > 1080)
            {
                $filePath = storage_path('app/images/');

                $img->resize(1920, 1080, function ($const) {
                   $const->aspectRatio();
                })->save($filePath.'/'.$name, 75); //save temporary file

                $contents = file_get_contents($filePath.'/'.$name);
                $path = Storage::disk('do_image_spaces')->put('products'.$as.$name, $contents, 'public');
                unlink($filePath.'/'.$name); //remove temporary file

            }else $path = $file->storePubliclyAs('products'.$as, $name, 'do_image_spaces');


            $item->images()->save(new ProductImage(['path' => $path, 'order' => 0]));
        }


        /*
        *   Logging and mailing
        */

        $brand_text = isset($brand) ? 'Brand: '.$brand->name.PHP_EOL : '';
        $log_message = '(ID '.$item->id.') '.$item->name.' created.'.PHP_EOL.$brand_text;

        $mail_data = [
            'id'      => $item->id,
            'name'    => $item->name,
            'brand'   => isset($brand) ? $brand->name : 'Not filled in'
        ];

        if(!is_null($item->urgency)) {
            $urgency = $item->urgency->toDateString();
            $log_message .= 'Urgency: '.$urgency.PHP_EOL;
            $mail_data['urgency'] = $urgency;
        }

        if(!is_null($item->notes)) {
            $log_message .= 'Notes: '.$item->notes.PHP_EOL;
            $mail_data['notes'] = $item->notes;
        }

        $url = $_SERVER['HTTP_HOST'].'/admin/product?id='.$item->id;
        $log_message .= 'Product url: '.$url;
        $mail_data['url'] = $url;

        Log::channel('product_update_slack')->info($log_message);

        try{

            $product_request_mailing = User::where('product_request_mailing', 1)->get();

            $recipients = collect(['rhys@drewl.com', 'tayo@drewl.com']); //default recipients

            if($product_request_mailing->isNotEmpty())
            {
                foreach($product_request_mailing as $u) {
                    $recipients->push($u->email);
                }
            }

            foreach ($recipients as $recipient) {
                Mail::to($recipient)->send(new ProductRequestMail($mail_data));
            }

            return redirect("/admin/$item->id/request_success")->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }catch(\Exception $e){

            return redirect("/admin/$item->id/request_success")->with([
                'status' => 'success',
                'message' => 'product saved but email not sended: '. $e->getMessage()
            ]);
        }

    }
}
