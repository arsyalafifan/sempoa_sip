<?php

namespace App\Http\Requests\master\Rincianobyek;

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
            'robykode' => [
                'required',
                Rule::unique('tbmroby')->where(function ($query) {
                        return $query->where([['obyid', $this->obyid], ['robykode', $this->robykode], ['dlt', 0]]);
                }),
                'max:20'
            ],
            'robynama' => 'required|max:200',
            'obyid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'robykode.required' => 'Kode Rincian Obyek harus diisi',
            'robykode.unique' => 'Kode Rincian Obyek sudah digunakan',
            'robykode.max' => 'Kode Rincian Obyek maksimal 20 karakter',
            'robynama.required' => 'Nama Rincian Obyek harus diisi',
            'robynama.max' => 'Nama Rincian Obyek maksimal 200 karakter',
            'obyid.required' => 'Obyek harus dipilih'
       ];
    }
}
