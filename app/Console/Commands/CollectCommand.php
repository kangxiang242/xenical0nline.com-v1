<?php

namespace App\Console\Commands;


use App\Models\AdminRands;
use App\Models\CollectGoods;
use App\Models\Product;
use App\Models\ProductAdded;
use App\Services\CollectService;
use App\Services\SoaService;
use Illuminate\Console\Command;

class CollectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:goods';

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
        /*$this->info('開始采集商品Code~');
        $CollectService = new CollectService();
        $CollectService->handle();*/

        /*$this->info('開始采集商品信息~');
        $CollectService = new CollectService();
        $goods = CollectGoods::get();
        foreach($goods as $item){
            try {
                $CollectService->getGoodsInfo($item);
                $item->insert_goods = 1;
                $item->insert_error = '';
                $item->save();
            }catch (\Exception $exception){
                $item->insert_error = $exception->getMessage();
                $item->insert_goods = 0;
                $item->save();
            }
            sleep(1);
        }
        $this->info('采集完成~');*/

        /*$this->info('同步缺貨狀態~');
        $goods = CollectGoods::get();
        foreach($goods as $item){
            Product::where('collect_code',$item->goods_code)->update(['is_stock'=>$item->is_stock]);
            sleep(1);
        }
        $this->info('同步完成~');*/

        /*$product = Product::get();
        $goods_ids = [];
        foreach($product as $item){
            $img = public_path('uploads'.$item->img);
            if(!file_exists($img)){
                $goods_ids[] = $item->id;
            }
        }
        echo implode(',',$goods_ids);exit;*/

        //同步架構數據
        /*$added_ids = \App\Models\Product::where('added_price','>',0)->pluck('id');
        $products = \App\Models\Product::get();
        foreach ($products as $item){

            if ($item->added){
                $create = [];
                foreach ($added_ids as $id){
                    $create[] = [
                        'product_id'=>$item->id,
                        'added_product_id'=>$id
                    ];
                }
                ProductAdded::insert($create);
            }else{
                continue;
            }

        }*/

    }
}
