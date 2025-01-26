<?php

namespace App\Http\Requests\master\Struk;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
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
                    return $query->where('strukid', '!=', $this->strukid)
                        ->where('dlt', '0');
                }),
                'max:20'
            ],
            'struknama' => 'required|max:200',
        ];
    }

    public function messages()
    {
        return [
            'strukkode.required' => 'Kode kota harus diisi',
            'strukkode.unique' => 'Kode kota sudah digunakan',
            'strukkode.max' => 'Kode kota maksimal 20 karakter',
            'struknama.required' => 'Nama Kota harus diisi',
            'struknama.max' => 'Nama Kota maksimal 200 karakter',
       ];
    }
}
