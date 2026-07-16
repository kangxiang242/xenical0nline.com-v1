<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Seo;
use App\Services\ConfigService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Rizhou\PageCache\PageCache;

class RandProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rand:product';

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
     * @return int
     */
    public function handle()
    {
        /*$products = Product::get();
        foreach ($products as $product){
            $sort = rand(0,10000);
            $product->sort = $sort;
            $product->save();
            usleep(200000);
        }*/

        $products = Product::get();
        foreach ($products as $product){
            $path = '/product/'.$product->id;
            $title = $product->name.$product->name_en.$product->quantity.($product->quantity == 1?"盒標準裝":"盒優惠裝") .'線上訂購｜'.$product->sub_name;
            if(!Seo::where('path',$path)->first()){
                Seo::create([
                    'path'=>$path,
                    'title'=>$title
                ]);
            }else{
                Seo::where('path',$path)->update([
                    'title'=>$title
                ]);
            }
        }

        foreach(config('global.cache')  as $key){
            Cache::forget($key);
        }
        ConfigService::cache();

        foreach(config('global.cache')  as $key){
            Cache::forget($key);
        }
        ConfigService::cache();

        //app(PageCache::class)->clear();

    }
}
