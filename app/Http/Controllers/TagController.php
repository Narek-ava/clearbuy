<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Requests\Tag as Requests;

class TagController extends BaseItemController
{
      protected $baseUrl = '/admin/tags';

      public function list(Requests\ListRequest $request)
      {
          $items = Tag::orderBy($request->sort, $request->order);

          $listData = $this->getListData($request);
          $listData['items'] = $items->paginate($request->perPage);

          return view('tag.list', $listData);
      }

      public function form(Requests\GetFormRequest $request)
      {
          $formData = $this->getFormData($request);
          $formData['item'] = Tag::find($request->id);

          return view('tag.form', $formData);
      }

      public function save(Requests\SaveRequest $request)
      {
          $tag = Tag::firstOrNew(['id' => $request->id]);
          $tag->name = $request->name;
          $tag->save();

          if ($request->id) { //existing item

              return back()->with([
                  'status' => 'success',
                  'message' => 'saved successfully'
              ]);

          }else{ //new item

              return redirect('/admin/tag?id='.$tag->id.'&backUrl='.urlencode($request->backUrl))->with([
                  'status' => 'success',
                  'message' => 'new item saved successfully'
              ]);
          }


      }

      public function delete(Requests\DeleteRequest $request)
      {
          try {
              Tag::whereIn('id', $request->items)->delete();
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
