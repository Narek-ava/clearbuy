<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ZapierProductResource extends JsonResource
{
    private User $user;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  User  $user
     * @return void
     */
    public function __construct($resource, $user)
    {
        parent::__construct($resource);
        $this->user = $user;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {     
        $userValuesArr = [
            'initiated_by_user_id' => $this->user->id,
            'initiated_by_user_name' => $this->user->name,
            'initiated_by_user_email' => $this->user->email
        ];

        return array_merge($userValuesArr, $this->resource->toArray());
    }
}
