<?php


namespace App\Services;

use DOMDocument;
use DOMXPath;

class ArticleService
{
    public static function readingMinuteCount(int $number){
        $minute_number = 300; //每分钟阅读多少字
        $min = $number/$minute_number;

        return self::readingFormat($min);
    }

    public static function readingFormat($min){
        $s = 60/(1/$min);
        $m = $s/60;
        if($m<1){
            return ceil($s)."秒鐘";
        }else{
            return round($m,0)."分鐘";
        }
    }

    public function parseContentWithToc(string $html): array
    {
        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $wrapped = '<div id="__article-root">' . $html . '</div>';
        $dom->loadHTML(
            mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//h2 | //h3');
        $images = $xpath->query('//img');

        foreach ($images as $image) {
            if (!$image->hasAttribute('loading')) {
                $image->setAttribute('loading', 'lazy');
            }

            if (!$image->hasAttribute('decoding')) {
                $image->setAttribute('decoding', 'async');
            }
        }

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

        $firstParagraph = null;
        $firstP = $xpath->query('(//p)[1]')->item(0);
        if ($firstP) {
            $firstP->setAttribute('id', 'lead');
            $firstParagraph = $dom->saveHTML($firstP);
            $parent = $firstP->parentNode;
            $parent->removeChild($firstP);

            $content = '';
            foreach ($parent->childNodes as $child) {
                $content .= $dom->saveHTML($child);
            }
            array_unshift($toc, [
                'id' => 'lead',
                'title' => '導讀',
                'children' => [],
            ]);
        } else {
            $content = '';
            $root = $dom->getElementById('__article-root');
            if ($root) {
                foreach ($root->childNodes as $child) {
                    $content .= $dom->saveHTML($child);
                }
            } else {
                $content = $dom->saveHTML();
            }
        }

        return [
            'content' => $content,
            'toc' => $toc,
            'first_paragraph' => $firstParagraph,
        ];
    }

    protected function slug(string $text): string
    {
        $slug = mb_strtolower($text);
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $slug);
        return trim($slug, '-');
    }
}
