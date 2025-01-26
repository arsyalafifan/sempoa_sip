<?php

namespace App\Http\Requests\master\Subkegiatan;

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
            'subkegkode' => [
                'required',
                Rule::unique('tbmsubkeg')->where(function ($query) {
                        return $query->where([['kegid', $this->kegid], ['subkegkode', $this->subkegkode], ['dlt', 0]]);
                }),
                'max:20'
            ],
            'subkegnama' => 'required|max:200',
            'kegid' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'subkegkode.required' => 'Kode Sub Kegiatan harus diisi',
            'subkegkode.unique' => 'Kode Sub Kegiatan sudah digunakan',
            'subkegkode.max' => 'Kode Sub Kegiatan maksimal 20 karakter',
            'subkegnama.required' => 'Nama Sub Kegiatan harus diisi',
            'subkegnama.max' => 'Nama Sub Kegiatan maksimal 200 karakter',
            'kegid.required' => 'Kegiatan harus dipilih'
       ];
    }
}
