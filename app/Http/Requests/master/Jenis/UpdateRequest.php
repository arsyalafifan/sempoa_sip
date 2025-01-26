<?php

namespace App\Http\Requests\master\Jenis;

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
            'jenkode' => [
                'required', 
                Rule::unique('tbmjen')->where(function ($query) {
                    return $query->where('jenid','!=',$this->jenid)
                        ->where('kelid',$this->kelid)
                        ->where('dlt','0');
                }),
                'max:20'
            ],
            'jennama' => 'required|max:200',
            'dasarhukum' => 'max:200',
            'kelid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'jenkode.required' => 'Kode Jenis harus diisi',
            'jenkode.unique' => 'Kode Jenis sudah digunakan',
            'jenkode.max' => 'Kode Jenis maksimal 20 karakter',
            'jennama.required' => 'Nama Jenis harus diisi',
            'dasarhukum.max' => 'Dasar Hukum maksimal 200 karakter',
            'jennama.max' => 'Nama Jenis maksimal 200 karakter',
            'kelid.required' => 'Kelompok harus dipilih'
        ];
    }
}
