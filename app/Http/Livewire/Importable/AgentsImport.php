<?php

namespace App\Http\Livewire\Importable;


use App\Models\Agent;
use Livewire\Component;
use App\Http\Livewire\Traits\CSVImport;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests\Agent\SaveRequest as Request;
use App\Traits\NullableFields;
use Image;

class AgentsImport extends Component {

    use CSVImport, NullableFields;

    public function mount() {
        $this->baseUrl = '/admin/agents';
    }

    public function import() {

        //get Agent SaveRequest for actually rules array
        $request = new Request();
        $this->setRules($request->rules());

        /*
        *   Validation
        */

        foreach ($this->rows as $key=>$row) {

            //convert empty cells to null and some specified cells to array
            $ar_string_to_array = ['countries'];

            $row_with_nulls = collect($row)->map(function($item, $key) use ($ar_string_to_array) {

                if(in_array($key, $ar_string_to_array)){

                    if(strpos($item, '|')) $item = explode('|', $item);
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

                if (Agent::where('name', $row['name'])->where('type_id', $row['type_id'])->doesntExist()) {

                      Agent::create($row);
                      $storedData++;

                      if(!is_null($row['image']))
                      {
                          $url = $row['image'];

                          if(filter_var(trim($url), FILTER_VALIDATE_URL)) {

                              $filename = basename($url);

                              if(Storage::disk('do_image_spaces')->missing('agents/'.$filename))
                              {
                                  try{

                                      $contents = file_get_contents($url);

                                      if($contents !== false AND !empty($contents)) {

                                          $filePath = storage_path('app/images/');

                                          $img = Image::make($url); //make image for resize

                                          if($img->width() > 1920 || $img->height() > 1080)
                                          {
                                              $img->resize(1920, 1080, function ($const) {
                                                 $const->aspectRatio();
                                              })->save($filePath.'/'.$filename, 75); //save temporary file

                                              $contents = file_get_contents($filePath.'/'.$filename);
                                              $resize = true;
                                          }

                                          //create cloud directory if not exists
                                          if(Storage::disk('do_image_spaces')->missing('agents'))
                                          {
                                              Storage::disk('do_image_spaces')->makeDirectory('agents');
                                          }

                                          //cloud upload
                                          try{
                                              Storage::disk('do_image_spaces')->put('agents/'.$filename, $contents, 'public');
                                              if(isset($resize)) unlink($filePath.'/'.$filename); //remove temporary file
                                          }catch(\Exception $e){
                                              //Log::channel('import_slack')->info($e->getMessage());
                                          }
                                      }

                                  }catch(\Exception $e){
                                      //Log::channel('import_slack')->info($e->getMessage());
                                  }
                              }

                          }
                      }

                }
            }
        }

        return $storedData;
    }

}
