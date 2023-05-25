<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Models\ProductDealPrice;
use App\Http\Controllers\Controller;
use App\Http\Resources\DealsResource;
use App\Http\Requests\API\DealsRequest;
use Illuminate\Database\Eloquent\Builder;

class DealController extends Controller
{
    public function getDeals(DealsRequest $request) {
        $hotQuantity = 8;

        $hot = Product::with(['hotDealWithPriority.agent', 'hotDealWithPriority.currency'])
            ->has('hotDealWithPriority')
            ->orderBy(
                ProductDealPrice::take(1)
                    ->select('created_at')
                    ->where('is_hot', true)
                    ->where('expiry_date', '>', now())
                    ->orderBy('created_at', 'desc')
                    ->orderBy('recommended', 'desc')
                    ->orderBy('price')
                    ->whereColumn('deal_prices.product_id', 'product.id'), 'desc'
            )
            ->take($hotQuantity);

        if($request->websites) {
            $hot->whereHas('websites', function (Builder $query) use($request) {
                $query->whereIn('name', $request->websites);
            });
        }

        if($request->categories) {
            $hot->whereIn('category_id', $request->categories);
        }

        $hot = $hot->get();

        $hotCount = $hot->count();
        $skippedIds = $hot->pluck('id');

        if($hotCount < $hotQuantity) {
            $additionalHot = Product::with(['dealWithPriority.agent', 'dealWithPriority.currency'])
                ->has('dealWithPriority')
                ->whereNotIn('id', $skippedIds)
                ->orderBy(
                    ProductDealPrice::take(1)
                        ->select('created_at')
                        ->where('expiry_date', '>', now())
                        ->orderBy('created_at', 'desc')
			            ->orderBy('recommended', 'desc')
			            ->orderBy('price')
                        ->whereColumn('deal_prices.product_id', 'product.id'), 'desc'
                )
                ->take($hotQuantity - $hotCount);

            if($request->websites) {
                $additionalHot->whereHas('websites', function (Builder $query) use($request) {
                    $query->whereIn('name', $request->websites);
                });
            }

            if($request->categories) {
                $additionalHot->whereIn('category_id', $request->categories);
            }

            $additionalHot = $additionalHot->get();

            $hot = $hot->merge($additionalHot);
            $skippedIds = $hot->pluck('id');
        }

        $posts = Product::with(['dealWithPriority.agent', 'dealWithPriority.currency'])
            ->has('dealWithPriority')
            ->whereNotIn('id', $skippedIds)
            ->orderBy(
                ProductDealPrice::take(1)
                    ->select('created_at')
                    ->where('expiry_date', '>', now())
                    ->orderBy('created_at', 'desc')
                    ->orderBy('recommended', 'desc')
                    ->orderBy('price')
                    ->whereColumn('deal_prices.product_id', 'product.id'), 'desc'
            );

        if($request->websites) {
            $posts->whereHas('websites', function (Builder $query) use($request) {
                $query->whereIn('name', $request->websites);
            });
        }

        if($request->categories) {
            $posts->whereIn('category_id', $request->categories);
        }

        $posts = $posts->paginate($request->posts_per_page);

        $data['hot'] = $hot;
        $data['posts'] = $posts->getCollection();
        $data['total_posts'] = $posts->total();
        $data['total_pages'] = $posts->lastPage();
        $data['current_page'] = $request->page;
        $data['posts_per_page'] = $request->posts_per_page;

        return response()->json(new DealsResource($data));
    }
}
