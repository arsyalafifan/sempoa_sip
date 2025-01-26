<?php

namespace App\Http\Requests\master\Jenis;

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
            'jenkode' => [
                'required',
                Rule::unique('tbmjen')->where(function ($query) {
                        return $query->where([['kelid', $this->kelid], ['jenkode', $this->jenkode], ['dlt', 0]]);
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
            'dasarhukum.max' => 'Dasar Hukum maksimal 200 karakter',
            'jennama.required' => 'Nama Jenis harus diisi',
            'jennama.max' => 'Nama Jenis maksimal 200 karakter',
            'kelid.required' => 'Kelompok harus dipilih'
       ];
    }
}
