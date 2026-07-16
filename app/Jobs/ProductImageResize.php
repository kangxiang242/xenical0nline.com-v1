<?php

namespace App\Jobs;

use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use QL\QueryList;

class ProductImageResize implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;

    /**
     * Create a new job instance.
     * @param $product
     */
    public function __construct($product)
    {

        $this->product = $product;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $product = $this->product;
        $imageService = app(ImageService::class);
        $file_path = public_path('uploads/'.ltrim($product->img,'/'));
        if(is_file($file_path)){
            foreach (config('image.resizes.goods') as $size){
                $imageService->resize(public_path('uploads/'.ltrim($product->img,'/')),$size);
            }
        }
        $remote_desc_img = QueryList::html($product->describe)->find('img')->attrs('src');
        if($remote_desc_img && $remote_desc_img->isNotEmpty()){
            $remote_desc_img->map(function($value,$key)use (&$describe){
                $local = public_path('uploads/'.$value);
                if(strpos($local,'.gif') !== false){
                }else{
                    $desc_resize = config('image.resizes.goods-desc');
                    foreach($desc_resize as $size){
                        app(ImageService::class)->resize($local,$size);
                    }
                }
            });
        }

    }
}
