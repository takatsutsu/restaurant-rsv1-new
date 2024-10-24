<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopAdminRequest extends FormRequest
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
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:50|unique:users',
            'password' => 'required|string|confirmed|min:8|max:200',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前は必須です。',
            'name.max' => 'お名前は最大50文字までです。',
            'email.required' => 'メールアドレスは必須です。',
            'email.email' => '有効なメールアドレスを入力してください。',
            'email.unique' => 'このメールアドレスはすでに使用されています。',
            'email.max' => 'メールアドレスは最大50文字までです。',
            'password.required' => 'パスワードは必須です。',
            'password.confirmed' => 'パスワード確認が一致しません。',
            'password.min' => 'パスワードは少なくとも8文字である必要があります。',
            'password.max' => 'パスワードが無効です。',
        ];
    }
}
