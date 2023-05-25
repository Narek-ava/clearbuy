<?php

use App\Http\Controllers as Controllers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
	return view('welcome');
});

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');

Route::get('auth/google', [Controllers\GoogleSocialiteController::class, 'redirectToGoogle']);
Route::get('callback/google', [Controllers\GoogleSocialiteController::class, 'handleCallback']);

Route::prefix('admin')->middleware(['auth', 'verified', 'can:use admin panel'])->group(function () {

	Route::get('/', function () {
		return redirect('admin/products');
	});

	$entities = collect([
		['name' => 'role', 'plural' => 'roles', 'controller' => Controllers\RoleController::class],
		['name' => 'user', 'plural' => 'users', 'controller' => Controllers\UserController::class],
		['name' => 'country', 'plural' => 'countries', 'controller' => Controllers\CountryController::class],
		['name' => 'brand', 'plural' => 'brands', 'controller' => Controllers\BrandController::class],
		['name' => 'measure', 'plural' => 'measures', 'controller' => Controllers\MeasureController::class],
		['name' => 'agent', 'plural' => 'agents', 'controller' => Controllers\AgentController::class],
		['name' => 'app_store', 'plural' => 'app_stores', 'controller' => Controllers\AppStoreController::class],
		['name' => 'currency', 'plural' => 'currencies', 'controller' => Controllers\CurrencyController::class],
		['name' => 'attribute_group', 'plural' => 'attribute_groups', 'controller' => Controllers\AttributeGroupController::class],
		['name' => 'attribute', 'plural' => 'attributes', 'controller' => Controllers\AttributeController::class],
		['name' => 'rating', 'plural' => 'ratings', 'controller' => Controllers\RatingController::class],
		['name' => 'category', 'plural' => 'categories', 'controller' => Controllers\CategoryController::class],
		['name' => 'product', 'plural' => 'products', 'controller' => Controllers\ProductController::class],
		['name' => 'product_request', 'plural' => '{product_id}/request_success', 'controller' => Controllers\ProductRequestController::class],
		['name' => 'deal', 'plural' => 'deals', 'controller' => Controllers\DealController::class],
		['name' => 'website', 'plural' => 'websites', 'controller' => Controllers\WebsiteController::class],
		['name' => 'license', 'plural' => 'licenses', 'controller' => Controllers\LicenseController::class],
		['name' => 'os', 'plural' => 'oss', 'controller' => Controllers\OSController::class],
		['name' => 'film_genre', 'plural' => 'film_genres', 'controller' => Controllers\FilmGenreController::class],
		['name' => 'age_rating', 'plural' => 'age_ratings', 'controller' => Controllers\AgeRatingController::class],
		['name' => 'film', 'plural' => 'films', 'controller' => Controllers\FilmController::class],
		['name' => 'film_review', 'plural' => 'film_reviews', 'controller' => Controllers\FilmReviewController::class],
		['name' => 'man', 'plural' => 'people', 'controller' => Controllers\PeopleController::class],
		['name' => 'app', 'plural' => 'apps', 'controller' => Controllers\AppController::class],
		['name' => 'domain', 'plural' => 'domains', 'controller' => Controllers\AllowedDomainsController::class],
		['name' => 'badge', 'plural' => 'badges', 'controller' => Controllers\BadgeController::class],
		['name' => 'auth_domain', 'plural' => 'auth_domains', 'controller' => Controllers\AllowedAuthDomainsController::class],
		['name' => 'tag', 'plural' => 'tags', 'controller' => Controllers\TagController::class],

	])->map(function ($item) {
		return (object) $item;
	});
	foreach ($entities as $entity) {
		Route::middleware(['can:view ' . $entity->plural])->group(function () use ($entity) {
			Route::get($entity->plural, [$entity->controller, 'list']);
			Route::middleware(['can:update ' . $entity->plural])->group(function () use ($entity) {
				Route::get($entity->name, [$entity->controller, 'form']);
				Route::post($entity->name, [$entity->controller, 'save']);
			});
			Route::middleware(['can:delete ' . $entity->plural])->post('delete_' . $entity->plural, [$entity->controller, 'delete']);
		});
	}

	Route::prefix('export')->group(function () {
		$importEntities = collect([
			['name' => 'product', 'plural' => 'products', 'controller' => Controllers\ProductController::class, 'action' => 'export'],
			['name' => 'category', 'plural' => 'categories', 'controller' => Controllers\CategoryController::class, 'action' => 'export'],
			['name' => 'agent', 'plural' => 'agents', 'controller' => Controllers\AgentController::class, 'action' => 'export'],
			['name' => 'brand', 'plural' => 'brands', 'controller' => Controllers\BrandController::class, 'action' => 'export'],
			['name' => 'country', 'plural' => 'countries', 'controller' => Controllers\CountryController::class, 'action' => 'export'],
			['name' => 'currency', 'plural' => 'currencies', 'controller' => Controllers\CurrencyController::class, 'action' => 'export'],
			['name' => 'measure', 'plural' => 'measures', 'controller' => Controllers\MeasureController::class, 'action' => 'export'],
			['name' => 'website', 'plural' => 'websites', 'controller' => Controllers\WebsiteController::class, 'action' => 'export'],
			['name' => 'app', 'plural' => 'apps', 'controller' => Controllers\AppController::class, 'action' => 'export'],
		])->map(function ($item) {
			return (object) $item;
		});

		foreach ($importEntities as $entity) {
			Route::middleware(['can:update ' . $entity->plural])->group(function () use ($entity) {
				Route::get($entity->plural, [$entity->controller, $entity->action])->name("export-{$entity->plural}");
			});
		}
	});

});
