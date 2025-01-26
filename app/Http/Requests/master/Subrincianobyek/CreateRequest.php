<?php

namespace App\Http\Requests\master\Subrincianobyek;

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
            'srobykode' => [
                'required',
                Rule::unique('tbmsroby')->where(function ($query) {
                        return $query->where([['robyid', $this->robyid], ['srobykode', $this->srobykode], ['dlt', 0]]);
                }),
                'max:20'
            ],
            'srobynama' => 'required|max:200',
            'robyid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'srobykode.required' => 'Kode Sub Rincian Obyek harus diisi',
            'srobykode.unique' => 'Kode Sub Rincian Obyek sudah digunakan',
            'srobykode.max' => 'Kode Sub Rincian Obyek maksimal 20 karakter',
            'srobynama.required' => 'Nama Sub Rincian Obyek harus diisi',
            'srobynama.max' => 'Nama Sub Rincian Obyek maksimal 200 karakter',
            'robyid.required' => 'Rincian Obyek harus dipilih'
       ];
    }
}
