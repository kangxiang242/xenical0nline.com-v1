<?php


namespace App\Repositories;


use App\Models\Article;


class NewRepository extends Repository
{
    protected $modelClass = Article::class;


    public function paginate($limit=6,$order_column='sort',$order_by='desc'){

        return $this->model()->where('status',1)->orderBy($order_column,$order_by)->paginate($limit);
    }

    public function newNews($limit = 6){
        return $this->model()->with('cate')->where('status',1)->orderBy('created_at','desc')->limit($limit)->get();
    }

    public function find(int $id){
        return $this->model()->where('status',1)->find($id);
    }


    public function getNextArticle($id,$cate_id){
        $next_article = $this->model()->where('article_cate_id',$cate_id)->where('status',1)->where('id','>',$id)->orderBy('id','asc')->first();

        if(!$next_article){
            $next_article = $this->model()->where('article_cate_id',$cate_id)->where('status',1)->where('id','<',$id)->orderBy('id','asc')->first();
        }
        return $next_article;
    }

    public function getPrevArticle($id,$cate_id){
        $prev_article = $this->model()->where('article_cate_id',$cate_id)->where('status',1)->where('id','<',$id)->orderBy('id','desc')->first();

        if(!$prev_article){

            $prev_article = $this->model()->where('article_cate_id',$cate_id)->where('status',1)->where('id','>',$id)->orderBy('id','desc')->first();
        }

        return $prev_article;
    }
}
