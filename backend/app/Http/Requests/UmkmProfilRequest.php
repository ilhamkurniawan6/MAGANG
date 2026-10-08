<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UmkmProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_usaha' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori_umkm,id',
            'alamat' => 'required|string',
            'kontak_wa' => 'required|string|max:30',
            'email_usaha' => 'nullable|email|max:255',
            'deskripsi' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ];
    }
}
