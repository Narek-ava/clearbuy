<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Http\Requests\Badge as Requests;

class BadgeController extends BaseItemController
{
    protected $baseUrl = '/admin/badges';

    public function list(Requests\ListRequest $request)
    {
        $items = Badge::orderBy($request->sort, $request->order);

        $listData = $this->getListData($request);
        $paginator = $items->paginate($request->perPage);

        //for item's logo show
        $updatedItems = $paginator->getCollection();
        $updatedItems->transform(function ($value) {

            if(!is_null($value->image)){
                $value->image = $value->getLogoUrl($value->image);
            }
            return $value;
        });
        $listData['items'] = $paginator->setCollection($updatedItems);

        return view('badge.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Badge::find($request->id);

        return view('badge.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $badge = Badge::firstOrNew(['id' => $request->id]);
        $badge->name = $request->name;
        $badge->year = $request->year;
        $badge->image = $request->image ?? null;
        $badge->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/badge?id='.$badge->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            Badge::whereIn('id', $request->items)->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->withErrors([
                'delete' => 'Unable to delete. Selected items are used in other objects.'
            ])->withInput();
        }

        return back()->with([
            'status' => 'success',
            'message' => 'deleted successfully'
        ]);
    }
}
