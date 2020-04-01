<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class bellingParameter extends FormRequest
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
            'year'  => 'integer',
            'month' => 'integer',
        ];
    }

    public function messages()
    {
        return [
            'year.integer'  => '年は整数にしてください',
            'month.integer' => '月は整数にしてください',
        ];
    }
}
