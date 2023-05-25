<?php

namespace App\Http\Livewire\Importable;

use App\Models\Brand;
use Livewire\Component;
use App\Http\Livewire\Traits\CSVImport;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\Brand\SaveRequest as Request;
use App\Traits\NullableFields;
use Image;

class BrandsImport extends Component
{
    use CSVImport, NullableFields;

    public function mount() {
        $this->baseUrl = '/admin/brands';
    }

    public function import()
    {
        //get Brand SaveRequest for actually rules array
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

                if (Brand::where('name', $row['name'])->doesntExist()) {

                    //check if foreign key exists
                    if (DB::table('country')->where('id', $row['country_id'])->doesntExist()) {
                        $row['country_id'] = null;
                    }

                    Brand::create($row);
                    $storedData++;

                    if(!is_null($row['image']))
                    {
                        $url = $row['image'];

                        if(filter_var(trim($url), FILTER_VALIDATE_URL)) {

                            $filename = basename($url);

                            if(Storage::disk('do_image_spaces')->missing('brands/'.$filename))
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
                                        if(Storage::disk('do_image_spaces')->missing('brands'))
                                        {
                                            Storage::disk('do_image_spaces')->makeDirectory('brands');
                                        }

                                        //cloud upload
                                        try{
                                            Storage::disk('do_image_spaces')->put('brands/'.$filename, $contents, 'public');
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
