<?php

namespace App\Http\Requests\sarpras\SarprasTersedia\DetailPagu;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'jenispagu' => 'required',
            'nilaipagu' => 'required',
            'perusahaanid' => 'required',
            'tgldari' => 'required|date|before:tomorrow',
            'tglsampai' => 'required|date|after:tgldari',
            'file' => 'mimes:jpg,jpeg,png,pdf|max:5120'
        ];
    }

    public function messages()
    {
        return [
            'jenispagu.required' => 'Jenis pagu harus diisi.',
            'nilaipagu.required' => 'Nilai pagu harus diisi.',
            'perusahaanid.required' => 'Perusahaan harus diisi',
            'tgldari.required' => 'Tanggal pelaksanaan harus diisi.',
            'tgldari.before' => 'Tanggal pelaksanaan tidak boleh melebihi tanggal hari ini.',
            'tglsampai.after' => 'Sampai tanggal harus melebihi dari tanggal',
            'file.required' => 'Foto harus diisi.',
            'file.mimes' => 'Format foto harus berupa PNG, JPG atau JPEG.',
            'file.max' => 'Maksimal ukuran file 5MB'
       ];
    }
}
