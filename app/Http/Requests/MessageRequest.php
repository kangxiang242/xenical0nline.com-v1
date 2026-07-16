<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends BaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>'required',
            'email'=>'required|email',
            'content'=>'required',
        ];
    }

    public function messages(){
        return [
            'name.required'=>'請填寫您的昵稱',
            'email.required'=>'請填寫您的郵箱',
            'email.email'=>'請填寫正確的郵箱',
            'content.required'=>'請填寫您的意見或建議'
        ];
    }
}
