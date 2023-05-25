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

class AmazonScrapper
{    
    public $config;
    private $ids;
    public array $results = [];

    public function __construct(array $ids)
    {
        //product ASINs
        $this->ids = $ids;

        $this->config = new Configuration();

        /*
        * Add your credentials
        */

        # Please add your access key here
        $this->config->setAccessKey(config('amazon.access_key'));
        # Please add your secret key here
        $this->config->setSecretKey(config('amazon.secret_key'));

        /*
        * PAAPI host and region to which you want to send request
        * For more details refer:
        * https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
        */
        $this->config->setHost('webservices.amazon.com');
        $this->config->setRegion('us-east-1');
    }

    /**
     * Returns the array of items mapped to ASIN
     *
     * @param array $items Items value.
     * @return array of \Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\Item mapped to ASIN.
     */
    public function parseResponse($items)
    {
        $mappedResponse = [];
        foreach ($items as $item) {
            $mappedResponse[$item->getASIN()] = $item;
        }
        return $mappedResponse;
    }

    public function getItems()
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
            GetItemsResource::OFFERSLISTINGSPRICE,
        ];

        # Forming the request
        $getItemsRequest = new GetItemsRequest();
        $getItemsRequest->setItemIds($itemIds);
        $getItemsRequest->setPartnerTag(config('amazon.partner_tag'));
        $getItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
        $getItemsRequest->setResources($resources);

        # Validating request
        $invalidPropertyList = $getItemsRequest->listInvalidProperties();
        $length = count($invalidPropertyList);
        if ($length > 0) {
            $invalidPropList = '';
            foreach ($invalidPropertyList as $invalidProperty) {
                $invalidPropList .= $invalidProperty . PHP_EOL;
            }

            throw new Exception("Error forming the request  " . $invalidPropList);
        }

        # Sending the request
        // try {
        $getItemsResponse = $apiInstance->getItems($getItemsRequest);

        // echo 'API called successfully', PHP_EOL;
        // echo 'Complete Response: ', $getItemsResponse, PHP_EOL;

        # Parsing the response
        if ($getItemsResponse->getItemsResult() !== null) {
            //echo 'Printing all item information in ItemsResult:', PHP_EOL;
            if ($getItemsResponse->getItemsResult()->getItems() !== null) {
                $responseList = $this->parseResponse($getItemsResponse->getItemsResult()->getItems());
                foreach ($itemIds as $itemId) {
                    // echo 'Printing information about the itemId: ', $itemId, PHP_EOL;

                    if (isset($responseList[$itemId])) {
                        $item = $responseList[$itemId];
                        if (
                            $item->getOffers() !== null and
                            $item->getOffers()->getListings() !== null
                            and $item->getOffers()->getListings()[0]->getPrice() !== null
                            and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() !== null
                        ) {

                            // get currency_id of product
                            $currency = \App\Models\Currency::select('id')->where('name', $item->getOffers()->getListings()[0]->getPrice()->getCurrency())->first();

                            $this->results[] = [
                                'asin' => $itemId,
                                'amount' => $item->getOffers()->getListings()[0]->getPrice()->getAmount(),
                                'currency' => $currency->id,
                                'savings' => $item->getOffers()->getListings()[0]->getPrice()->getSavings() != null ? $item->getOffers()->getListings()[0]->getPrice()->getSavings()->getAmount() : null,
                                'url' => $item->getDetailPageURL()
                            ];
                        }
                    }
                }
            }
        }
        // if ($getItemsResponse->getErrors() !== null) {
        //     echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
        //     echo 'Error code: ', $getItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
        //     echo 'Error message: ', $getItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
        // }
        // } catch (ApiException $exception) {
        //     echo "Error calling PA-API 5.0!", PHP_EOL;
        //     echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
        //     echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        //     if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
        //         $errors = $exception->getResponseObject()->getErrors();
        //         foreach ($errors as $error) {
        //             echo "Error Type: ", $error->getCode(), PHP_EOL;
        //             echo "Error Message: ", $error->getMessage(), PHP_EOL;
        //         }
        //     } else {
        //         echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
        //     }
        // } catch (\Exception $exception) {
        //     echo "Error Message: ", $exception->getMessage(), PHP_EOL;
        // }

        // echo "Results: ", PHP_EOL;
        // print_r($this->results);

        return $this->results;
    }
}
