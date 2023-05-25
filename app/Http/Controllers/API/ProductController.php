<?php

namespace App\Http\Controllers\API;

use App\Models\Rating;
use App\Models\Product;
use App\Models\RatingProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\ProductRequest;
use App\Http\Requests\API\ProductsRequest;
use App\Http\Requests\API\RatingRequest;
use App\Http\Resources as Resources;

class ProductController extends Controller
{
    /*
    *   All methods returns \Illuminate\Http\Response
    */


    /**
     * Returns products with prices
     */
    public function getProduct(ProductRequest $request) {
        $products = Product::whereIn('id', $request->product_ids)->has('brand')->get();

        if(!empty($request->variants_ids)) { //get variants
            $products = $products->merge(Product::whereIn('variant_id', $request->variants_ids)->has('brand')->get());

        }

        return response()->json(Resources\ProductResource::collection($products));
    }

    /**
     * Returns parent products
     */
    public function getProducts() {

        $products = Product::select('id', 'name')
            ->whereNotNull('date_publish')
            ->whereNull('parent_id')
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json(Resources\ProductsResource::collection($products));
    }

    /**
     * Returns list of retailers for multiple products depending on the price type.
     */
    public function getAgents(ProductsRequest $request) {

        $products = Product::whereIn('id', $request->ids)->get();

        return response()->json(Resources\ProductsRetailersResource::collection($products));
    }

    /**
     * Returns list of variants for multiple products
     */
    public function getVariants(ProductsRequest $request) {

        $products = Product::select('id', 'name')
            ->whereNull('parent_id')
            ->whereIn('id', $request->ids)
            ->get();

        return response()->json(Resources\ProductsVariantsResource::collection($products));
    }

    /**
     * Returns product info for “best” widget
     */
    public function getBestProduct(ProductsRequest $request) {

        $products = Product::whereIn('id', $request->ids)->get();

        return response()->json(Resources\ProductsBestResource::collection($products));
    }

    /**
     * Returns information on multiple products for usage in best list.
     */
    public function getBestListProduct(ProductsRequest $request) {

        $products = Product::whereIn('id', $request->ids)->get();

        return response()->json(Resources\ProductsBestListResource::collection($products));
    }

    /**
     * Returns specifications of multiple products
     */
    public function getProductSpecs(ProductsRequest $request) {

        $products = Product::whereIn('id', $request->ids)->with('attributes')->get();

        return response()->json(Resources\ProductsSpecsResource::collection($products));
    }

    public function updateProductRating(RatingRequest $request)
    {
        $rating = Rating::where('name', $request->rating_name)->first();

        if(!$rating) return response('Rating not found', 400);

        $entry = RatingProduct::where('product_id', $request->product_id)->where('rating_id', $rating->id)->first();

        if(!$entry) return response("User's rating not found", 400);
    
        $entry->update([
            'user_rating' => $entry->user_rating * ($entry->votes / ($entry->votes + 1)) + $request->rating_value / ($entry->votes + 1),
            'votes' => $entry->votes + 1,
        ]);

        return response(true);
    }
}
