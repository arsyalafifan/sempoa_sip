<?php

namespace App\Http\Requests\sarpras\SarprasTersedia\DetailSarpras;

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
            'subkegid' => 'required',
            'sumberdana' => 'required',
            'jenispagu.*' => 'required',
            'nilaipagu.*' => 'required',
            'perusahaanid.*' => 'required',
            'tglpelaksanaan.*' => 'required|date|before:tomorrow',
            'file.*' => 'mimes:pdf,jpg,jpeg,png|max:5120'
        ];
    }

    public function messages()
    {
        return [
            'tglpelaksanaan.before' => 'Tanggal pelaksanaan harus sebelum hari ini',
            'file.mimes' => 'File harus berformat PDF, JPEG, JPG atau PNG',
            'file.max' => 'Ukuran file tidak boleh melebihi 5MB',
        ];
    }
}
