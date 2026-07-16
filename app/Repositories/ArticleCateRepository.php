<?php


namespace App\Repositories;



use App\Models\ArticleCate;

class ArticleCateRepository extends Repository
{
    protected $modelClass = ArticleCate::class;


    public function getAll(){

        return $this->cache(config('global.cache.article_cate'),function (){
            return $this->model()->where('status',1)->orderBy('sort','desc')->get();
        });

    }
}
