<?php

namespace App\Http\Requests\master\Obyek;

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
            'obykode' => [
                'required',
                Rule::unique('tbmoby')->where(function ($query) {
                        return $query->where([['jenid', $this->jenid], ['obykode', $this->obykode], ['dlt', 0]]);
                }),
                'max:20'
            ],
            'obynama' => 'required|max:200',
            'jenid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'obykode.required' => 'Kode Obyek harus diisi',
            'obykode.unique' => 'Kode Obyek sudah digunakan',
            'obykode.max' => 'Kode Obyek maksimal 20 karakter',
            'obynama.required' => 'Nama Obyek harus diisi',
            'obynama.max' => 'Nama Obyek maksimal 200 karakter',
            'jenid.required' => 'Jenis harus dipilih'
       ];
    }
}
