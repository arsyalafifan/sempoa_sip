<?php

namespace App\Http\Requests\master\Ijazahtemp;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
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
                // Rule::unique(function ($query) {
                //     return $query->from('tbmijazah')
                //         ->join('tbmijazahtemp', 'tbmijazah.noijazah', '=', 'tbmijazahtemp.noijazah')
                //         ->where('tbmijazah.dlt', '0')
                //         ->where('tbmijazahtemp.dlt', '0');
                // })->ignore(Request::segment(2), 'ijazahid'),
                Rule::unique('tbmijazah')
                ->where(function ($query) {
                    $query->where('dlt', '0');
                })
                ->ignore(Request::segment(2), 'ijazahid'),
            ],
            'tgl_lulus' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'namasiswa.required' => 'Nama Siswa harus diisi' ,
            'noijazah.unique' => 'No Ijazah sudah terdaftar, Masukan no ijazah lainnya'
            // 'sekolahid.required' => 'Pilih Sekolah Terlebih Dahulu' 
        ];
    }
}
