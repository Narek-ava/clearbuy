<?php

namespace App\Http\Livewire\Importable;


use Livewire\Component;
use App\Http\Livewire\Traits\CSVImport;
use App\Models\App;

class AppsImport extends Component
{
    use CSVImport;

    public function mount() {
        $this->baseUrl = '/admin/apps';
    }

    public function import()
    {

        foreach ($this->rows as $row) {
            $exist = App::where('name', $row['name'])->first();
            if (!$exist) {
                $app =  App::create($row);

                if ($row['images']) {
                    $images = explode(',', $row['images']);
                    foreach ($images as $order => $path) {
                        $app->images()->save(new \App\Models\AppImage(['path' => $path, 'order' => $order]));
                    }
                }

                if ($row['os']) {
                    $os = explode(',', $row['os']);
                    $osList = \App\Models\OS::whereKey($os)->get();
                    foreach ($osList as $os) {
                        $app->os()->attach($os);
                    }
                }

                if ($row['countries']) {
                    $countries = explode(',', $row['countries']);
                    $countries = \App\Models\Country::whereKey($countries)->get();
                    foreach ($countries as $country) {
                        $app->countries()->attach($country);
                    }
                }
            }
        }
    }
}
