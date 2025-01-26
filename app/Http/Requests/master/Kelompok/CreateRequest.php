<?php

namespace App\Http\Requests\master\Kelompok;

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
            'kelkode' => [
                'required',
                Rule::unique('tbmkel')->where(function ($query) {
                        return $query->where([['strukid', $this->strukid], ['kelkode', $this->kelkode], ['dlt', 0]]);
                }),
                'max:20'
            ],
            'kelnama' => 'required|max:200',
            'strukid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'kelkode.required' => 'Kode Kelompok harus diisi',
            'kelkode.unique' => 'Kode Kelompok sudah digunakan',
            'kelkode.max' => 'Kode Kelompok maksimal 20 karakter',
            'kelnama.required' => 'Nama Kelompok harus diisi',
            'kelnama.max' => 'Nama Kelompok maksimal 200 karakter',
            'strukid.required' => 'Struktur harus dipilih'
       ];
    }
}
