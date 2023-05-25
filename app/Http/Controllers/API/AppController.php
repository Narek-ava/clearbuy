<?php

namespace App\Http\Controllers\API;

use App\Models\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\API\AppRequest;

use App\Http\Resources as Resources;

class AppController extends Controller
{
    /**
     * Returns app list by ids
     */
    public function getApp(AppRequest $request) {

        $apps = App::whereIn('id', $request->ids)->orderBy('created_at', 'DESC')->with('links')->get();

        return response()->json(Resources\AppResource::collection($apps));
    }


    /**
     * Returns all app list
     */
    public function getApps(Request $request) {

        $apps = App::select('id', 'name')->orderBy('created_at', 'DESC')->get();

        return response()->json(Resources\AppsResource::collection($apps));
    }

    /**
     * Returns app list with stores
     */
    public function getAppsStores(AppRequest $request) {

        $apps = App::whereIn('id', $request->ids)->orderBy('created_at', 'DESC')->with('links')->get();

        return response()->json(Resources\AppsStoresResource::collection($apps));
    }


}
