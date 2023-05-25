<?php

namespace App\Http\Livewire\Importable;


use Livewire\Component;
use App\Http\Livewire\Traits\CSVImport;
use App\Models\Website;

class WebsitesImport extends Component
{
    use CSVImport;

    public function mount() {
        $this->baseUrl = '/admin/websites';
    }

    public function import()
    {

        foreach ($this->rows as $row) {
            $exist = Website::where( 'url', $row['url'] )->first();

            if (!$exist) {
                Website::create($row);
            }
        }

    }
}
