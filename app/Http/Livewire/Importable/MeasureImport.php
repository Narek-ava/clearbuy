<?php

namespace App\Http\Livewire\Importable;


use Livewire\Component;
use App\Http\Livewire\Traits\CSVImport;
use App\Models\Measure;

use Illuminate\Support\Facades\Log;

use App\Http\Requests\Measure\SaveRequest as Request;
use App\Traits\NullableFields;

class MeasureImport extends Component
{
    use CSVImport, NullableFields;

    public function mount() {
        $this->baseUrl = '/admin/measures';
    }

    public function import()
    {
        //get Measure SaveRequest for actually rules array
        $request = new Request();
        $this->setRules($request->rules());

        /*
        *   Validation
        */

        foreach ($this->rows as $key=>$row) {

            //convert empty cells to null
            $row_with_nulls = collect($row)->map(function($item, $key) {
                return $this->nullIfEmpty($item);

            })->toArray();

            $this->validateData($row_with_nulls, $key);
        }

        if(!empty($this->errorsData)){
            $message = join(PHP_EOL, $this->errorsData);
            //Log::channel('import_slack')->info($message);
        }

        /*
        *   Data Storage
        */

        $storedData = 0;

        if(!empty($this->validsData))
        {
            ini_set('max_execution_time', '0'); //infinite time

            foreach ($this->validsData as $key=>$row) {

                if (Measure::where('name', $row['name'])->doesntExist()) {

                    Measure::create($row);
                    $storedData++;
                }
            }
        }

        return $storedData;
    }

}
