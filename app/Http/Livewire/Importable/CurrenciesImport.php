<?php

namespace App\Http\Livewire\Importable;


use Livewire\Component;
use App\Http\Livewire\Traits\CSVImport;
use App\Models\Currency;

use Illuminate\Support\Facades\Log;

use App\Http\Requests\Currency\SaveRequest as Request;
use App\Traits\NullableFields;

class CurrenciesImport extends Component
{
    use CSVImport, NullableFields;

    public function mount() {
        $this->baseUrl = '/admin/currencies';
    }

    public function import()
    {
        //get Currency SaveRequest for actually rules array
        $request = new Request();
        $this->setRules($request->rules());

        /*
        *   Validation
        */

        foreach ($this->rows as $key=>$row) {

            //convert empty cells to null and some specified cells to array
            $ar_string_to_array = ['country_ids'];

            $row_with_nulls = collect($row)->map(function($item, $key) use ($ar_string_to_array) {

                if(in_array($key, $ar_string_to_array)){

                    if(strpos($item, ',')) $item = explode(',', $item);
                    else $item = [$item];
                    return $item;
                }
                return $this->nullIfEmpty($item);

            })->toArray();

            $this->validateData($row_with_nulls, $key);
        }

        if(!empty($this->errorsData)){
            $message = join(PHP_EOL, $this->errorsData);
            Log::channel('import_slack')->info($message);
        }

        /*
        *   Data Storage
        */

        $storedData = 0;

        if(!empty($this->validsData))
        {
            ini_set('max_execution_time', '0'); //infinite time

            foreach ($this->validsData as $key=>$row) {

                if(count($row['country_ids']) > 1) $row['country_ids'] = implode(',',$row['country_ids']);
                else $row['country_ids'] = $row['country_ids'][0];

                if (Currency::where('name', $row['name'])->doesntExist()) {

                    Currency::create($row);
                    $storedData++;
                }
            }
        }

        return $storedData;

    }
}
