<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return  true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'shop_name'=> 'required|string|max:50',
            'shop_explanation' => 'required|string|max:250',
            'genre_id' => 'required',
            'area_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'shop_name.required' => '店舗名は必須です。',
            'shop_name.max' => '店舗名は最大50文字までです。',
            'shop_explanation.required' => '店舗説明は必須です。',
            'shop_explanation.max' => '店舗説明は最大250文字までです。',
            'genre_id.required' => 'ジャンルは必須です。',
            'area_id.required' => 'エリアは必須です。',
        ];
    }
}
