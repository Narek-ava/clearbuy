<?php

namespace App\Services\Product;

use App\DataTransferObjects\Product\ProductFieldCompletionPercentageDTO;
use App\Exceptions\Product\ProductFields\UnknownFieldsFromException;
use App\Models\Product;
use App\Models\ProductAttributeValue;

class ProductFieldCompletionPercentageService
{
    private const FIELDS_FROM_CONSTANT = 1;
    private const FIELDS_FROM_RELATION = 2;

    public const SECTIONS = [
        'product_info' => [
            'color' => 'rgb(4,150,255)',
            'background_color' => 'rgb(4,150,255,0.3)',
            'fields_from' => self::FIELDS_FROM_CONSTANT,
            'fields' => [
                'name' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'sku' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'asin' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'model' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'model_family' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'category_id' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'brand_id' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'price_msrp' => [
                    'coefficient' => 0.5,
                    'handler' => 'handlerEmpty'
                ],
                'currency_msrp' => [
                    'coefficient' => 0.5,
                    'handler' => 'handlerEmpty'
                ],
                'released_with_os_id' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty',
                    'when' => [
                        'field' => 'category.os',
                        'handler' => 'handlerCount'
                    ],
                ],
                'size_length' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'size_width' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'size_height' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'weight' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'product_url' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'tagline' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'date_publish' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
            ],
        ],
        'editorial' => [
            'color' => 'rgb(252,47,0)',
            'background_color' => 'rgba(252,47,0,0.3)',
            'fields_from' => self::FIELDS_FROM_CONSTANT,
            'fields' => [
                'rating' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'excerpt' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'summary_main' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'reasons_to_buy' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'pros' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'cons' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'full_overview' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'similarProducts' => [
                    'coefficient' => 1,
                    'handler' => 'handlerCount'
                ],
                'websites' => [
                    'coefficient' => 1,
                    'handler' => 'handlerCount'
                ],
                'review_url' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'buyers_guide_url' => [
                    'coefficient' => 1,
                    'handler' => 'handlerEmpty'
                ],
                'badges' => [
                    'coefficient' => 1,
                    'handler' => 'handlerCount'
                ],
                'tags' => [
                    'coefficient' => 1,
                    'handler' => 'handlerCount'
                ],
            ],
        ],
        'specs' => [
            'color' => 'rgb(133,10,255)',
            'background_color' => 'rgba(133,10,255,0.3)',
            'fields_from' => self::FIELDS_FROM_RELATION,
            'relation_name' => 'specifications',
            'when' => [
                'field' => 'category.specifications',
                'handler' => 'handlerCount'
            ],
            'handler' => 'handlerAttributeFilled'
        ],        
        'affiliate' => [
            'color' => 'rgb(255,188,10)',
            'background_color' => 'rgb(255,188,10,0.3)',
            'fields_from' => self::FIELDS_FROM_CONSTANT,
            'fields' => [
                'prices' => [
                    'coefficient' => 1,
                    'handler' => 'handlerCount'
                ],
                'deal' => [
                    'coefficient' => 1,
                    'handler' => 'handlerCount'
                ],
            ],
        ],
    ];

    /**
     * @param Product $product
     * @return ProductFieldCompletionPercentageDTO[]
     * @throws UnknownFieldsFromException
     */
    public static function getFilledPercent(Product $product): array
    {
        $result = [];

        foreach (self::SECTIONS as $sectionName => $section) {
            $filledScore = 0;
            $totalScore = 0;
            if (empty($section['when']) || static::{$section['when']['handler']}($product, $section['when']['field'])) {
                switch ($section['fields_from']) {
                    case self::FIELDS_FROM_CONSTANT:
                        foreach ($section['fields'] as $fieldName => $field) {
                            if (
                                !empty($field['when'])
                                && !static::{$field['when']['handler']}($product, $field['when']['field'])
                            ) {
                                continue;
                            }

                            $totalScore += $field['coefficient'];
                            $filledScore += (int)static::{$field['handler']}($product, $fieldName) * $field['coefficient'];
                        }
                        break;
                    case self::FIELDS_FROM_RELATION :
                        foreach ($product->{$section['relation_name']} as $relationItem) {
                            $totalScore++;
                            $filledScore += (int)static::{$section['handler']}($relationItem);
                        }
                        break;
                    default :
                        throw new UnknownFieldsFromException("Unknown fields_from: {$section['fields_from']}");
                }

                $percent = (int)($totalScore ? floor($filledScore / $totalScore * 100) : 0);
            } else {
                $percent = 100;
            }

            $result[] = new ProductFieldCompletionPercentageDTO(
                $sectionName,
                $section['color'],
                $section['background_color'],
                $percent
            );
        }

        return $result;
    }

    /**
     * @param Product $product
     * @param string $fieldString
     * @return bool
     */
    private static function handlerCount(Product $product, string $fieldString): bool
    {
        $property = null;

        foreach (explode('.', $fieldString) as $field) {
            $property = $property ? $property->{$field} : $product->{$field};

            if (empty($property)) {
                return false;
            }
        }

        return $property->count() > 0;
    }

    /**
     * @param Product $product
     * @param string $fieldString
     * @return bool
     */
    private static function handlerEmpty(Product $product, string $fieldString): bool
    {
        $property = null;

        foreach (explode('.', $fieldString) as $field) {
            $property = $property ? $property->{$field} : $product->{$field};

            if (empty($property)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ProductAttributeValue $productAttributeValue
     * @return bool
     */
    private static function handlerAttributeFilled(ProductAttributeValue $productAttributeValue): bool
    {
        return !empty($productAttributeValue->attribute_option_id)
            || !empty($productAttributeValue->value_numeric)
            || !empty($productAttributeValue->value_text)
            || !empty($productAttributeValue->value_boolean)
            || !empty($productAttributeValue->value_date);
    }

}
