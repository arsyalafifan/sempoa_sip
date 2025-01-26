<?php

namespace App\Http\Requests\master\Kecamatan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
            'kodekec' => [
                'required', 
                Rule::unique('tbmkecamatan')->where(function ($query) {
                    return $query->where('kecamatanid','!=',$this->kecamatanid)
                        ->where('kotaid',$this->kotaid)
                        ->where('dlt','0');
                }),
                'max:20'
            ],
            'namakec' => 'required|max:50',
            'kotaid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'kodekec.required' => 'Kode Kecamatan harus diisi',
            'kodekec.unique' => 'Kode Kecamatan sudah digunakan',
            'kodekec.max' => 'Kode Kecamatan maksimal 20 karakter',
            'namakec.required' => 'Nama Kecamatan harus diisi',
            'namakec.max' => 'Nama Kecamatan maksimal 50 karakter',
            'kotaid.required' => 'Kota/Kabupaten harus dipilih'
        ];
    }
}
