<?php

namespace App\Http\Requests\master\Kegiatan;

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
            'kegkode' => [
                'required',
                Rule::unique('tbmkeg')->where(function ($query) {
                        return $query->where([['progid', $this->progid], ['kegkode', $this->kegkode], ['dlt', 0]]);
                }),
                'max:20'
            ],
            'kegnama' => 'required|max:200',
            'progid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'kegkode.required' => 'Kode Kegiatan harus diisi',
            'kegkode.unique' => 'Kode Kegiatan sudah digunakan',
            'kegkode.max' => 'Kode Kegiatan maksimal 20 karakter',
            'kegnama.required' => 'Nama Kegiatan harus diisi',
            'kegnama.max' => 'Nama Kegiatan maksimal 200 karakter',
            'progid.required' => 'Program harus dipilih'
       ];
    }
}
