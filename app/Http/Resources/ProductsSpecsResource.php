<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Controllers\ProductController;

class ProductsSpecsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $parent_groups = [];
        $sorted_attributes = [];
        $attributes = $this->attributes; //there are no default repeatable attributes
        $attributes = $attributes->unique(); //for duplicate attributes like multiple

        if ($attributes->isNotEmpty()) {

            $featuredAttributes = $this->category->featuredAttributes;

            $ar_attributes = [];

            foreach ($attributes as $attr)
            {
                if (intval($attr->kind) == 1) { //only Specifications

                    $value = $attr->getProductValue($this->id);

                    if ($value instanceof \Illuminate\Support\Collection) {

                        //multiple attribute options transform to string
                        $value = $value->map(function($v){
                            return $v->map(function($option){
                                return $option->name;
                            })[0];
                        })->join(', ');
                    }

                    //single attribute option
                    if (is_object($value)) {
                        $value = $value->name;
                    }

                    //boolean
                    if(intval($attr->type) == 2) {
                        $value = $value ? 'Yes' : 'No';
                    }

                    $short_name = $attr->measure ? ' '.$attr->measure->short_name : '';

                    $ar_attributes[] = [
                        'name'  =>  $attr['name'],
                        'value' =>  $value.$short_name,
                        'is_featured'   =>  $featuredAttributes->contains('id', $attr['id']) ? true : false,
                        'sort_by_group' =>  $attr->group->sort_order,
                        'sort_by_order' =>  $attr['sort_order'],
                        'group_id'      =>  $attr->group->id,
                        'group_name'    =>  $attr->group->name,
                        'group_repeatable'    =>  $attr->group->repeatable,
                        'group_parent_id'     =>  $attr->group->parent_id,
                        'group_parent_name'   =>  $attr->group->where('id', $attr->group->parent_id)->value('name')
                    ];
                }
            }

            $sorted = collect($ar_attributes)->sortBy([
                ['sort_by_group', 'asc'],
                ['is_featured', 'desc'],
                ['sort_by_order', 'asc']
            ]);

            $sorted_attributes = $sorted->all();

            // foreach($sorted_attributes as $key => $item) {
            //     unset($sorted_attributes[$key]['sort_by_group']);
            //     unset($sorted_attributes[$key]['sort_by_order']);
            // }

            foreach ($sorted_attributes as $key => $attr) {
                if (is_null($attr['group_parent_id'])) { //non repeatable

                    $parent_groups[] = [
                        'id' => $attr['group_id'],
                        'name' => $attr['group_name'],
                        'sets' => []
                    ];

                } else { //repeatable

                    $parent_groups[] = [
                        'id' => $attr['group_parent_id'],
                        'name' => $attr['group_parent_name'],
                        'sets' => []
                    ];
                }
            }

            $parent_groups = array_unique($parent_groups, SORT_REGULAR);

            foreach ($sorted_attributes as $key => $attr) {
                foreach ($parent_groups as $pk => $pg) {

                    if (is_null($attr['group_parent_id'])) { //original attributes (usual groups)

                        if(!$attr['group_repeatable']) { //no need repeatable group attributes (prototypes)

                            if(intval($attr['group_id']) == intval($pg['id'])) {
                                $parent_groups[$pk]['sets'][] = [
                                    'id'   => $attr['group_id'],
                                    'name' => $attr['group_name'],
                                    'attributes' => []
                                ];
                            }
                        }

                    } else { //attributes from sets

                        if(intval($attr['group_parent_id']) == intval($pg['id'])) {
                            $parent_groups[$pk]['sets'][] = [
                                'id'   => $attr['group_id'],
                                'name' => $attr['group_name'],
                                'attributes' => []
                            ];
                        }
                    }

                }
            }

            foreach($parent_groups as $pk => &$pg) {
                $pg['sets'] = array_unique($pg['sets'], SORT_REGULAR);
            }

            foreach($sorted_attributes as $key => $attr) {
                foreach($parent_groups as $pk => &$pg) {

                    if(is_null($attr['group_parent_id'])) { //original attributes (usual groups)

                        if(intval($attr['group_id']) == intval($pg['id'])) {
                            foreach($pg['sets'] as &$set) {
                                if(intval($attr['group_id']) == intval($set['id'])) {
                                    array_push($set['attributes'], $attr);
                                }
                            }
                        }

                    }else{ //attributes from sets

                        if(intval($attr['group_parent_id']) == intval($pg['id'])) {
                            foreach($pg['sets'] as &$set) {
                                if(intval($attr['group_id']) == intval($set['id'])) {
                                    array_push($set['attributes'], $attr);
                                }
                            }
                        }
                    }

                }
            }

        }

        return [
            'id'        =>  (int) $this->id,
            'name'      =>  (string) $this->name,
            'groups'    =>  $parent_groups,
            'edit_url'  =>  action([ProductController::class, 'form'], ['id' => $this->id, 'backUrl' => '/admin/products'])
        ];
    }
}
