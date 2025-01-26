<?php

namespace App\Http\Requests\master\Program;

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
            'progkode' => [
                'required', 
                Rule::unique('tbmprog')->where(function ($query) {
                    return $query->where('dlt','0');
                }),
                'max:20'
            ],
            'prognama' => 'required|max:200',
        ];
    }

    public function messages()
    {
        return [
            'progkode.required' => 'Kode Program harus diisi',
            'progkode.unique' => 'Kode Program sudah digunakan',
            'progkode.max' => 'Kode Program maksimal berjumlah 20 karakter',
            'prognama.required' => 'Nama Program harus diisi',
            'prognama.max' => 'Nama Program maksimal berjumlah 200 karakter',
       ];
    }
}
