<?php

namespace App\Http\Requests\master\Kota;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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
            'kodekota' => [
                'required',
                Rule::unique('tbmkota')->where(function ($query) {
                    return $query->where('kotaid', '!=', $this->kotaid)
                        ->where('dlt', '0');
                }),
                'max:20'
            ],
            'namakota' => 'required|max:50',
            'jenis' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'kodekota.required' => 'Kode kota harus diisi',
            'kodekota.unique' => 'Kode kota sudah digunakan',
            'kodekota.max' => 'Kode kota maksimal 20 karakter',
            'namakota.required' => 'Nama Kota harus diisi',
            'namakota.max' => 'Nama Kota maksimal 50 karakter',
            'jenis.required' => 'Jenis harus dipilih'
       ];
    }
}
