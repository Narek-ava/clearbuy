<?php

namespace App\Http\Resources;

use App\Http\Controllers\DealController;
use Illuminate\Http\Resources\Json\JsonResource;

class DealResource extends JsonResource
{
    private static $isHot;

    /**
     * Collection with params
     *
     * @param  mixed  $resource
     * @param bool $isHot
     * @return @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collectionWithParams($resource, $isHot)
    {
        self::$isHot = $isHot;

        return parent::collection($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $product = $this;
        
        if(self::$isHot) {
            // Hot deals + additional deals
            $deal = $product->hotDealWithPriority ?? $product->dealWithPriority;
        } else {
            // Only not hot deals 
            $deal = $product->dealWithPriority;
        }

        return [
            'id' => $deal->id,
            'title' => $product->name,
            'url' => $deal->url,
            'if_free' => $deal->if_free,
            'store' => $deal->agent->name,
            'added' => $this->added($deal->created_at_timestamp),
            'expires' => $this->getDealExpirationString($deal->expiry_date_timestamp),
            'expired' => $deal->expiry_date > now() ? false : true,
            'original_price' => max($deal->original_price, $deal->price),
            'current_price' => min(max($deal->original_price, $deal->price), $deal->price),
            'currency' => $deal->currency->symbol,
            'summary' => $product->summary_main,
            'coupon_code' => $deal->coupon_code,
            'retailer_custom_text' => $deal->retailer_custom_text,
            'image' => $product->productFirstImage(),
            'edit_url' => action([DealController::class, 'form'], ['id' => $deal->id, 'backUrl' => '/admin/deals'])
        ];
    }

    private function added(int $timestamp): string
    {
        $addedString = $this->getAddedString($timestamp);

        if(empty($addedString)) return '';

        return 'Added ' . $addedString;
    }

    private function getAddedString(int $timestamp): string
    {
        $timeDiff = now()->timestamp - $timestamp;

        if($timeDiff < 1) return '0 s';
        if($timeDiff > 24 * 60 * 60) return date('F j, Y', $timestamp);

        $types = [
            60 * 60 => 'hour',
            60 => 'minute',
            1 => 'second',
        ];

        foreach($types as $type => $ago) {
            $d = $timeDiff / $type;
            if($d >= 1) {
                $roundTime = round($d);
                if($roundTime == 1 ) {
                    return $roundTime . ' ' . $ago . ' ago';
                } else {
                    return $roundTime . ' ' . $ago . 's ago';
                }
            }
        }

        return '';
    }

    private function getDealExpirationString(int $expires): string
    {
        $day = 24 * 60 * 60;

        $now = now()->timestamp;
        if ($now > $expires)return 'Expired | ';

        $remaining = ceil(($expires - $now) / $day);

        if ($remaining <= 1) return $remaining . ' day remaining | ';
        if ($remaining <= 15) return $remaining . ' days remaining | ';

        return '';
    }
}
