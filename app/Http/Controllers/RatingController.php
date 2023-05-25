<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Http\Requests\Rating as Requests;

class RatingController extends BaseItemController
{
    protected $baseUrl = '/admin/ratings';

    public function list(Requests\ListRequest $request)
    {
        $items = Rating::orderBy('sort_order');

        if($request->name) $items->where('name', 'LIKE', "%$request->name%");

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);

        return view('ratings.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);

        if ($request->copy_id) {
            $formData['item'] = Rating::findOrFail($request->copy_id);
            $formData['is_copy'] = true;
        } else {
            $formData['item'] = Rating::find($request->id);
        }

        return view('ratings.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = Rating::firstOrNew(['id' => $request->id]);
        $item->name = $request->name;
        $item->sort_order = $request->sort_order;
        $item->save();

        return redirect($request->backUrl)->with([
            'status' => 'success',
            'message' => 'saved successfully'
        ]);
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            Rating::whereIn('id', $request->items)->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            if($request->ajax()){
               return $request->session()->flash('message', 'Unable to delete. Selected items are used in other objects.');
            }

            return back()->withErrors([
                'delete' => 'Unable to delete. Selected items are used in other objects.'
            ])->withInput();
        }

        if($request->ajax()){
            $request->session()->flash('status', 'success');
            return $request->session()->flash('message', 'deleted successfully');
        }

        return redirect($request->backUrl)->with([
            'status' => 'success',
            'message' => 'deleted successfully'
        ]);
    }
}
