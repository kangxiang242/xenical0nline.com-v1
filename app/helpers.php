<?php

function asset_upload($path='',$default=null){

    return asset('uploads/'.$path);

}

function img_field($src,$blur=null,$size=null,$alt='',$class=''){
    $src = '/'.ltrim($src,'/');

    $pathinfo = pathinfo($src);
    $extension = \Illuminate\Support\Arr::get($pathinfo,'extension');
    $filename = \Illuminate\Support\Arr::get($pathinfo,'filename');
    $dirname = \Illuminate\Support\Arr::get($pathinfo,'dirname');

    if(!is_null($size)){
        $resizeName = $filename.'-'.$size.'.'.$extension;
        $size_path = $dirname.'/'.$resizeName;
    }else{
        $size_path = $src;
    }

    if(!is_null($blur)){
        $blurName = $filename.'-'.$blur.'.'.$extension;
        $blur_path = $dirname.'/'.$blurName;
    }else{
        $blur_path = $src;
    }

    if(config('global.image_url')){
        $size_path = config('global.image_url').$size_path;
        $blur_path = config('global.image_url').$blur_path;
    }


    return '<img class="lazyload '.$class.'" src="'.$blur_path.'" data-src="'.$size_path.'" alt="'.$alt.'">';
}

function get_img_resize($path,$size){

    $pathinfo = pathinfo($path);

    $extension = \Illuminate\Support\Arr::get($pathinfo,'extension');
    $filename = \Illuminate\Support\Arr::get($pathinfo,'filename');
    $dirname = \Illuminate\Support\Arr::get($pathinfo,'dirname');
    if($extension && $filename){
        $resizeName = $filename.'-'.$size.'.'.$extension;
        $resize_image_path = public_path('uploads'.$dirname.'/'.$resizeName);

        if(!is_file($resize_image_path)){
            $img = Intervention\Image\Facades\Image::make(public_path('uploads'.$path))->resize($size, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            $saveName = $img->filename.'-'.$size.'.'.$img->extension;
            $img->save($img->dirname.'/'.$saveName);
        }
        return $dirname.'/'.$resizeName;

    }


    return $path;
}

function convert_content_image_urls($content)
{
    if (empty($content)) {
        return $content;
    }

    // Get the current app URL domain to convert to relative paths
    $currentHost = config('app.url');
    if ($currentHost) {
        $parsed = parse_url($currentHost);
        $domain = $parsed['scheme'] . '://' . $parsed['host'];
        if (isset($parsed['port'])) {
            $domain .= ':' . $parsed['port'];
        }

        // Replace domain URLs with relative paths for /uploads/ images
        $content = str_replace($domain . '/uploads/', '/uploads/', $content);
    }

    // Also handle common local/dev server URLs
    $content = preg_replace('/https?:\/\/[^\/]*192\.168\.\d+\.\d+[^\/]*\/?uploads\//', '/uploads/', $content);

    return $content;
}

/**
 * 若 HTML 最外層為單一 <div class="…"> 且 class 含指定 token（完整詞，非子字串），剝掉該層。
 * 用於後台富文本多包一層（例如 div.art）與前台 .editor 重複時。
 */
function unwrap_outer_div_by_class(?string $html, string $classToken): string
{
    if ($html === null || trim($html) === '') {
        return (string) $html;
    }

    $html = trim($html);
    $classToken = trim($classToken);
    if ($classToken === '') {
        return $html;
    }

    libxml_use_internal_errors(true);

    for ($depth = 0; $depth < 5; $depth++) {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $wrapped = '<div id="__unwrap-page-root">' . $html . '</div>';
        $dom->loadHTML(
            mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $root = $dom->getElementById('__unwrap-page-root');
        if (!$root) {
            break;
        }

        $elementChildren = [];
        foreach ($root->childNodes as $child) {
            if ($child->nodeType === XML_TEXT_NODE) {
                if (trim($child->textContent) !== '') {
                    return $html;
                }

                continue;
            }
            if ($child->nodeType === XML_ELEMENT_NODE) {
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
        if (!in_array($classToken, $classes, true)) {
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

function replaceCodeHtml($html,$size=68){

    try {
        preg_match_all('/<img[^>]*?src="([^"]*?)"[^>]*?>/i',$html,$match);
        if(isset($match[1]) && $match[1]){
            foreach($match[1] as $v){
                $info = pathinfo($v);
                if($info['extension'] != 'gif'){
                    $new_path = $info['dirname'].'/'.$info['filename'].'-'.$size.'.'.$info['extension'];
                    $replace = asset_upload($new_path).'" data-src="'.asset_upload($v).'';
                    $html = str_replace($v,$replace,$html);

                }else{
                    $replace = asset_upload($v);
                    $html = str_replace($v,$replace,$html);

                }
            }
        }
    }catch (\Exception $exception){
    }
    return $html;
}

function array_get($array,$key,$default=null){
    return \Illuminate\Support\Arr::get($array,$key,$default);
}

if (! function_exists('template')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function template($view = null, $data = [], $mergeData = []){
        $device = \App\Handlers\DeviceTypeHandlers::isMobile() ? 'mobile' : 'web';

        return view($device.'.'.$view, $data, $mergeData);
    }
}



function is_mobile(){
    return \App\Handlers\DeviceTypeHandlers::isMobile();
}

if (! function_exists('is_googlebot')) {
    function is_googlebot()
    {
        $user_agent = request()->header('user-agent');

        return preg_match('/(Googlebot|Chrome-Lighthouse)/i', $user_agent);
    }
}

if (! function_exists('vite_tags')) {
    function vite_tags($entrypoints = ['resources/js/vite-app.js']){
        $entrypoints = (array) $entrypoints;
        $hotFile = public_path('hot');
        $devServer = null;

        if (is_file($hotFile)) {
            $devServer = rtrim(trim(file_get_contents($hotFile)), '/');
        } elseif (filled(env('VITE_DEV_SERVER_URL'))) {
            // 勿用「埠號可連線」推測 Vite：5173 上常見其他程式，會誤載 dev 標籤而得到 404。
            $devServer = rtrim((string) env('VITE_DEV_SERVER_URL'), '/');
        }

        if ($devServer) {
            $tags = [
                '<script type="module" src="'.e($devServer.'/@vite/client').'"></script>',
            ];

            foreach ($entrypoints as $entrypoint) {
                $tags[] = '<script type="module" src="'.e($devServer.'/'.ltrim($entrypoint, '/')).'"></script>';
            }

            return implode("\n", $tags);
        }

        $manifestPath = public_path('build/manifest.json');

        if (! is_file($manifestPath)) {
            return '';
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        $tags = [];

        foreach ($entrypoints as $entrypoint) {
            if (! isset($manifest[$entrypoint])) {
                continue;
            }

            $asset = $manifest[$entrypoint];

            foreach ($asset['css'] ?? [] as $css) {
                $tags[] = '<link rel="stylesheet" href="'.e(asset('build/'.$css)).'">';
            }

            if (isset($asset['file'])) {
                $tags[] = '<script type="module" src="'.e(asset('build/'.$asset['file'])).'"></script>';
            }
        }

        return implode("\n", $tags);
    }
}

if (! function_exists('release_token')) {
    function release_token(): ?string
    {
        return \App\Models\Release::whereNotNull('token')
            ->latest('deployed_at')
            ->value('token');
    }
}

if (! function_exists('release_asset')) {
    function release_asset(string $path): string
    {
        $token = release_token();
        $version = config('app.asset_version', '1.0.0');
        $cacheBuster = $token ?: $version;
        $prefix = asset('/static');
        $sep = str_contains($path, '?') ? '&' : '?';
        return "{$prefix}/{$path}{$sep}{$cacheBuster}";
    }
}
