<?php

namespace App\Http\Requests\transaksi\Realisasi;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
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
            'nosp2d' => 'required',
            'tglsp2d' => 'required',
            'nilaisp2d' => 'required',
            'keterangan' => 'required',
            'file' => 'mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'file.mimes' => 'Format file yang anda masukkan tidak sesuai',
            'file.max' => 'Ukuran maksimal upload file adalah 5MB'
        ];
    }
}
