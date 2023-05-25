<?php

namespace App\Http\Helpers\Scrapper;

use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;
use Amazon\ProductAdvertisingAPI\v1\Configuration;
use Exception;

class AmazonOriginScrapper
{
    protected object $config;
    private array $ids = [];
    private array $items = [];


    public function __construct(array $ids)
    {
        $this->ids = $ids; //product ASINs

        $this->config = new Configuration();
        $this->config->setAccessKey(config('amazon.access_key'));
        $this->config->setSecretKey(config('amazon.secret_key'));
        $this->config->partnerTag = config('amazon.partner_tag');
        $this->config->setHost(config('amazon.host'));
        $this->config->setRegion(config('amazon.region'));
    }

    /**
     * Returns the array of items mapped to ASIN
     *
     * @param array $items Items value.
     * @return array of \Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\Item mapped to ASIN.
     */
    function parseResponse($items)
    {
        $mappedResponse = [];
        foreach ($items as $item) {
            $mappedResponse[$item->getASIN()] = $item;
        }
        return $mappedResponse;
    }

    function getItems()
    {
        $apiInstance = new DefaultApi(
            /*
                * If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
                * This is optional, `GuzzleHttp\Client` will be used as default.
                */
            new \GuzzleHttp\Client(),
            $this->config
        );

        # Request initialization

        # Choose item id(s)
        $itemIds = $this->ids;

        /*
         * Choose resources you want from GetItemsResource enum
         * For more details, refer: https://webservices.amazon.com/paapi5/documentation/get-items.html#resources-parameter
         */
        $resources = [
            GetItemsResource::ITEM_INFOTITLE,
            GetItemsResource::OFFERSLISTINGSPRICE,
            //GetItemsResource::OFFERSSUMMARIESHIGHEST_PRICE,
            GetItemsResource::OFFERSLISTINGSSAVING_BASIS,
            GetItemsResource::ITEM_INFOCONTENT_INFO,
            GetItemsResource::ITEM_INFOPRODUCT_INFO,
            GetItemsResource::ITEM_INFOTECHNICAL_INFO,
            GetItemsResource::ITEM_INFOBY_LINE_INFO,
            GetItemsResource::ITEM_INFOTRADE_IN_INFO,
            GetItemsResource::ITEM_INFOMANUFACTURE_INFO,
            GetItemsResource::ITEM_INFOFEATURES,
            GetItemsResource::ITEM_INFOCLASSIFICATIONS,
            GetItemsResource::ITEM_INFOCONTENT_RATING,
            GetItemsResource::IMAGESPRIMARYLARGE,
            GetItemsResource::IMAGESVARIANTSLARGE,
        ];

        # Forming the request
        $getItemsRequest = new GetItemsRequest();
        $getItemsRequest->setItemIds($itemIds);
        $getItemsRequest->setPartnerTag($this->config->partnerTag);
        $getItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
        $getItemsRequest->setResources($resources);

        # Validating request
        $invalidPropertyList = $getItemsRequest->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            echo "Error forming the request", PHP_EOL;
            foreach ($invalidPropertyList as $invalidProperty) {
                echo $invalidProperty, PHP_EOL;
            }
            return;
        }

        # Sending the request
        try {
            $getItemsResponse = $apiInstance->getItems($getItemsRequest);

            //echo 'API called successfully', PHP_EOL;
            //echo 'Complete Response: ', $getItemsResponse, PHP_EOL;

            # Parsing the response
            if ($getItemsResponse->getItemsResult() !== null) {
                //echo 'Printing all item information in ItemsResult:', PHP_EOL;
                if ($getItemsResponse->getItemsResult()->getItems() !== null) {

                    $responseList = $this->parseResponse($getItemsResponse->getItemsResult()->getItems());

                    return $responseList;
                }
            }
            if ($getItemsResponse->getErrors() !== null) {

                return [
                    'code' => $getItemsResponse->getErrors()[0]->getCode(),
                    'message' => $getItemsResponse->getErrors()[0]->getMessage()
                ];

                // echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
                // echo 'Error code: ', $getItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
                // echo 'Error message: ', $getItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
            }
        } catch (ApiException $exception) {

            // echo "Error calling PA-API 5.0!", PHP_EOL;
            // echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
            // echo "Error Message: ", $exception->getMessage(), PHP_EOL;

            if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
                $errors = $exception->getResponseObject()->getErrors();
                foreach ($errors as $error) {

                    $code = $error->getCode();
                    $message = $error->getMessage();
                    // echo "Error Type: ", $error->getCode(), PHP_EOL;
                    // echo "Error Message: ", $error->getMessage(), PHP_EOL;
                }
            } else {

                $code = 400;
                $message = $exception->getResponseBody();
                //echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
            }

            return  [
                'code' => $code,
                'message' => $message
            ];

        } catch (Exception $exception) {

            return  [
                'code' => 400,
                'message' => $exception->getMessage()
            ];

            //echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        }
    }

    function getItemsWithHttpInfo()
    {
        $config = new Configuration();

        /*
         * Add your credentials
         */
        # Please add your access key here
        $config->setAccessKey('<YOUR ACCESS KEY>');
        # Please add your secret key here
        $config->setSecretKey('<YOUR SECRET KEY>');

        # Please add your partner tag (store/tracking id) here
        $partnerTag = '<YOUR PARTNER TAG>';

        /*
         * PAAPI host and region to which you want to send request
         * For more details refer:
         * https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
         */
        $config->setHost('webservices.amazon.com');
        $config->setRegion('us-east-1');

        $apiInstance = new DefaultApi(
            /*
             * If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
             * This is optional, `GuzzleHttp\Client` will be used as default.
             */
            new GuzzleHttp\Client(),
            $config
        );

        # Request initialization

        # Choose item id(s)
        $itemIds = ["059035342X", "B00X4WHP55", "1401263119"];

        /*
         * Choose resources you want from GetItemsResource enum
         * For more details, refer: https://webservices.amazon.com/paapi5/documentation/get-items.html#resources-parameter
         */
        $resources = [
            GetItemsResource::ITEM_INFOTITLE,
            GetItemsResource::OFFERSLISTINGSPRICE];

        # Forming the request
        $getItemsRequest = new GetItemsRequest();
        $getItemsRequest->setItemIds($itemIds);
        $getItemsRequest->setPartnerTag($partnerTag);
        $getItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
        $getItemsRequest->setResources($resources);

        # Validating request
        $invalidPropertyList = $getItemsRequest->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            echo "Error forming the request", PHP_EOL;
            foreach ($invalidPropertyList as $invalidProperty) {
                echo $invalidProperty, PHP_EOL;
            }
            return;
        }

        # Sending the request
        try {
            $responseWithHttpInfo = $apiInstance->getItemsWithHttpInfo($getItemsRequest);

            echo 'API called successfully', PHP_EOL;
            echo 'Complete Response dump: ';
            var_dump($responseWithHttpInfo);
            echo "HTTP Info: ";
            var_dump($responseWithHttpInfo[2]);

            # Parsing the response
            $response = $responseWithHttpInfo[0];
            if ($response->getItemsResult() !== null) {
                //echo 'Printing all item information in ItemResult:', PHP_EOL;
                if ($response->getItemsResult()->getItems() !== null) {
                    $responseList = $this->parseResponse($response->getItemsResult()->getItems());

                    foreach ($itemIds as $itemId) {
                        //echo 'Printing information about the itemId: ', $itemId, PHP_EOL;
                        $item = $responseList[$itemId];
                        if ($item !== null) {
                            // if ($item->getASIN()) {
                            //     echo 'ASIN: ', $item->getASIN(), PHP_EOL;
                            // }
                            // if ($item->getItemInfo() !== null and $item->getItemInfo()->getTitle() !== null
                            //     and $item->getItemInfo()->getTitle()->getDisplayValue() !== null) {
                            //     echo 'Title: ', $item->getItemInfo()->getTitle()->getDisplayValue(), PHP_EOL;
                            // }
                            // if ($item->getDetailPageURL() !== null) {
                            //     echo 'Detail Page URL: ', $item->getDetailPageURL(), PHP_EOL;
                            // }
                            // if ($item->getOffers() !== null and $item->getOffers()->getListings() !== null
                            //     and $item->getOffers()->getListings()[0]->getPrice() !== null
                            //     and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() !== null) {
                            //     echo 'Buying price: ', $item->getOffers()->getListings()[0]->getPrice()
                            //         ->getDisplayAmount(), PHP_EOL;
                            // }
                        } else {
                            echo "Item not found, check errors", PHP_EOL;
                        }
                    }
                }
            }
            if ($response->getErrors() !== null) {
                echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
                echo 'Error code: ', $response->getErrors()[0]->getCode(), PHP_EOL;
                echo 'Error message: ', $response->getErrors()[0]->getMessage(), PHP_EOL;
            }
        } catch (ApiException $exception) {
            echo "Error calling PA-API 5.0!", PHP_EOL;
            echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
            echo "Error Message: ", $exception->getMessage(), PHP_EOL;
            if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
                $errors = $exception->getResponseObject()->getErrors();
                foreach ($errors as $error) {
                    echo "Error Type: ", $error->getCode(), PHP_EOL;
                    echo "Error Message: ", $error->getMessage(), PHP_EOL;
                }
            } else {
                echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
            }
        } catch (Exception $exception) {
            echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        }
    }

    function getItemsAsync()
    {
        $config = new Configuration();

        /*
         * Add your credentials
         */
        # Please add your access key here
        $config->setAccessKey('<YOUR ACCESS KEY>');
        # Please add your secret key here
        $config->setSecretKey('<YOUR SECRET KEY>');

        # Please add your partner tag (store/tracking id) here
        $partnerTag = '<YOUR PARTNER TAG>';

        /*
         * PAAPI host and region to which you want to send request
         * For more details refer:
         * https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
         */
        $config->setHost('webservices.amazon.com');
        $config->setRegion('us-east-1');

        $apiInstance = new DefaultApi(
            /*
             * If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
             * This is optional, `GuzzleHttp\Client` will be used as default.
             */
            new GuzzleHttp\Client(),
            $config
        );

        # Request initialization

        # Choose item id(s)
        $itemIds = ["059035342X", "B00X4WHP55", "1401263119"];

        /*
         * Choose resources you want from GetItemsResource enum
         * For more details, refer: https://webservices.amazon.com/paapi5/documentation/get-items.html#resources-parameter
         */
        $resources = [
            GetItemsResource::ITEM_INFOTITLE,
            GetItemsResource::OFFERSLISTINGSPRICE];

        # Forming the request
        $getItemsRequest = new GetItemsRequest();
        $getItemsRequest->setItemIds($itemIds);
        $getItemsRequest->setPartnerTag($partnerTag);
        $getItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
        $getItemsRequest->setResources($resources);

        # Validating request
        $invalidPropertyList = $getItemsRequest->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            echo "Error forming the request", PHP_EOL;
            foreach ($invalidPropertyList as $invalidProperty) {
                echo $invalidProperty, PHP_EOL;
            }
            return;
        }

        # Sending the request
        try {
            $promise = $apiInstance->getItemsAsync($getItemsRequest);
            $response = $promise->wait();
            $promise->then(
                function ($response) {
                    return $response;
                },
                function (\Exception $exception) {
                    echo "Error Message: ", $exception->getMessage(), PHP_EOL;
                    throw $exception;
                }
            );

            echo 'API called successfully', PHP_EOL;
            echo 'Complete Response: ', $response, PHP_EOL;

            # Parsing the response
            if ($response->getItemsResult() !== null) {
                //echo 'Printing all item information in ItemResult:', PHP_EOL;
                if ($response->getItemsResult()->getItems() !== null) {
                    $responseList = $this->parseResponse($response->getItemsResult()->getItems());

                    foreach ($itemIds as $itemId) {
                        //echo 'Printing information about the itemId: ', $itemId, PHP_EOL;
                        $item = $responseList[$itemId];
                        if ($item !== null) {
                            if ($item->getASIN()) {
                                echo 'ASIN: ', $item->getASIN(), PHP_EOL;
                            }
                            if ($item->getItemInfo() !== null and $item->getItemInfo()->getTitle() !== null
                                and $item->getItemInfo()->getTitle()->getDisplayValue() !== null) {
                                echo 'Title: ', $item->getItemInfo()->getTitle()->getDisplayValue(), PHP_EOL;
                            }
                            if ($item->getDetailPageURL() !== null) {
                                echo 'Detail Page URL: ', $item->getDetailPageURL(), PHP_EOL;
                            }
                            if ($item->getOffers() !== null and $item->getOffers()->getListings() !== null
                                and $item->getOffers()->getListings()[0]->getPrice() !== null
                                and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() !== null) {
                                echo 'Buying price: ', $item->getOffers()->getListings()[0]->getPrice()
                                    ->getDisplayAmount(), PHP_EOL;
                            }
                        } else {
                            echo "Item not found, check errors", PHP_EOL;
                        }
                    }
                }
            }
            if ($response->getErrors() !== null) {
                echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
                echo 'Error code: ', $response->getErrors()[0]->getCode(), PHP_EOL;
                echo 'Error message: ', $response->getErrors()[0]->getMessage(), PHP_EOL;
            }
        } catch (ApiException $exception) {
            echo "Error calling PA-API 5.0!", PHP_EOL;
            echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
            echo "Error Message: ", $exception->getMessage(), PHP_EOL;
            if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
                $errors = $exception->getResponseObject()->getErrors();
                foreach ($errors as $error) {
                    echo "Error Type: ", $error->getCode(), PHP_EOL;
                    echo "Error Message: ", $error->getMessage(), PHP_EOL;
                }
            } else {
                echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
            }
        } catch (Exception $exception) {
            echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        }
    }

    function getItemsAsyncWithHttpInfo()
    {
        $config = new Configuration();

        /*
         * Add your credentials
         */
        # Please add your access key here
        $config->setAccessKey('<YOUR ACCESS KEY>');
        # Please add your secret key here
        $config->setSecretKey('<YOUR SECRET KEY>');

        # Please add your partner tag (store/tracking id) here
        $partnerTag = '<YOUR PARTNER TAG>';

        /*
         * PAAPI host and region to which you want to send request
         * For more details refer:
         * https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
         */
        $config->setHost('webservices.amazon.com');
        $config->setRegion('us-east-1');

        $apiInstance = new DefaultApi(
            /*
             * If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
             * This is optional, `GuzzleHttp\Client` will be used as default.
             */
            new GuzzleHttp\Client(),
            $config
        );

        # Request initialization

        # Choose item id(s)
        $itemIds = ["059035342X", "B00X4WHP55", "1401263119"];

        /*
         * Choose resources you want from GetItemsResource enum
         * For more details, refer: https://webservices.amazon.com/paapi5/documentation/get-items.html#resources-parameter
         */
        $resources = [
            GetItemsResource::ITEM_INFOTITLE,
            GetItemsResource::OFFERSLISTINGSPRICE];

        # Forming the request
        $getItemsRequest = new GetItemsRequest();
        $getItemsRequest->setItemIds($itemIds);
        $getItemsRequest->setPartnerTag($partnerTag);
        $getItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
        $getItemsRequest->setResources($resources);

        # Validating request
        $invalidPropertyList = $getItemsRequest->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            echo "Error forming the request", PHP_EOL;
            foreach ($invalidPropertyList as $invalidProperty) {
                echo $invalidProperty, PHP_EOL;
            }
            return;
        }

        # Sending the request
        try {
            $promise = $apiInstance->getItemsAsyncWithHttpInfo($getItemsRequest);
            $responseWithHttpInfo = $promise->wait();
            $promise->then(
                function ($response) {
                    return $response;
                },
                function (\Exception $exception) {
                    echo "Error Message: ", $exception->getMessage(), PHP_EOL;
                    throw $exception;
                }
            );

            echo 'API called successfully', PHP_EOL;
            echo 'Complete Response dump: ';
            var_dump($responseWithHttpInfo);
            echo "HTTP Info: ";
            var_dump($responseWithHttpInfo[2]);

            # Parsing the response
            $response = $responseWithHttpInfo[0];
            if ($response->getItemsResult() !== null) {
                //echo 'Printing all item information in ItemResult:', PHP_EOL;
                if ($response->getItemsResult()->getItems() !== null) {
                    $responseList = $this->parseResponse($response->getItemsResult()->getItems());

                    foreach ($itemIds as $itemId) {
                        //echo 'Printing information about the itemId: ', $itemId, PHP_EOL;
                        $item = $responseList[$itemId];
                        if ($item !== null) {
                            // if ($item->getASIN()) {
                            //     echo 'ASIN: ', $item->getASIN(), PHP_EOL;
                            // }
                            // if ($item->getItemInfo() !== null and $item->getItemInfo()->getTitle() !== null
                            //     and $item->getItemInfo()->getTitle()->getDisplayValue() !== null) {
                            //     echo 'Title: ', $item->getItemInfo()->getTitle()->getDisplayValue(), PHP_EOL;
                            // }
                            // if ($item->getDetailPageURL() !== null) {
                            //     echo 'Detail Page URL: ', $item->getDetailPageURL(), PHP_EOL;
                            // }
                            // if ($item->getOffers() !== null and $item->getOffers()->getListings() !== null
                            //     and $item->getOffers()->getListings()[0]->getPrice() !== null
                            //     and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() !== null) {
                            //     echo 'Buying price: ', $item->getOffers()->getListings()[0]->getPrice()
                            //         ->getDisplayAmount(), PHP_EOL;
                            // }
                        } else {
                            echo "Item not found, check errors", PHP_EOL;
                        }
                    }
                }
            }
            if ($response->getErrors() !== null) {
                echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
                echo 'Error code: ', $response->getErrors()[0]->getCode(), PHP_EOL;
                echo 'Error message: ', $response->getErrors()[0]->getMessage(), PHP_EOL;
            }
        } catch (ApiException $exception) {
            echo "Error calling PA-API 5.0!", PHP_EOL;
            echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
            echo "Error Message: ", $exception->getMessage(), PHP_EOL;
            if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
                $errors = $exception->getResponseObject()->getErrors();
                foreach ($errors as $error) {
                    echo "Error Type: ", $error->getCode(), PHP_EOL;
                    echo "Error Message: ", $error->getMessage(), PHP_EOL;
                }
            } else {
                echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
            }
        } catch (Exception $exception) {
            echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        }
    }

    /*
    *   return shorthand array
    */

    function getItemsInfoReduce() : array
    {
        $arShort = [];
        $responseList = $this->getItems();

        foreach ($this->ids as $itemId) {

            $item = $responseList[$itemId];

            if ($item !== null) {

                $infoResource = $item->getItemInfo(); //to reduce

                if(!empty($infoResource))
                {
                    $title = $infoResource->getTitle() ? $infoResource->getTitle()->getDisplayValue() : '';
                    $brand = ($infoResource->getByLineInfo() && $infoResource->getByLineInfo()->getBrand()) ? $infoResource->getByLineInfo()->getBrand()->getDisplayValue() : '';
                    $model = ($infoResource->getManufactureInfo() && $infoResource->getManufactureInfo()->getModel()) ? $infoResource->getManufactureInfo()->getModel()->getDisplayValue() : '';

                    $product_info = $infoResource->getProductInfo();

                    if(!empty($product_info))
                    {
                        $height = (!empty($product_info->getItemDimensions()) && $product_info->getItemDimensions()->getHeight()) ? $product_info->getItemDimensions()->getHeight()->getDisplayValue() : 0;
                        $height_unit = (!empty($product_info->getItemDimensions()) && $product_info->getItemDimensions()->getHeight()) ? $product_info->getItemDimensions()->getHeight()->getUnit() : '';

                        $length = (!empty($product_info->getItemDimensions()) && $product_info->getItemDimensions()->getLength()) ? $product_info->getItemDimensions()->getLength()->getDisplayValue() : 0;
                        $length_unit = (!empty($product_info->getItemDimensions()) && $product_info->getItemDimensions()->getLength()) ? $product_info->getItemDimensions()->getLength()->getUnit() : '';

                        $weight = (!empty($product_info->getItemDimensions()) && $product_info->getItemDimensions()->getWeight()) ? $product_info->getItemDimensions()->getWeight()->getDisplayValue() : 0;
                        $weight_unit = (!empty($product_info->getItemDimensions()) && $product_info->getItemDimensions()->getWeight()) ? $product_info->getItemDimensions()->getWeight()->getUnit() : '';

                        $width = (!empty($product_info->getItemDimensions()) && $product_info->getItemDimensions()->getWidth()) ? $product_info->getItemDimensions()->getWidth()->getDisplayValue() : 0;
                        $width_unit = (!empty($product_info->getItemDimensions()) && $product_info->getItemDimensions()->getWidth()) ? $product_info->getItemDimensions()->getWidth()->getUnit() : '';
                    }

                    $feature = $infoResource->getFeatures() ? $infoResource->getFeatures()->getDisplayValues() : [];
                    $feature_string = '';

                    //for better markup in UI
                    if(!empty($feature))
                    {
                        foreach($feature as $f) $feature_string .= $f.PHP_EOL;
                    }
                }

                if ($item->getOffers() !== null and
                    $item->getOffers()->getListings() !== null
                    and $item->getOffers()->getListings()[0]->getPrice() !== null
                    and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() !== null) {

                        //Base price
                        $price = ($item->getOffers() && $item->getOffers()->getListings()) ? $item->getOffers()->getListings()[0]->getPrice()->getAmount() : null;
                        $currency = ($item->getOffers() && $item->getOffers()->getListings()) ? $item->getOffers()->getListings()[0]->getPrice()->getCurrency() : null;

                        //Listing price (MSRP)
                        $msrp = ($item->getOffers() && $item->getOffers()->getListings()[0]->getSavingBasis()) ? $item->getOffers()->getListings()[0]->getSavingBasis()->getAmount() : null;
                        $msrp_currency = ($item->getOffers() && $item->getOffers()->getListings()[0]->getSavingBasis() ) ? $item->getOffers()->getListings()[0]->getSavingBasis()->getCurrency() : null;

                        $savings = $item->getOffers()->getListings()[0]->getPrice()->getSavings() != null ? $item->getOffers()->getListings()[0]->getPrice()->getSavings()->getAmount() : null;
                }

                if ($item->getDetailPageURL() !== null) {
                    $url = $item->getDetailPageURL();
                }

                //get country shortname from config/amazon.php
                $country_name = config('amazon.country');

                array_push($arShort, [

                    'asin' => $itemId,
                    'title' =>  $title ?? '',
                    'brand' =>  $brand ?? '',
                    'model' =>  $model ?? '',
                    'height' =>  $height ?? 0,
                    'height_unit' =>  $height_unit ?? '',
                    'length' =>  $length ?? 0,
                    'length_unit' =>  $length_unit ?? '',
                    'weight' =>  $weight ?? 0,
                    'weight_unit' =>  $weight_unit ?? '',
                    'width' =>  $width ?? 0,
                    'width_unit' =>  $width_unit ?? '',
                    'feature_string' =>  $feature_string ?? '',
                    'price' =>  $price ?? 0,
                    'currency' =>  $currency ?? null,
                    'msrp' =>  $msrp ?? 0,
                    'msrp_currency' =>  $msrp_currency ?? null,
                    'url'  => $url ?? '',
                    'country_name'  => $country_name ?? '',
                    'savings' => $savings ?? '',
                    'primaryImg' => $item->getImages()->getPrimary() ? $item->getImages()->getPrimary()->getLarge()->getUrl() : '',
                    'variants' => $item->getImages()->getVariants() ? $item->getImages()->getVariants() : [],
                ]);

            }
        }

        return $arShort;
    }


}
