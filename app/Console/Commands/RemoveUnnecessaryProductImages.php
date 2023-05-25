<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isEmpty;

class RemoveUnnecessaryProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:unnecessary-product-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param string $path
     * @return bool
     */
    public function init(string $path): bool
    {
        return ProductImage::query()->where('path', $path)->exists();
    }

    /**
     * @return bool
     */
    public function handle(): bool
    {

        $storageImages = ProductImage::query()->get();




        foreach ($storageImages as $storageImage) {


            $product = Product::query()->where('id', $storageImage->product_id)->first();
            $basename = basename($storageImage->path);

                var_dump($product->asin);

            if ($product->asin !== null) {
                if (!Storage::disk('do_image_spaces')->exists( $storageImage->path)){
//                    $storageImage->delete();


                }else{
                        Storage::disk('do_image_spaces')->copy($storageImage->path, 'products/'. $product->id .'/'. $product->asin . '/' . $basename);

                        $storageImage->update([
                            'path' => 'products/' . $product->id .'/'. $product->asin . '/' . $basename
                        ]);

                }
            }

            if ($product->asin === null ) {

                if (!Storage::disk('do_image_spaces')->exists( $storageImage->path)){
//                    $storageImage->delete();

                }else{

                    Storage::disk('do_image_spaces')->copy($storageImage->path, 'products/' . $product->id . '/' . $basename);
                    $storageImage->update([
                        'path' => 'products/' . $product->id . '/' . $basename
                    ]);
                }
            }
        }

        $storageImages = Storage::disk('do_image_spaces')->allFiles('products');
        foreach ($storageImages as $storageImage) {
            if (!ProductImage::query()->where('path',$storageImage)->exists()){
                Storage::disk('do_image_spaces')->delete($storageImage);
            }
        }

        $directories = Storage::disk('do_image_spaces')->directories('products');


        foreach ($directories as $directory) {
            $directoryCheckByDirectory = Storage::disk('do_image_spaces')->directories($directory);
            $directoryCheckByFile = Storage::disk('do_image_spaces')->files($directory);

            if (empty($directoryCheckByDirectory) && empty($directoryCheckByFile)) {
                Storage::disk('do_image_spaces')->deleteDirectory($directory);
            }
        }

         return true;
    }
}
