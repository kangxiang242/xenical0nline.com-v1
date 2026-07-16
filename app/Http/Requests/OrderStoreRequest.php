<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderStoreRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>'required',
            'phone'=>'required',
            'email'=>'required|email',
            'city'=>'required',
            'county'=>'required',
            'street'=>'required',
            'goods_id'=>'required',

        ];
    }

    public function messages()
    {
        return [
            'name.required'=>'請填寫收貨人姓名',
            'phone.required'=>'請填寫收貨電話',
            'email.required'=>'請填寫郵箱',
            'city.required'=>'請選擇市/縣',
            'county.required'=>'請選擇地區',
            'street.required'=>'請選擇路段',
            'goods_id.required'=>'商品数据错误'
        ];
    }
}
