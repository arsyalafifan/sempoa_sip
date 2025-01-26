<?php

namespace App\Http\Requests\master\Program;

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
            'progkode' => [
                'required',
                Rule::unique('tbmprog')->where(function ($query) {
                    return $query->where('progid', '!=', $this->progid)
                        ->where('dlt', '0');
                }),
                'max:20'
            ],
            'prognama' => 'required|max:200',
        ];
    }

    public function messages()
    {
        return [
            'progkode.required' => 'Kode kota harus diisi',
            'progkode.unique' => 'Kode kota sudah digunakan',
            'progkode.max' => 'Kode kota maksimal 20 karakter',
            'prognama.required' => 'Nama Kota harus diisi',
            'prognama.max' => 'Nama Kota maksimal 200 karakter',
       ];
    }
}
