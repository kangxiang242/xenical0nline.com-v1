<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Observer;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ObserverController extends Controller
{
    /**
     * 服务端 metadata allowlist — 与前端 tracker.js 的 METADATA_ALLOWED 保持同步
     */
    const METADATA_ALLOWED = [
        'field', 'action', 'product_id', 'href', 'element', 'error_code',
        'depth_percent', 'milestone', 'scroll_target', 'duration_seconds', 'duration_sec',
        'max_scroll_percent', 'exit_type', 'next_uri', 'checkout_outcome',
        'last_field', 'fields_touched', 'submit_clicked', 'order_no', 'amount',
        'product_name', 'price', 'bmi', 'recommend_product_id', 'redirect',
        'changed', 'section_label', 'title', 'visibility_ratio_peak',
        'fcp_ms', 'lcp_ms', 'inp_ms', 'ttfb_ms', 'lcp_tag',
        'engagement_type', 'duration_before_click_sec', 'max_scroll_before_click_percent',
        'blocks_seen', 'last_section_id', 'checkout_duration_sec', 'calc_type',
        'slide_index', 'percent', 'max_read_progress', 'expanded', 'faq_id',
        'status', 'step', 'has_value', 'filled', 'article_id', 'cms_uri',
        'page_index', 'session_path', 'landing_page', 'heading_id',
    ];

    public function store(Request $request)
    {
        // 兼容旧格式：新字段存在时用新字段，否则回退到旧的 uri + explain + event
        $rules = ['uri' => 'required'];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response('error', 400);
        }

        try {
            $data = [
                'host'           => $request->getHost(),
                'uri'            => $request->input('uri'),
                'ip'             => VehicleService::IP(),
                'ipcountry'      => $request->header('cf-ipcountry'),
                'user_agent'     => VehicleService::userAgent(),
                'referer'        => $request->input('referer'),
                'referer_original' => $request->input('original_referer'),
            ];

            // 兼容旧字段
            $data['explain']   = $request->input('explain', $request->input('label', ''));
            $data['event']     = $request->input('event', $request->input('event_type', ''));

            // 新字段
            $data['event_type']   = $request->input('event_type', '');
            $data['event_name']   = $request->input('event_name', '');
            $data['section']      = $request->input('section', '');
            $data['device']       = $request->input('device', 'web');
            $data['session_id']   = $request->input('session_id', '');
            $data['visitor_id']   = $request->input('visitor_id', '');
            $data['page_view_id'] = $request->input('page_view_id', '');
            $data['page_type']    = $request->input('page_type', '');
            $data['utm_source']   = $request->input('utm_source', '');
            $data['utm_medium']   = $request->input('utm_medium', '');
            $data['utm_campaign'] = $request->input('utm_campaign', '');

            // metadata JSON 解析 + allowlist 校验
            $data['metadata'] = $this->parseAndFilterMetadata($request->input('metadata', ''));

            Observer::create($data);

            return response('success', 200);
        } catch (\Exception $exception) {
            Log::warning('ObserverController.store failed', [
                'error' => $exception->getMessage(),
                'uri'   => $request->input('uri'),
            ]);
            return response('error', 500);
        }
    }

    /**
     * 解析 metadata JSON 字符串，只保留 allowlist 中的字段
     */
    private function parseAndFilterMetadata($raw)
    {
        if (empty($raw) || !is_string($raw)) {
            return null;
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return null;
        }

        $allowedMap = array_flip(self::METADATA_ALLOWED);

        $filtered = array_intersect_key($decoded, $allowedMap);

        return empty($filtered) ? null : $filtered;
    }
}
