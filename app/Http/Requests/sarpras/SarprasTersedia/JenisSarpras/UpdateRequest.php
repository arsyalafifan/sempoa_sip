<?php

namespace App\Http\Requests\sarpras\SarprasTersedia\JenisSarpras;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
     /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'jenissarpras' => 'required',
            'namasarprasid' => 'required',
            'jumlah' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'jenissarpras.required' => 'Jenis sarpras harus dipilih.',
            'namasarpras.required' => 'Nama sarpras harus dipilih.',
            'jumlah.required' => 'Jumlah unit harus diisi'
        ];
    }
}
