<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Image;

class ImageUpload extends Component
{
    use WithFileUploads;

    public $files;
    public $path;
    public $product_id;
    public $asin;

    protected $listeners = ['pathUpdated'];

    public function render()
    {
        return view('livewire.image-upload');
    }

    public function updatedFiles() {

        // $this->validate([
        //     'files.*' => 'image|max:1024'
        // ]);

        $this->save();
    }

    public function save()
    {
        if ($this->files == null) {
            return;
        }

        $filePath = storage_path('app/products');

        foreach ($this->files as $file) {

            $name = $file->getClientOriginalName();

            // if (Storage::disk('do_image_spaces')->exists($this->path.$name)) {
            //     $i = 1;
            //     while (Storage::disk('do_image_spaces')->exists($this->path.$i.'_'.$name)) {
            //         $i++;
            //     }
            //     $name = $i.'_'.$name;
            // }

            $img = Image::make($file); //make image for resize
//            if (!Storage::disk('do_image_spaces')->exists($this->path.'/'.$this->product_id)){
//                Storage::disk('do_image_spaces')->makeDirectory($this->path.'/'.$this->product_id);
//            }
            if($img->width() > 1920 || $img->height() > 1080)
            {
                $img->resize(1920, 1080, function ($const) {
                   $const->aspectRatio();
                })->save($filePath.'/'.$name, 75); //save temporary file

                $contents = file_get_contents($filePath.'/'.$name);
                Storage::disk('do_image_spaces')->put($this->path.'/'.$name, $contents, 'public');
                unlink($filePath.'/'.$name); //remove temporary file

            }elseif($this->asin === ''){
                $file->storePubliclyAs('products'.'/'.$this->product_id.'/',$name, 'do_image_spaces');
                Storage::disk('do_image_spaces')->copy($this->path, "products/{$this->product_id}/{$name}");
            }
            $file->storePubliclyAs('products'.'/'.$this->product_id.'/'.$this->asin.'/',$name, 'do_image_spaces');
        }

        $this->files = null;

        $this->emit('filesUploaded'); //close upload form popup
    }

    public function pathUpdated($path)
    {
        $this->path = $path;
    }
}
