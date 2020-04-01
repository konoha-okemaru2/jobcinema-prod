<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequestForSeeker extends FormRequest
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
            'name' => 'required|max:100',
            'name_ruby' => 'required|max:100|kana',
            'email' => 'required|email',
            'phone' => 'nullable',
            'content' => 'required|max:1000'
        ];
    }
}
