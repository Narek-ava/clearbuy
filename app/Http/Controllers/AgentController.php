<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Country;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Agent as Requests;
use App\Http\Helpers\CSVExport;

class AgentController extends BaseItemController
{
    protected $baseUrl = '/admin/agents';

    public function list(Requests\ListRequest $request)
    {
        $items = Agent::orderBy($request->sort, $request->order);

        //filtering from UI
        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%")
                  ->orWhere('surname', 'LIKE', "%$request->name%");
        }

        if ($request->website) {
            $items->where('website', 'LIKE', "%$request->website%");
        }

        if ($request->is_retailer !== null) {
            $items->where('is_retailer', '=', $request->is_retailer);
        }

        if ($request->type !== null) {
            $items->where('type_id', '=', $request->type);
        }

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
        $listData['types'] = Agent::types();

        return view('agent.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Agent::find($request->id);
        $formData['types'] = Agent::types();
        $formData['countries'] = Country::orderBy('name')->get();

        return view('agent.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = Agent::firstOrNew(['id' => $request->id]);
        $item->name = $request->name;
        $item->surname = $request->surname;
        $item->website = $request->website;
        $item->is_retailer = $request->is_retailer;
        $item->type_id = (int)$request->type;
        $item->image = $request->images[0] ?? null;
        $item->countries = $request->countries;
        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/agent?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function export()
    {
        $exporter = new CSVExport(new Agent);
        $exporter->export('agents');
        return redirect()->back();
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            Agent::whereIn('id', $request->items)->delete();
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
