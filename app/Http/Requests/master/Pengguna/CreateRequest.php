<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
                    return $query->where('dlt','0');
                }),
                'max:50',
                // 'alpha_dash',
                'alpha_num'
            ],
            'password' => [
                'required', 
                'max:100',
                'regex:/[a-z]/',
                'regex:/[A-Z]/', 
                'regex:/[0-9]/',
                'confirmed'
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
            // 'login.alpha_dash' => 'Login tidak boleh menggunakan spasi',
            'login.alpha_num' => 'Login tidak boleh menggunakan spasi dan hanya boleh berupa huruf dan angka',
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 50 karakter',
            'password.required' => 'Password harus diisi',
            'password.confirmed' => 'Ulangi Password tidak cocok',
            'password.regex' => 'Password tidak valid (wajib menggunakan huruf kecil, huruf besar, dan angka)',
            'aksesid.required' => 'Hak Akses harus diisi',
       ];
    }
}