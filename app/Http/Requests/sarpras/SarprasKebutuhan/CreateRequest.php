<?php

namespace App\Http\Requests\sarpras\SarprasKebutuhan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


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
            'nopengajuan' => [
                'required', 
                Rule::unique('tbtranssarpraskebutuhan')->where(function ($query) {
                    return $query
                        ->where('dlt','0');
                }),
                'max:20'
            ],
            'tglpengajuan' => 'required|date|before:tomorrow',
            'pegawaiid' => 'required',
            'jenissarpras' => 'required',
            'namasarprasid' => 'required',
            'jumlah' => 'required',
            'filesarpraskebutuhan' => 'required',
            'filesarpraskebutuhan.*' => 'mimes:jpg,jpeg,png|max:5120'
        ];
    }

    public function messages()
    {
        return [
            'nopengajuan.required' => 'Nomor pengajuan harus diisi.',
            'nopengajuan.unique' => 'Nomor pengajuan ini sudah digunakan.',
            'nopengajuan.max' => 'Nomor pengajuan maksimal berjumlah 20 karakter.',
            'tglpengajuan.required' => 'Tanggal pengajuan harus diisi.',
            'tglpengajuan.before' => 'Tanggal pengajuan tidak boleh melebihi tanggal hari ini.',
            'pegawaiid.required' => 'Kepala sekolah harus diisi.',
            'jenissarpras.required' => 'Jenis sarpras harus dipilih.',
            'namasarprasid.required' => 'Nama sarpras harus dipilih.',
            'jumlah.required' => 'Jumlah harus diisi.',
            'filesarpraskebutuhan.required' => 'Foto harus diisi.',
            'filesarpraskebutuhan.*.mimes' => 'Format foto harus berupa PNG, JPG atau JPEG.',
            'filesarpraskebutuhan.max' => 'Maksimal ukuran file 5MB'
       ];
    }
}
