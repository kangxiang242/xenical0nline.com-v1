<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Faq extends Model implements Sortable
{
    use SortableTrait;

    protected $sortable = [
        'order_column_name' => 'sort',
        'sort_when_creating' => true,
    ];

    protected $fillable = [
        'uri',
        'title',
        'content',
        'status',
        'sort',
        'questions_gb',
        'answers_gb',
    ];

    public static function getUriLabel()
    {
        $uris = [
            '/' => '首頁',
            'faq' => '減肥疑問解答',
            'bmi' => 'BMI計算機',
            'bmr' => 'BMR計算機',
            'body-fat' => '體脂肪計算機',
            'check' => '訂單查詢',
            'message' => '客服協助',
        ];

        return collect($uris);
    }
}
