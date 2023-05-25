<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Services\SidebarLinksService;

class BaseItemController extends Controller
{
    protected $baseUrl = '';

    protected function getListData(Request $request)
    {
        return [
            'allowedPerPages' => $request->allowedPerPages(),
            'sort' => $request->sort,
            'order' => $request->order,
            'backUrl' => $request->fullUrl(),
            'sidebarLinks' => SidebarLinksService::getLinks($this->baseUrl)
        ];
    }

    protected function getFormData(Request $request)
    {
        return [
            'is_copy' => false,
            'backUrl' => $request->backUrl,
            'sidebarLinks' => SidebarLinksService::getLinks($this->baseUrl)
        ];
    }
}
