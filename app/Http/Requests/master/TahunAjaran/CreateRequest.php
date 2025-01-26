<?php

namespace App\Http\Requests\master\TahunAjaran;

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
        return true;
        // return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'daritahun' => 'required|max:4',
            'daribulan' => 'required',
            'sampaitahun' => 'required|max:4|gt:daritahun',
            'sampaibulan' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'daritahun.required' => 'Dari tahun harus diisi',
            'sampaitahun.required' => 'Sampai tahun harus diisi',
            'sampaitahun.gt' => 'Pilihan Sampai Tahun harus lebih besar dari pilihan Dari Tahun',
            'daribulan.required' => 'Dari bulan harus diisi',
            'sampaibulan.required' => 'Sampai bulan harus diisi'
        ];
    }
}
