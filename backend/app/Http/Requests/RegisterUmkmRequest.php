<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUmkmRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // User Data
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
            'no_wa' => 'nullable|string|max:30',

            // UMKM Data
            'kategori_id' => 'required|exists:kategori_umkm,id',
            'nama_usaha' => 'required|string|max:255',
            'alamat' => 'required|string',
            'kontak_wa' => 'required|string|max:30',
            'email_usaha' => 'nullable|email|max:255',
            'deskripsi' => 'nullable|string',
        ];
    }
}
