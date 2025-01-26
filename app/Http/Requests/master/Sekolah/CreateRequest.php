<?php

namespace App\Http\Requests\master\Sekolah;

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
            'namasekolah' => 'required|string|max:200',
            'npsn' => 'required|string|max:12',
            'jenjang' => 'required',
            'jenis' => 'required',
            'alamat' => 'required|max:250',
            'kota' => 'required',
            'kecamatan' => 'required',
            'lintang' => 'required',
            'bujur' => 'required',
            'kurikulum' => 'required',
            'predikat' => 'required',
            'fileakreditasi.*' => 'mimes:jpg,jpeg,png,gif,tiff|max:5120'
        ];
    }

    public function messages()
    {
        return [
            'namasekolah.required' => 'Nama sekolah harus diisi' 
        ];
    }
}
