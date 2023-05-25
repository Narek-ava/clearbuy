<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\FilmReview;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\FilmReview as Requests;

class FilmReviewController extends BaseItemController
{
    protected $baseUrl = '/admin/film_reviews';

    public function list(Requests\ListRequest $request)
    {
        $items = FilmReview::orderBy($request->sort, $request->order)->with([
            'film'
        ]);

        if ($request->title) {
            $items->where('title', 'LIKE', '%'.$request->title.'%');
        }

        if ($request->rating_from) {
            $items->where('rating', '>=', $request->rating_from);
        }

        if ($request->rating_to) {
            $items->where('rating', '<=', $request->rating_to);
        }

        if ($request->film) {
            $items->whereHas('film', function($q) use ($request) {
                $q->where('film.name', 'LIKE', '%'.$request->film.'%');
            });
        }

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);

        return view('film_review.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = FilmReview::find($request->id);

        return view('film_review.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = FilmReview::firstOrNew(['id' => $request->id]);

        $item->title = $request->title;
        $item->subtitle = $request->subtitle;
        $item->rating = $request->rating;
        $item->summary = $request->summary;
        $item->positive = $request->positive;
        $item->negative = $request->negative;

        $item->save();
        $item->refresh();

        $film = Film::find($request->film);
        if ($film !== null) {
            $item->film()->associate($film);
        } else {
            return back()->withErrors([
                'film' => 'Selected film does not exist'
            ])->withInput();
        }

        $item->recomendations()->detach();
        if ($request->recomendations) {
            $films = Film::whereKey($request->recomendations)->get();
            foreach ($films as $film) {
                $item->recomendations()->attach($film);
            }
        }

        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/film_review?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            FilmReview::whereKey($request->items)->delete();
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
