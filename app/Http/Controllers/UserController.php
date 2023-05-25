<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User as Requests;

class UserController extends BaseItemController
{
    protected $baseUrl = '/admin/users';

    public function list(Requests\ListRequest $request)
    {
        $items = User::with('roles');

        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        if ($request->email) {
            $items->where('email', 'LIKE', "%$request->email%");
        }

        if ($request->roles && count($request->roles) > 0) {
            $items->whereHas('roles', function($q) use ($request) {
                $q->whereIn('id', $request->roles);
            });
        }

        $items->orderBy($request->sort, $request->order);

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);
        $listData['roles'] = Role::all();

        return view('user.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $user = User::find($request->id);

        $formData = $this->getFormData($request);
        $formData['item'] = $user;
        $formData['roles'] = Role::orderByRaw('CASE WHEN name="admin" THEN 0 ELSE 1 END ASC')->orderBy('name', 'ASC')->get();

        return view('user.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $user = User::findOrNew($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->password) $user->password = Hash::make($request->password);
        $user->product_request_mailing = $request->product_request_mailing;

        $user->save();

        $roles = Role::whereIn('id', $request->roles)->get();
        $user->syncRoles($roles);

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/user?id='.$user->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        $userIds = collect($request->items)->filter(function($item) use ($request) {
            return $item != $request->user()->id;
        });

        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $user->deleteProfilePhoto();
            $user->tokens->each->delete();
            $user->delete();
        }

        return back()->with([
            'status' => 'success',
            'message' => 'deleted successfully'
        ]);
    }
}
