<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\AgeRating;
use App\Models\Film;
use App\Models\FilmGenre;
use App\Models\People;
use App\Models\Website;


use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Film as Requests;

class FilmController extends BaseItemController
{
    protected $baseUrl = '/admin/films';

    public function list(Requests\ListRequest $request)
    {
        $items = Film::orderBy($request->sort, $request->order)->with([
            'genres',
            'director',
            'writer',
            'producer',
            'actors',
            'production_company'
        ]);

        if ($request->name) {
            $items->where('film.name', 'LIKE', '%'.$request->name.'%');;
        }

        if ($request->type !== null) {
            $items->where('film.type_id', $request->type);;
        }

        if ($request->release_date_from) {
            $items->where('release_date', '>=', $request->release_date_from);
        }

        if ($request->release_date_to) {
            $items->where('release_date', '<=', $request->release_date_to);
        }

        if ($request->age_rating) {
            $items->where('age_rating_id', $request->age_rating);;
        }

        if ($request->genre) {
            $items->whereHas('genres', function($q) use ($request) {
                $q->where('film_genre.name', 'LIKE', '%'.$request->genre.'%');
            });
        }

        if ($request->director) {
            $items->whereHas('director', function($q) use ($request) {
                $q->where('people.name', 'LIKE', '%'.$request->director.'%')
                ->orWhere('people.surname', 'LIKE', '%'.$request->director.'%');
            });
        }

        if ($request->writer) {
            $items->whereHas('writer', function($q) use ($request) {
                $q->where('people.name', 'LIKE', '%'.$request->writer.'%')
                ->orWhere('people.surname', 'LIKE', '%'.$request->writer.'%');
            });
        }

        if ($request->producer) {
            $items->whereHas('producer', function($q) use ($request) {
                $q->where('people.name', 'LIKE', '%'.$request->producer.'%')
                ->orWhere('people.surname', 'LIKE', '%'.$request->producer.'%');
            });
        }

        if ($request->actor) {
            $items->whereHas('actors', function($q) use ($request) {
                $q->where('people.name', 'LIKE', '%'.$request->actor.'%')
                ->orWhere('people.surname', 'LIKE', '%'.$request->actor.'%');
            });
        }

        if ($request->production_company) {
            $items->whereHas('production_company', function($q) use ($request) {
                $q->where('people.name', 'LIKE', '%'.$request->production_company.'%')
                ->orWhere('people.surname', 'LIKE', '%'.$request->production_company.'%');
            });
        }

        $listData = $this->getListData($request);
        $listData['items'] = $items->paginate($request->perPage);
        $listData['types'] = Film::types();
        $listData['ageRatings'] = AgeRating::all();

        return view('film.list', $listData);
    }

    public function form(Requests\GetFormRequest $request)
    {
        $formData = $this->getFormData($request);
        $formData['item'] = Film::find($request->id);
        $formData['types'] = Film::types();
        $formData['ageRatings'] = AgeRating::all();

        return view('film.form', $formData);
    }

    public function save(Requests\SaveRequest $request)
    {
        $item = Film::firstOrNew(['id' => $request->id]);

        //general
        $item->name = $request->name;
        $item->type_id = (int)$request->type;

        $item->runtime = $request->runtime;
        $item->bingetime = $request->bingetime;
        $item->seasons = $request->seasons;
        $item->still_running = (bool)$request->still_running;
        $item->end_of_release = $request->end_of_release;

        $item->release_date = $request->release_date;
        $item->trailer_link = $request->trailer_link;
        $item->description = $request->description;
        $item->image = $request->images[0] ?? '';

        $item->save();
        $item->refresh();

        $age_rating = AgeRating::find($request->age_rating);
        if ($age_rating !== null) {
            $item->age_rating()->associate($age_rating);
        } else {
            return back()->withErrors([
                'age_rating' => 'Selected age rating does not exist'
            ])->withInput();
        }

        $item->genres()->detach();
        if ($request->genres) {
            $genres = FilmGenre::whereKey($request->genres)->get();
            foreach ($genres as $genre) {
                $item->genres()->attach($genre);
            }
        }

        $item->websites()->detach();
        if ($request->websites) {
            $websites = Website::whereKey($request->websites)->get();
            foreach ($websites as $website) {
                $item->websites()->attach($website);
            }
        }

        $item->recomendations()->detach();
        if ($request->recomendations) {
            $films = Film::whereKey($request->recomendations)->get();
            foreach ($films as $film) {
                $item->recomendations()->attach($film);
            }
        }

        $item->actors()->detach();
        if ($request->actors) {
            $people = People::whereKey($request->actors)->get();
            foreach ($people as $man) {
                $item->actors()->attach($man);
            }
        }

        $item->director()->associate(People::find($request->director));
        $item->writer()->associate(People::find($request->writer));
        $item->producer()->associate(People::find($request->producer));
        $item->production_company()->associate(People::find($request->production_company));

        $item->save();

        if ($request->id) { //existing item

            return back()->with([
                'status' => 'success',
                'message' => 'saved successfully'
            ]);

        }else{ //new item

            return redirect('/admin/film?id='.$item->id.'&backUrl='.urlencode($request->backUrl))->with([
                'status' => 'success',
                'message' => 'new item saved successfully'
            ]);
        }
    }

    public function delete(Requests\DeleteRequest $request)
    {
        try {
            Film::whereKey($request->items)->delete();
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
