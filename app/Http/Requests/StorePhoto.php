<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePhoto extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * ユーザーがこのリクエストの権限を持っているかを判断する
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
        // validationルール
        // 必須 & フィールドがファイル & 拡張子がjpg,jpeg,png,gif
        return [
            'photo' => 'required|file|mimes:jpg,jpeg,png,gif'
        ];
    }
}
