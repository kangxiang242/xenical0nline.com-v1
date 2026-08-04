<?php

namespace App\Http\Controllers\Web;

use App\Handlers\ArticleAnchorsHandler;
use App\Http\Controllers\Controller;
use App\Models\Anchor;
use App\Models\ArticleCate;
use App\Models\Tag;
use App\Repositories\ArticleCateRepository;
use App\Repositories\ArticleTagRepository;
use App\Repositories\NewRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    private $newRepository;

    public function __construct(NewRepository $newRepository)
    {
        $this->newRepository = $newRepository;
    }

    public function index(Request $request, $uri = null)
    {
        $isEffectMode = $request->is('effect*');

        // Get category if URI provided
        $cate = null;
        if ($uri) {
            $cate = app(ArticleCateRepository::class)->getAll()
                ->where('uri', $uri)->where('status', 1)
                ->first();
        }

        // Get news query
        $query = $this->newRepository->model()->where('status', 1);
        if ($cate) {
            $query = $query->where('article_cate_id', $cate->id);
        }
        $news = $query->orderBy('sort', 'desc')->paginate(10);

        // Get topics/tags with articles for the page (for tag linkage)
        $topicsTags = $cate
            ? app(ArticleTagRepository::class)->getTagsWithArticlesByCateId($cate->id, 3, 3)
            : [];

        // Get top news for display
        $top = $this->newRepository->model()
            ->where('status', 1)
            ->orderBy('read_num', 'desc')
            ->limit(6)
            ->get();

        if ($cate) {
            // 本站特有：分類文章列表（/news/{uri}）保留原站視圖
            return template('news.index-cate', compact(
                'news',
                'cate',
                'topicsTags',
                'top',
                'isEffectMode'
            ));
        }

        return template('news.index', compact(
            'news',
            'cate',
            'topicsTags',
            'top',
            'isEffectMode'
        ));
    }


    public function show($uri, $id)
    {
        $cate = app(ArticleCateRepository::class)->getAll()
            ->where('uri', $uri)->where('status', 1)
            ->first();

        if (!$cate) {
            abort(404);
        }

        $news = $this->newRepository
            ->model()
            ->where('article_cate_id', $cate->id)
            ->where('id', intval($id))
            ->where('status', 1)
            ->first();

        if (!$news) {
            abort(404);
        }

        $next = $this->newRepository->getNextArticle(intval($id), $cate->id);
        $prev = $this->newRepository->getPrevArticle(intval($id), $cate->id);
        // Get top articles by read count
        $top = $this->newRepository->model()
            ->where('status', 1)
            ->where('id', '!=', intval($id))
            ->orderBy('read_num', 'desc')
            ->limit(6)
            ->with('cate')
            ->get();
        $newNews = $this->newRepository->newNews(5);

        // Process content with anchors (guard against null content)
        $rawContent = $news->content ?? '';
        // mode=1: 代码上传文章，从解压的 HTML 文件中读取内容
        if (empty($rawContent) && $news->mode == 1 && $news->html_file) {
            $htmlKey = str_replace('.zip', '', $news->html_file);
            $htmlBaseUrl = '/uploads/' . $htmlKey . '/';
            $htmlPath = public_path('uploads/' . $htmlKey . '/index.html');
            if (file_exists($htmlPath)) {
                $rawContent = file_get_contents($htmlPath);
                $htmlDir = dirname($htmlPath);
                // 提取 head 中的 CSS 文件，读入内容作为内联样式
                $inlineStyles = '';
                if (preg_match('/<head[^>]*>([\s\S]*?)<\/head>/i', $rawContent, $headMatch)) {
                    // 提取 body 内容
                    if (preg_match('/<body[^>]*>([\s\S]*?)<\/body>/i', $rawContent, $bodyMatch)) {
                        $rawContent = $bodyMatch[1];
                    }
                    $headContent = $headMatch[1];
                    if (preg_match_all('/<link[^>]*href=("|\')([^"\']+\.css[^"\']*)\1[^>]*>/i', $headContent, $cssMatches)) {
                        foreach ($cssMatches[2] as $cssHref) {
                            $cssRelPath = str_replace('./', '', $cssHref);
                            // 去掉 query string (?1393)
                            $cssRelPath = preg_replace('/\?.*$/', '', $cssRelPath);
                            $cssFilePath = $htmlDir . '/' . $cssRelPath;
                            if (file_exists($cssFilePath)) {
                                $cssContent = file_get_contents($cssFilePath);
                                // 修复 CSS 中的相对路径（背景图、字体等）
                                $cssContent = preg_replace('/url\((\"|\'?)(?!https?:\/\/|data:)([^"\'\)]+)/i',
                                    'url($1' . $htmlBaseUrl . '$2', $cssContent);
                                $inlineStyles .= '<style>' . $cssContent . '</style>';
                            }
                        }
                    }
                }
                // 在 body 内容前插入内联样式
                if (!empty($inlineStyles)) {
                    $rawContent = $inlineStyles . $rawContent;
                }
                // 修复 body 中的相对路径（img），指向正确的解压目录
                $rawContent = preg_replace('/\b(src)=("|\')(?!https?:\/\/|\/\/|data:|#)([^"\']+)/i', 
                    '$1=$2' . $htmlBaseUrl . '$3', $rawContent);
            }
        }
        $content = app(ArticleAnchorsHandler::class)->setAnchors($rawContent, Anchor::get()->toArray());
        $content = app(ArticleAnchorsHandler::class)->relatedArticle($content, $news->id);

        // Convert absolute image URLs to relative paths
        $content = convert_content_image_urls($content);

        // Parse TOC and inject IDs into headings
        $parsed = $this->parseContentWithToc($content);
        $content = $this->unwrapOuterArticleContainer($parsed['content']);
        $toc = $parsed['toc'];
        $firstParagraph = null;

        // Get custom CSS for this article
        $customCss = $news->custom_css ?? null;

        // Get tags for this article
        $articleTags = app(ArticleTagRepository::class)->getByArticleId($news->id);

        return template('news.show-cate', compact(
            'news',
            'content',
            'toc',
            'firstParagraph',
            'next',
            'prev',
            'top',
            'newNews',
            'customCss',
            'articleTags'
        ));
    }

    /**
     * Show article by ID only (no category URI)
     */
    public function showById($id)
    {
        $news = $this->newRepository->find(intval($id));
        // 如果 status=0 但 mode=1（代码上传），仍然显示
        if (!$news) {
            $news = $this->newRepository->model()->where('id', intval($id))->where('mode', 1)->first();
        }
        if (!$news) {
            abort(404);
        }

        $cate = $news->cate;

        $next = $this->newRepository->getNextArticle(intval($id), $news->article_cate_id);
        $prev = $this->newRepository->getPrevArticle(intval($id), $news->article_cate_id);
        $top = $this->newRepository->model()
            ->where('status', 1)
            ->where('id', '!=', intval($id))
            ->orderBy('read_num', 'desc')
            ->limit(6)
            ->with('cate')
            ->get();
        $newNews = $this->newRepository->newNews(5);

        // Process content with anchors (guard against null content)
        $rawContent = $news->content ?? '';
        // mode=1: 代码上传文章，从解压的 HTML 文件中读取内容
        if (empty($rawContent) && $news->mode == 1 && $news->html_file) {
            $htmlKey = str_replace('.zip', '', $news->html_file);
            $htmlBaseUrl = '/uploads/' . $htmlKey . '/';
            $htmlPath = public_path('uploads/' . $htmlKey . '/index.html');
            if (file_exists($htmlPath)) {
                $rawContent = file_get_contents($htmlPath);
                $htmlDir = dirname($htmlPath);
                // 提取 head 中的 CSS 文件，读入内容作为内联样式
                $inlineStyles = '';
                if (preg_match('/<head[^>]*>([\s\S]*?)<\/head>/i', $rawContent, $headMatch)) {
                    // 提取 body 内容
                    if (preg_match('/<body[^>]*>([\s\S]*?)<\/body>/i', $rawContent, $bodyMatch)) {
                        $rawContent = $bodyMatch[1];
                    }
                    $headContent = $headMatch[1];
                    if (preg_match_all('/<link[^>]*href=("|\')([^"\']+\.css[^"\']*)\1[^>]*>/i', $headContent, $cssMatches)) {
                        foreach ($cssMatches[2] as $cssHref) {
                            $cssRelPath = str_replace('./', '', $cssHref);
                            // 去掉 query string (?1393)
                            $cssRelPath = preg_replace('/\?.*$/', '', $cssRelPath);
                            $cssFilePath = $htmlDir . '/' . $cssRelPath;
                            if (file_exists($cssFilePath)) {
                                $cssContent = file_get_contents($cssFilePath);
                                // 修复 CSS 中的相对路径（背景图、字体等）
                                $cssContent = preg_replace('/url\((\"|\'?)(?!https?:\/\/|data:)([^"\'\)]+)/i',
                                    'url($1' . $htmlBaseUrl . '$2', $cssContent);
                                $inlineStyles .= '<style>' . $cssContent . '</style>';
                            }
                        }
                    }
                }
                // 在 body 内容前插入内联样式
                if (!empty($inlineStyles)) {
                    $rawContent = $inlineStyles . $rawContent;
                }
                // 修复 body 中的相对路径（img），指向正确的解压目录
                $rawContent = preg_replace('/\b(src)=("|\')(?!https?:\/\/|\/\/|data:|#)([^"\']+)/i', 
                    '$1=$2' . $htmlBaseUrl . '$3', $rawContent);
            }
        }
        $content = app(ArticleAnchorsHandler::class)->setAnchors($rawContent, Anchor::get()->toArray());
        $content = app(ArticleAnchorsHandler::class)->relatedArticle($content, $news->id);

        // Convert absolute image URLs to relative paths
        $content = convert_content_image_urls($content);

        // Parse TOC and inject IDs into headings
        $parsed = $this->parseContentWithToc($content);
        $content = $this->unwrapOuterArticleContainer($parsed['content']);
        $toc = $parsed['toc'];
        $firstParagraph = null;

        // Get custom CSS for this article
        $customCss = $news->custom_css ?? null;

        // Get tags for this article
        $articleTags = app(ArticleTagRepository::class)->getByArticleId($news->id);

        return template('news.show', compact(
            'news',
            'content',
            'toc',
            'firstParagraph',
            'next',
            'prev',
            'top',
            'newNews',
            'customCss',
            'articleTags'
        ));
    }

    /**
     * 版型已在 Blade 用 section.article-content.article-container 包住正文；
     * 若後台內容再帶一層 div.article-container，輸出會重複，這裡剝掉最外層（可連剝數層）。
     */
    private function unwrapOuterArticleContainer(string $html): string
    {
        $html = trim($html);
        if ($html === '') {
            return $html;
        }

        libxml_use_internal_errors(true);

        for ($depth = 0; $depth < 5; $depth++) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $wrapped = '<div id="__unwrap-root">' . $html . '</div>';
            $dom->loadHTML(
                mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'),
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );

            $root = $dom->getElementById('__unwrap-root');
            if (!$root) {
                break;
            }

            $elementChildren = [];
            foreach ($root->childNodes as $child) {
                if ($child->nodeType === \XML_TEXT_NODE) {
                    if (trim($child->textContent) !== '') {
                        return $html;
                    }

                    continue;
                }
                if ($child->nodeType === \XML_ELEMENT_NODE) {
                    $elementChildren[] = $child;
                }
            }

            if (count($elementChildren) !== 1) {
                break;
            }

            $only = $elementChildren[0];
            if (strtolower($only->nodeName) !== 'div' || !$only->hasAttribute('class')) {
                break;
            }

            $classes = preg_split('/\s+/', trim($only->getAttribute('class')), -1, PREG_SPLIT_NO_EMPTY);
            if (!in_array('article-container', $classes, true)) {
                break;
            }

            $inner = '';
            foreach ($only->childNodes as $child) {
                $inner .= $dom->saveHTML($child);
            }
            $html = trim($inner);
        }

        return $html;
    }

    /**
     * Parse table of contents and inject IDs into headings
     */
    private function parseContentWithToc(string $html): array
    {
        libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $wrapped = '<div id="__article-root">' . $html . '</div>';
        $dom->loadHTML(
            mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//h2 | //h3');

        $toc = [];
        $currentH2Index = -1;

        foreach ($nodes as $index => $node) {
            $tag = strtolower($node->nodeName);
            $text = trim($node->textContent);

            if (!$node->hasAttribute('id')) {
                $id = $this->slug($text) ?: 'section-' . $index;
                $node->setAttribute('id', $id);
            } else {
                $id = $node->getAttribute('id');
            }

            if ($tag === 'h2') {
                $toc[] = [
                    'id' => $id,
                    'title' => $text,
                    'children' => [],
                ];
                $currentH2Index++;
            }

            if ($tag === 'h3' && $currentH2Index >= 0) {
                $toc[$currentH2Index]['children'][] = [
                    'id' => $id,
                    'title' => $text,
                ];
            }
        }

        // Get content with injected IDs
        $content = '';
        $root = $dom->getElementById('__article-root');
        if ($root) {
            foreach ($root->childNodes as $child) {
                $content .= $dom->saveHTML($child);
            }
        } else {
            $content = $dom->saveHTML();
        }

        return [
            'content' => $content,
            'toc' => $toc,
        ];
    }

    protected function slug(string $text): string
    {
        $slug = mb_strtolower($text);
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $slug);
        return trim($slug, '-');
    }

}
