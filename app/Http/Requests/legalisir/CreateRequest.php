<?php

namespace App\Http\Requests\Legalisir;

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
        // allow guest to store data
        return true;
        // return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        'file_ijazah' => 'required|mimetypes:application/pdf|max:5120',
        'file_ktp' => 'required|mimetypes:application/pdf|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'file_ijazah.max' => 'File Ijazah tidak boleh lebih dari 5 MB',
            'file_ktp.max' => 'File Ktp tidak boleh lebih dari 5 MB',
            'file_ijazah.required' => 'Pilih file ijazah terlebih dahulu',
            'file_ktp.required' => 'Pilih file ktp terlebih dahulu',
            'file_ijazah.mimetypes' => 'File Ijazah harus berupa file PDF',
            'file_ktp.mimetypes' => 'File Ktp harus berupa file PDF'
        ];
    }
}
