<?php

namespace App\Http\Controllers;

use App\Models\People;
use Illuminate\Http\Request;
use App\Http\Requests\People as Requests;

class PeopleController extends BaseItemController
{
    protected $baseUrl = '/admin/people';

    public function list(Requests\ListRequest $request)
    {
        $items = People::orderBy($request->sort, $request->order);

        $listData = $this->getListData($request);
        $paginator = $items->paginate($request->perPage);

        $updatedItems = $paginator->getCollection();
        $listData['items'] = $paginator->setCollection($updatedItems);

        return view('people.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = People::find($request->id);

        return view('people.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = People::firstOrNew(['id' => $request->id]);
        $item->name = $request->name;
        $item->surname = $request->surname;
        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/man?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function export()
    {
        $exporter = new CSVExport(new People);
        $exporter->export('Actors');
        return redirect()->back();
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            People::whereIn('id', $request->items)->delete();
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
