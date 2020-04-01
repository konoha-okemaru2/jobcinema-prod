<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequestForEmployer extends FormRequest
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
            'category' => 'required',
            'cname' => 'required|max:100',
            'cname_katakana' => 'nullable|max:100|Katakana',
            'e_name' => 'required|max:100',
            'e_name_ruby' => 'nullable|max:100|kana',
            'email' => 'required|email|max:100',
            'phone' => 'nullable',
            'content' => 'required|max:1000'
        ];
    }
}
