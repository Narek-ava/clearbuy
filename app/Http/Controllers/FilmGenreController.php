<?php

namespace App\Http\Controllers;

use App\Models\FilmGenre;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\FilmGenre as Requests;

class FilmGenreController extends baseItemController
{
    protected $baseUrl = '/admin/film_genres';

    public function list(Requests\ListRequest $request)
    {
        $items = FilmGenre::orderBy($request->sort, $request->order);

        if ($request->name) {
            $items->where('name', 'LIKE', "%$request->name%");
        }

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);

        return view('film_genre.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = FilmGenre::find($request->id);

        return view('film_genre.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = FilmGenre::firstOrNew(['id' => $request->id]);
        $item->name = $request->name;
        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/film_genre?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            FilmGenre::whereIn('id', $request->items)->delete();
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
