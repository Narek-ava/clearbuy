<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Role as Requests;

class RoleController extends BaseItemController
{
    protected $baseUrl = '/admin/roles';

    public function list(Requests\ListRequest $request)
    {
        $items = Role::whereNotIn('id', [1]);

        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        if ($request->permissions && count($request->permissions) > 0) {
            $items->whereHas('permissions', function($q) use ($request) {
                $q->whereIn('id', $request->permissions);
            });
        }

        $items->orderBy($request->sort, $request->order);

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);
        $listData['permissions'] = Permission::all();

        return view('role.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Role::find($request->id);
        $formData['permissions'] = Permission::all();

        return view('role.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $role = Role::firstOrNew(['id' => $request->id]);
        $role->name = $request->name;
        $role->save();

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/role?id='.$role->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        Role::whereIn('id', $request->items)->delete();
        return back()->with([
            'status' => 'success',
            'message' => 'deleted successfully'
        ]);
    }
}
