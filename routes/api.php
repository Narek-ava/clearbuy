<?php

use App\Http\Controllers\ProductController;  //remove after refactoring

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\ProductController as ApiProductController;
use App\Http\Controllers\API\DealController as ApiDealController;
use App\Http\Controllers\API\AppController as ApiAppController;
use App\Http\Controllers\API\WebsiteController as ApiWebsiteController;
use App\Http\Controllers\API\CategoryController as ApiCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->group(function () {
	Route::get('/user', function (Request $request) {
		return $request->user();
	});
});

//'auth.domain'
Route::middleware(['auth.domain'])->group(function () {

	Route::get('/associate-product/{product}', [ProductController::class, 'getAssociateProductInfo']); //need to refactor

});

/*
*	PRODUCTS
*/

//returns products with prices
Route::get('/product', [ApiProductController::class, 'getProduct']);

//returns parent products
Route::get('/products', [ApiProductController::class, 'getProducts']);

//returns list of retailers for multiple products depending on the price type
Route::get('/product/agents', [ApiProductController::class, 'getAgents']);

//returns list of variants for multiple products
Route::get('/product/variants', [ApiProductController::class, 'getVariants']);

//returns product info for “best” widget
Route::get('/product/best', [ApiProductController::class, 'getBestProduct']);

//returns information on multiple products for usage in best list.
Route::get('/product/best_list', [ApiProductController::class, 'getBestListProduct']);

//returns specifications of multiple products
Route::get('/product/specs', [ApiProductController::class, 'getProductSpecs']);

//update user rating
Route::post('/product/rating', [ApiProductController::class, 'updateProductRating']);

/*
*	DEALS
*/

//returns deals
Route::get('/deals', [ApiDealController::class, 'getDeals']);

/*
*	APPS
*/

//returns app for shortcode
Route::get('/app', [ApiAppController::class, 'getApp']);

//returns apps
Route::get('/apps', [ApiAppController::class, 'getApps']);

//returns apps stores
Route::get('/app/stores', [ApiAppController::class, 'getAppsStores']);

//returns all websites
Route::get('/websites', [ApiWebsiteController::class, 'get']);

//returns all categories
Route::get('/categories', [ApiCategoryController::class, 'get']);
