<?php

namespace App\Services;

use App\Models\User;
use App\Jobs\ZapierJob;
use App\Models\Product;
use App\Models\ProductDealPrice;
use App\Http\Resources\ZapierDealResource;
use App\Http\Resources\ZapierProductResource;

class ZapierService
{
    /**
     * Product cteated
     *
     * @param integer $id
     * @return void
     */
    public static function productCreated(int $id)
    {
        ZapierJob::dispatchAfterResponse(auth()->user(), $id, Product::class, ZapierProductResource::class, config('zapier.product_created'));
    }

    /**
     * Product updated
     *
     * @param integer $id
     * @return void
     */
    public static function productUpdated(int $id)
    {
        ZapierJob::dispatchAfterResponse(auth()->user(), $id, Product::class, ZapierProductResource::class, config('zapier.product_updated'));
    }

    /**
     * Product requested
     *
     * @param integer $id
     * @return void
     */
    public static function productRequested(int $id)
    {
        ZapierJob::dispatchAfterResponse(auth()->user(), $id, Product::class, ZapierProductResource::class, config('zapier.product_requested'));
    }

    /**
     * Product moves from pending to normal
     *
     * @param integer $id
     * @return void
     */
    public static function productMovesFromPendingToNormal(int $id)
    {
        ZapierJob::dispatchAfterResponse(auth()->user(), $id, Product::class, ZapierProductResource::class, config('zapier.product_moves_from_pending_to_normal'));
    }

     /**
     * Deal cteated
     *
     * @param integer $id
     * @return void
     */
    public static function dealCreated(int $id)
    {
        ZapierJob::dispatchAfterResponse(auth()->user(), $id, ProductDealPrice::class, ZapierDealResource::class, config('zapier.deal_created'));
    }

    /**
     * Deal updated
     *
     * @param integer $id
     * @return void
     */
    public static function dealUpdated(int $id)
    {
        ZapierJob::dispatchAfterResponse(auth()->user(), $id, ProductDealPrice::class, ZapierDealResource::class, config('zapier.deal_updated'));
    }

     /**
     * Deal expires
     *
     * @param integer $id
     * @return void
     */
    public static function dealExpires(int $id)
    {
        $user = User::where('name', 'superadmin')->first();
        ZapierJob::dispatchAfterResponse($user, $id, ProductDealPrice::class, ZapierDealResource::class, config('zapier.deal_expires'));
    }
}
