<?php


namespace App\Services;


class StoreService
{

    protected $store = [];

    protected $synchroUrl = "http://control.test/seven_eleven/generate";

    public function __construct()
    {
        if(file_exists($this->getJsonPath())){
            $store = file_get_contents($this->getJsonPath());
            $this->store = json_decode($store,true);
        }else{
            $this->synchro();
        }

    }

    /**
     * 获取JSON文件保存路径
     */
    private function getJsonPath(){
        return public_path('store-711.json');
    }

    /**
     * 同步数据
     */
    public function synchro(){
        $store = file_get_contents($this->synchroUrl);
        file_put_contents($this->getJsonPath(),$store);
        $this->store = json_decode($store,true);
    }

    /**
     * 获取市
     * @param bool $is_view_son
     * @return array
     */
    public function getCity($is_view_son=false){


        if($is_view_son == false){
            return $this->removeArrayKey($this->store);
        }else{
            return $this->store;
        }
    }

    /**
     * 根据市县名称 获取镇的数据
     * @param $city_name
     * @param bool $is_view_son
     * @return array
     */
    public function getCounty($city_name,$is_view_son=false){

        $county = [];
        $city = $this->arrayFilterFieldValue($this->store,'name',trim($city_name));
        if(isset($city['son'])){
            $county = $city['son'];
        }
        if($is_view_son == false){
             return $this->removeArrayKey($county);
        }else{
             return $county;
        }

    }

    /**
     * 根据市 、镇 获取路段信息
     * @param $city_name
     * @param $county_name
     * @param bool $is_view_son
     * @return array
     */
    public function getRoad($city_name,$county_name,$is_view_son=false){

        $county = $this->getCounty($city_name,true);

        $roads = [];
        $county_name = trim($county_name);
        $data = $this->arrayFilterFieldValue($county,'name',$county_name);
        if(isset($data['son'])){
            $roads = $data['son'];
        }
        if($is_view_son == false){
            return $this->removeArrayKey($roads,'street_shop');
        }else{
            return $roads;
        }

    }



    /**
     * 根据市 、镇、路段 获取门市信息
     *
     * @param $city_name
     * @param $county_name
     * @param $road_name
     * @return array $shop
     */
    public function getShop($city_name,$county_name,$road_name){
        $roads = $this->getRoad($city_name,$county_name,true);
        $shop = [];
        $road_name = trim($road_name);
        $data = $this->arrayFilterFieldValue($roads,'name',$road_name);
        if(isset($data['street_shop'])){
            $shop = $data['street_shop'];
        }
        return $shop;

    }


    /**
     * 剔除son
     * @param array $arr
     * @param string $key
     * @return array
     */
    private function removeArrayKey($arr,$key='son'){
        return array_map(function($item)use($key){
            unset($item[$key]);
            return  $item;
        },$arr);
    }


    /**
     * 根据指定key和value获取指定数据
     * @param array $data
     * @param string $field
     * @param $value
     * @return array
     */
    public function arrayFilterFieldValue(array $data,  $field, $value)
    {
        $filter = [];

        //循环法
        /*foreach($data as $item){
            if(isset($item[$field]) && $item[$field] == $value){
                $filter = $item;
                break;
            }
        }*/

        //内置函数法
        array_filter($data, function ($row) use ($field, $value,&$filter) {
            if (isset($row[$field]) && $row[$field] == $value) {
                $filter = $row;
                return true;
            }
        });
        return $filter;

    }
}
