<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class ItemImages extends Component
{
    public $images; //product images
    public $path = '';
    public $files; //list of files in the cloud
    public $folders;
    public $selected_items = [];
    public $name;
    public $multiple;
    public $items2Delete = [];
    public $item2Delete = null;
    public $asin = ''; //only for products (amazon button)
    public $product_id;


    protected $listeners = ['filesUploaded', 'renderWithNewImage'];

    public function mount($name, $path, $images = [], $multiple = true) {

        $this->name = $name;
        $this->multiple = $multiple;

        //show product images in cloud
        $this->images = collect($images)->filter(function($item) {
               return  filter_var(trim($item), FILTER_VALIDATE_URL) ? true : Storage::disk('do_image_spaces')->exists(trim($item));
        })->map(function($item) {
            return (object)[
                'path' => trim($item),
                'name' => basename(Storage::disk('do_image_spaces')->path($item)),
                'url' => filter_var(trim($item), FILTER_VALIDATE_URL) ? trim($item) :  Storage::disk('do_image_spaces')->temporaryUrl($item, now()->addMinutes(10))
            ];
        });

        //do not load from cloud on startup
        $this->path = $path; //folders: products, brands, agents & etc.
        $this->selected = [];
        $this->selected_items = [];
        $this->folders = [];
        $this->files = $this->images;

        //$this->resetFileSystem(); //this will update all files in the root directory and preload them
    }

    public function resetFileSystem()
    {
        $this->path = '';
        $this->selected = [];
        $this->updateFileSystem();
    }

    public function updateFileSystem()
    {
        $this->selected_items = [];
        $this->folders = collect(Storage::disk('do_image_spaces')->directories($this->path))->map(function($item) {
            return (object)[
                'path' => $item,
                'name' => basename(Storage::disk('do_image_spaces')->path($item))
            ];
        });
        $this->files = collect(Storage::disk('do_image_spaces')->files($this->path))->map(function($item) {
            return (object)[
                'path' => $item,
                'name' => basename(Storage::disk('do_image_spaces')->path($item)),
                'url' => Storage::disk('do_image_spaces')->temporaryUrl($item, now()->addMinutes(10))
            ];
        });

        $this->emit('pathUpdated', $this->path);
    }

    public function hydrate()
    {
        $this->images = $this->images->filter(function($item) {
            return Storage::disk('do_image_spaces')->exists($item['path']);
        })->map(function($item) {
            return (object)[
                'path' => $item['path'],
                'url' => Storage::disk('do_image_spaces')->temporaryUrl($item['path'], now()->addMinutes(10))
            ];
        });
        $this->updateFileSystem();
    }

    public function moveUp($index)
    {
        if ($index >= $this->images->count() || $index <= 0) {
            return;
        }
        $tmp = $this->images[$index];
        $this->images[$index] = $this->images[$index - 1];
        $this->images[$index - 1] = $tmp;
    }

    public function moveDown($index)
    {
        if ($index >= $this->images->count() - 1 || $index < 0) {
            return;
        }
        $tmp = $this->images[$index];
        $this->images[$index] = $this->images[$index + 1];
        $this->images[$index + 1] = $tmp;
    }

    public function folder($folder)
    {
        if (!collect(Storage::disk('do_image_spaces')->directories($this->path))->contains($folder)) {
            return;
        }
        $this->path = $folder;
        $this->updateFileSystem();
    }

    public function back()
    {
        if ($this->path == '') {
            return;
        }
        $this->path = collect(explode('/', $this->path))->reverse()->slice(1)->reverse()->join('/');

        $this->updateFileSystem();
    }

    public function remove($index)
    {
        if ($index >= $this->images->count() || $index < 0) {
            return;
        }
        $this->images->splice($index, 1);
        $this->images = $this->images->values();
    }

    public function removeSelected(): void
    {
        $this->images = $this->images->reject(function ($value, $key) {
            return in_array($key, $this->items2Delete);
        });
        $this->items2Delete = [];
    }

    public function select()
    {
        if (!$this->multiple) {
            foreach ($this->selected_items as $selected) {

                if (Storage::disk('do_image_spaces')->exists(trim($selected))) {
                    $this->images = collect([((object)[
                        'path' => $selected,
                        'url' => Storage::disk('do_image_spaces')->temporaryUrl($selected, now()->addMinutes(10))
                    ])]);
                    break;
                }
            }
        } else {
            foreach ($this->selected_items as $selected) {
                $basename = basename($selected);
                if ($this->asin === '' && !Storage::disk('do_image_spaces')->exists("products/{$this->product_id}/{$basename}")){
                    Storage::disk('do_image_spaces')->copy($selected,"products/{$this->product_id}/{$basename}");
                }
                if ($this->asin !== '' && !Storage::disk('do_image_spaces')->exists("products/{$this->product_id}/{$this->asin}/{$basename}")){
                    Storage::disk('do_image_spaces')->copy($selected,"products/{$this->asin}/{$basename}");
                }
                if (Storage::disk('do_image_spaces')->exists(trim($selected)) && !$this->images->contains('path', $selected)) {
                    if ($this->asin !== ''){
                        $this->images->push((object)[
                            'path' => "products/{$this->product_id}/{$this->asin}/{$basename}",
                            'url' => Storage::disk('do_image_spaces')->temporaryUrl("products/{$this->product_id}/{$this->asin}/{$basename}", now()->addMinutes(10))
                        ]);
                    }else {
                        $this->images->push((object)[
                            'path' => "products/{$this->product_id}/{$basename}",
                            'url' => Storage::disk('do_image_spaces')->temporaryUrl("products/{$this->product_id}/{$basename}", now()->addMinutes(10))
                        ]);
                    }

                }
            }
        }
        $this->resetFileSystem();
    }

    public function deleteItem()
    {
        $image = $this->item2Delete;
        if (is_dir(Storage::disk('do_image_spaces')->path($image))) {
            Storage::disk('do_image_spaces')->deleteDirectory($image);
        } else {
            Storage::disk('do_image_spaces')->delete($image);
        }
        $this->images = $this->images->map(function($item) {
            return json_decode(json_encode($item), true);
        });
        $this->hydrate();
    }

    public function delete()
    {
        foreach ($this->selected_items as $selected) {
            if (is_dir(Storage::disk('do_image_spaces')->path($selected))) {
                Storage::disk('do_image_spaces')->deleteDirectory($selected);
            } else {
                Storage::disk('do_image_spaces')->delete($selected);
            }
        }
        $this->images = $this->images->map(function($item) {
            return json_decode(json_encode($item), true);
        });
        $this->hydrate();
    }

    public function createFolder($folderName)
    {
        if ($folderName == '' || Storage::disk('do_image_spaces')->exists($this->path.'/'.$folderName)) {
            return;
        }
        Storage::disk('do_image_spaces')->makeDirectory($this->path.'/'.$folderName);
        $this->updateFileSystem();
    }

    public function render()
    {
        if (!$this->multiple) {
            $this->images = $this->images->splice(0, 1);
        }
        return view('livewire.item-images');
    }

    public function orderImages($order): void
    {
        $this->images = $this->images->map(function ($item, $key) use ($order) {
            return $this->images[$order[$key]];
        });
    }

    public function filesUploaded()
    {
        $this->updateFileSystem();
    }

    /*
    *   call this method from another components
    */

    public function renderWithNewImage($filename){

        if(!empty($this->files))
        {
            $cloudUrl = '';

            foreach($this->files as $f)
            {
                if($f->name == $filename) $cloudUrl = $f->url;
            }

            $this->images->push((object)[
                'path' => $filename,
                'url' => $cloudUrl
            ]);

            $this->resetFileSystem();
        }
    }

}
