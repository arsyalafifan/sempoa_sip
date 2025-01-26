<?php

namespace App\Http\Requests\master\Ijazahtemp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

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
        // return Auth::check();
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
            // 'sekolahid' => 'required',
            'namasiswa' => 'required|string|max:200',
            'tempat_lahir' => 'required',
            'tgl_lahir' => 'required',
            'namaortu' => 'required|string|max:200',
            'nis' => 'required|string|max:20',
            'noijazah' => [
                'required',
                'string',
                'max:30',
                Rule::unique('tbmijazah'),
                // Rule::exists('tbmijazah')
                // ->where(function ($query) {
                //     $query->join('tbmijazahtemp', 'tbmijazah.noijazah', '=', 'tbmijazahtemp.noijazah')
                //         ->where('tbmijazah.dlt', '0')
                //         ->where('tbmijazahtemp.dlt', '0');
                // }),
            ],
            'tgl_lulus' => 'required',
            'file_ijazah' => 'required|mimetypes:application/pdf|max:5120',
            'file_ktp' => 'required|mimetypes:application/pdf|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'namasiswa.required' => 'Nama Siswa harus diisi' ,
            'noijazah.unique' => 'No Ijazah sudah terdaftar, Masukan no ijazah lainnya',
            'file_ijazah.max' => 'File Ijazah tidak boleh lebih dari 5 MB',
            'file_ktp.max' => 'File Ktp tidak boleh lebih dari 5 MB',
            'file_ijazah.required' => 'Pilih file ijazah terlebih dahulu',
            'file_ktp.required' => 'Pilih file ktp terlebih dahulu',
            'file_ijazah.mimetypes' => 'File Ijazah harus berupa file PDF',
            'file_ktp.mimetypes' => 'File Ktp harus berupa file PDF'
            // 'sekolahid.required' => 'Pilih Sekolah Terlebih Dahulu' 
        ];
    }
}
