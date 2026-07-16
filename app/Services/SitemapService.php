<?php


namespace App\Services;


use App\Models\ArticleCate;
use App\Models\Cate;
use App\Models\Product;


class SitemapService
{
    private $xml = [];

    private $last_mod;

    public function __construct()
    {
        $this->last_mod = date('Y-m-d');
    }


    public function generate(){
        $this->header();
        $this->startUrlSet();
        $this->home();
        $this->article();
        $this->product();
        $this->page();
        $this->endUrlSet();

        $sitemap = join("\n", $this->xml);
        return $sitemap;
        file_put_contents(public_path('sitemap.xml'),$sitemap);
    }

    /**
     * Sitemap页头
     */
    public function header(){
        $this->xml[] = '<?xml version="1.0" encoding="utf-8"?>';

    }

    public function startUrlSet(){
        $this->xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    }

    public function endUrlSet(){
        $this->xml[] = '</urlset>';
    }

    /**
     * 首页
     */
    public function home(){
        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('/')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = '    <changefreq>daily</changefreq>';
        $this->xml[] = '    <priority>1.0</priority>';
        $this->xml[] = '  </url>';
    }

    /**
     * 资讯公告
     */
    public function article(){
        $article = ArticleCate::with(['article'=>function($query){
            $query->where('status',1);
        }])->get();

        foreach($article as $item){
            $uri = $item->uri?url($item->uri):url('news');
            $this->xml[] = '  <url>';
            $this->xml[] = "    <loc>".$uri."</loc>";
            $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
            $this->xml[] = "    <changefreq>daily</changefreq>";
            $this->xml[] = '    <priority>0.8</priority>';
            $this->xml[] = "  </url>";

            foreach($item->article as $vv){
                $this->xml[] = '  <url>';
                $this->xml[] = "    <loc>".url($uri.'/'.$vv->id)."</loc>";
                $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
                $this->xml[] = "    <changefreq>daily</changefreq>";
                $this->xml[] = '    <priority>0.8</priority>';
                $this->xml[] = "  </url>";
            }

        }

    }

    /**
     * 产品
     */
    public function product(){
        $product = Product::where('status',1)->select(['id'])->get();
        foreach($product as $item){
            $this->xml[] = '  <url>';
            $this->xml[] = "    <loc>".url('goods/'.$item->id)."</loc>";
            $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
            $this->xml[] = "    <changefreq>daily</changefreq>";
            $this->xml[] = '    <priority>0.8</priority>';
            $this->xml[] = "  </url>";
        }
    }

    public function page(){
        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('about')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";

        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('bmi')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";

        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('guide')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";

        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('payment-delivery')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";

        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('after-sales')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";


        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('check')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";

        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('message')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";

        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('bmr')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";

        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('body-fat')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";

        $this->xml[] = '  <url>';
        $this->xml[] = "    <loc>".url('privacy')."</loc>";
        $this->xml[] = "    <lastmod>{$this->last_mod}</lastmod>";
        $this->xml[] = "    <changefreq>daily</changefreq>";
        $this->xml[] = '    <priority>0.8</priority>';
        $this->xml[] = "  </url>";
    }



}
