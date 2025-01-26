<?php

namespace App\Http\Requests\master\Instansi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InstansiFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return true;
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
            'logo' => 'mimes:jpg,jpeg,png,gif,tiff|max:1024',
            'namainstansi' => ['required', 'string', 'max:250'],
            'alamat' => ['required', 'string', 'max:250'],
            'telp' => ['required', 'string', 'max:50'],
            'fax' => ['nullable', 'string', 'max:50'],
            'kodepos' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'string', 'max:50'],
            'jenisinstansi' => ['required', 'string', 'max:50'],
            'jenisdaerah' => ['required', 'string', 'max:1'],
            'kepda' => ['required', 'string', 'max:50'],
            'namakepda' => ['required', 'string', 'max:100'],
            // 'nipkepda' => ['required', 'string', 'max:50'],
            'namasekda' => ['string', 'max:100'],
            'nipsekda' => ['string', 'max:50'],
            // 'provinsi' => ['required'],
            'ibukota' => ['nullable', 'string', 'max:50'],
            'namakadis' => ['nullable', 'string', 'max:100'],
            'nipkadis' => ['nullable', 'string', 'max:50']
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            
        ];
    }

    public function attributes()
    {
        return [
            'logo' => 'Logo',
            'namainstansi' => 'Nama Instansi',
            'alamat' => 'Alamat',
            'fax' => 'Fax',
            'telp' => 'Nomor Telp',
            'kodepos' => 'Kode Pos',
            'email' => 'E-mail',
            'jenisinstansi' => 'Jenis Instansi',
            'jenisdaerah' => 'Jenis Daerah',
            'kepda' => 'Kepala Daerah',
            'namakepda' => 'Nama Kepala Daerah',
            'namasekda' => 'Nama Sekretaris Daerah',
            'nipsekda' => 'NIP Sekretaris Daerah',
            'provinsi' => 'Provinsi',
            'ibukota' => 'Ibukota',
            'namakadis' => 'Nama Kepala Dinas',
            'nipkadis' => 'NIP Kepala Dinas'
        ];
    }
}
