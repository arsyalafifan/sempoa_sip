<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'aksesid' => 'required', 
            'grup' => 'required', 
            'login' => [
                'required', 
                Rule::unique('tbmuser')->where(function ($query) {
                    return $query->where('userid','!=',$this->userid)->where('dlt','0');
                }),
                'max:50',
                'alpha_num'
            ],
            'nama' => 'required|max:50',
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'Login harus diisi',
            'login.unique' => 'Login sudah digunakan',
            'login.max' => 'Login maksimal 50 karakter',
            'login.alpha_num' => 'Login tidak boleh menggunakan spasi dan hanya boleh berupa huruf dan angka',
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 50 karakter',
            'aksesid.required' => 'Hak Akses harus diisi',
       ];
    }
}