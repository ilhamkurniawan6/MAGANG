<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProdukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH') || $this->input('_method') === 'PUT';

        return [
            'nama_produk' => 'required|string|max:255',
            'kategori_produk_id' => 'required|exists:kategori_produk,id',
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'required|string',
            'foto_produk' => $isUpdate ? 'nullable|image|max:2048' : 'nullable|image|max:2048',
        ];
    }
}
