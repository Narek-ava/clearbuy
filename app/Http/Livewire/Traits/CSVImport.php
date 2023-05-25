<?php

namespace App\Http\Livewire\Traits;

use Revolution\Google\Sheets\Facades\Sheets;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

trait CSVImport
{
    public $COLUMNS = [];

    public $spreadsheet_id;
    public $sheet_id;
    public $rows;
    public $exportUrl;
    public $progress = 0;
    public $totalRecords = 0;
    public $failedImports = [];
    public $baseUrl;

    public $validsData = [];
    public $errorsData = [];

    protected $rules = [
        'spreadsheet_id' => 'required|string',
        'sheet_id' => 'required|string',
    ];

    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    public function mount(string $exportUrl)
    {
        $this->exportUrl = $exportUrl;
    }

    public function init()
    {
        $this->validate();

        $rows = Sheets::spreadsheet($this->spreadsheet_id)
            ->sheet($this->sheet_id)
            ->get();

        $header = collect($rows->pull(0))->map(function ($field) {
            return $field;
            //return strtolower(str_replace(' ', '_', $field));
        })->toArray();

        try{

            $this->rows = Sheets::collection($header, $rows)->toArray();

        }catch(\Exception $e){

            session()->flash('status', 'success');
            session()->flash('message', 'Header columns and rows columns did not match!');
            return redirect()->to($this->baseUrl);
        }

        if (!empty($this->rows)) {

            $storedData = $this->import();

            if(intval($storedData) > 0)
            {
                $message = $storedData." records imported successfully";
                session()->flash('status', 'success');

            }else{

                $message = "No new entries to import";
                session()->flash('status', 'success');
            }

            session()->flash('message', $message);
            return redirect()->to($this->baseUrl);
        }
    }

    public function resetAttr()
    {
        $this->reset(['spreadsheet_id', 'sheet_id', 'rows']);
    }

    abstract public function import();

    //item - one row of sheets
    public function validateData(array $item, $key=0)
    {
        try{ //without try/catch validator stops executing loop!

            //make validator with new rules
            $validator = Validator::make($item, $this->rules);
            $validated = $validator->validate(); //get validated data
            array_push($this->validsData, $validated);

        }catch(\Exception $e){

            if($validator->fails()){

                $errors = $validator->errors();
                foreach ($errors->all() as $message) {
                    //Log::channel('import')->info('Row: '.$key++.' - '.$message);
                    array_push($this->errorsData, 'Row: '.($key+1).' - '.$message);
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.product-c-s-v-import');
    }
}
