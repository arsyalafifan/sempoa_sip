<?php

namespace App\Http\Requests\master\Pegawai;

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
            // 'sekolahid' => 'required',
            'nip' => 'required|string|max:20',
            'nama' => 'required|string|max:200',
            'jabatan' => 'required',
            'npwp' => 'required|string|max:20',
            'ketjabatan' => 'max:200',
            'judulsk' => 'max:200',
            'nosk' => 'max:200',
            'file_ttd' => 'image|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'nama.max' => 'Panjang Karakter Tidak Boleh Lebih dari 200' ,
            'nip.max' => 'Panjang Karakter Tidak Boleh Lebih dari 20' ,
            'npwp.max' => 'Panjang Karakter Tidak Boleh Lebih dari 20' ,
            'ketjabatan.max' => 'Panjang Karakter Tidak Boleh Lebih dari 200' ,
            'judulsk.max' => 'Panjang Karakter Tidak Boleh Lebih dari 200' ,
            'nosk.max' => 'Panjang Karakter Tidak Boleh Lebih dari 200' ,
            'file_ttd.max' => 'Maximum file sebesar 5 MB' ,
        ];
    }
}
