<?php

namespace App\Http\Requests\master\Struk;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return false;
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'strukkode' => [
                'required', 
                Rule::unique('tbmstruk')->where(function ($query) {
                    return $query->where('dlt','0');
                }),
                'max:20'
            ],
            'struknama' => 'required|max:200',
        ];
    }

    public function messages()
    {
        return [
            'strukkode.required' => 'Kode Struk harus diisi',
            'strukkode.unique' => 'Kode Struk sudah digunakan',
            'strukkode.max' => 'Kode Struk maksimal berjumlah 20 karakter',
            'struknama.required' => 'Nama Struk harus diisi',
            'struknama.max' => 'Nama Struk maksimal berjumlah 200 karakter',
       ];
    }
}
