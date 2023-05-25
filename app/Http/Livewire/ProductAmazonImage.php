<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Http\Helpers\Scrapper\AmazonOriginScrapper;
use Illuminate\Support\Facades\Storage;

class ProductAmazonImage extends Component
{
    public string $imageStatus;
    public string $asin = '';
    public string $path = '';

    public function sendRequest() : void
    {
        $this->imageStatus = 'Getting an image...be patient';

        if(!empty($this->asin))
        {
            $scrapper = new AmazonOriginScrapper([$this->asin]);
            $items = $scrapper->getItems();

            if(!isset($items['code']))
            {
                $this->path = $items[$this->asin]->getImages()->getPrimary()->getLarge()->getUrl();

                if(!empty($this->path))
                {
                    $filename = basename($this->path);

                    //check directory for product
                    if(Storage::disk('do_image_spaces')->missing('products/'.$this->asin))
                    {
                        Storage::disk('do_image_spaces')->makeDirectory('products/'.$this->asin);
                    }

                    if(Storage::disk('do_image_spaces')->missing('products/'.$this->asin.'/'.$filename))
                    {
                        $contents = file_get_contents($this->path);

                        try{

                            Storage::disk('do_image_spaces')->put('products/'.$this->asin.'/'.$filename, $contents, 'public');
                            $this->imageStatus = 'Image uploaded to the cloud successfully, please save the product!';

                            $this->emit('renderWithNewImage', 'products/'.$this->asin.'/'.$filename);

                        }catch(Exception $e){

                            $this->imageStatus = $e->getMessage();
                        }

                    }else $this->imageStatus = 'Error: File products/'.$this->asin.'/'.$filename.' already exists in the gallery';

                }else $this->imageStatus = 'Error: Large image not found by this ASIN '.$this->asin;

            }else $this->imageStatus = $items['message'];

        }else $this->imageStatus = 'Error: Can not find product ASIN';
    }


    public function render()
    {
        return view('livewire.product-amazon-image');
    }


}
