<?php


namespace App\Handlers;


use App\Models\Article;
use App\Models\Product;
use Illuminate\Support\Arr;

class ArticleAnchorsHandler
{
    /**
     * 自动锚文本，a h1 h2 h3 不做锚文本处理
     * @param $str
     * @param $anchors
     * @return string|string[]|null
     */
    public function setAnchors($str,$anchors)
    {

        if(!$anchors){
            return $str;
        }

        $rule = "/<img.*>/";
        //先把img排除掉,并且将其存为一个数组
        preg_match_all($rule, $str, $matches);

        $str_without_alt = preg_replace($rule, 'Its_Just_A_Mark', $str);

        //锚处理
        foreach ($anchors as $anchor) {
            $rule = "/".$anchor['name']."(?!((?!<(a|h1|h2|h3)\b)[\s\S])*<\/(a|h1|h2|h3)>)/";
            $href = '<a target="_blank" href="'.$anchor['url'].'" class="seo-anchor">'.$anchor['name'].'</a>';
            //$str = preg_replace($rule, $href, $str,$anchor['anchor_num']); //这个是匹配数量，可以替换多少次

            $str = preg_replace($rule, $href, $str,0); //无限

        }

        //将img加上去
        foreach ($matches[0] as $alt_content) {
            preg_replace('/Its_Just_A_Mark/',$alt_content,$str,1);
        }

        return $str;
    }

    /**
     * 文章短代码生成
     * @param $str
     * @param null $id
     * @return string|string[]
     */
    public function relatedArticle($str,$id=null){

        $holder = $this->related('article',$str);

        foreach($holder as $item){

            $rule = $item['full'];

            $html = "<div class='article-related'><h2>為您推薦以下文章：</h2><div class='article-related-list'>";

            if($item['ids'] == 0){
                if($id){
                    $article = Article::inRandomOrder()->where('id','<>',$id)->limit(3)->get();
                }else{
                    $article = Article::inRandomOrder()->limit(3)->get();
                }

            }else{
                $ids = explode(',',$item['ids']);
                $article = Article::whereIn('id',$ids)->get();
            }
            foreach($article as $art){
                $html .= "<p><a target='_blank' href='".url('news/'.$art->id)."'>".$art->title."</a></p>";
            }
            $html .= "</div></div>";
            $str = str_replace($rule, $html, $str);

        }
        return $str;

    }

    /**
     * 产品短代码生成
     * @param $str
     * @return string|string[]
     */
    public function relatedProduct($str){
        $holder = $this->related('product',$str);

        foreach($holder as $item){

            $rule = $item['full'];
            $id = (integer)$item['ids'];

            if($id <= 0){
                $product = Product::where('status',1)->inRandomOrder()->first();
            }else{

                $product = Product::find($item['ids']);
            }

            $html = view('render.article.product',compact('product'))->render();

            $str = str_replace($rule, trim($html), $str);


        }
        return $str;
    }

    /**
     * 正则匹配短代码
     * @param $label
     * @param $content
     * @return array
     */
    private function related($label,$content){
        $rule = "/\[\*\*".$label.":(.*?)\*\*\]/i";
        preg_match_all($rule, $content, $matches);

        $holder = [];
        foreach(Arr::get($matches,0) as $k=>$item){
            $holder[] = [
                'full'=>$item,
                'ids'=>Arr::get($matches,"1.".$k),
            ];
        }
        return $holder;

    }


}
