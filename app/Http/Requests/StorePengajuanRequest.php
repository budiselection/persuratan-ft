<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;    

class StorePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi route ditangani oleh Middleware & Policy
        return true; 
    }

    public function rules(): array
    {
        return [
            'jenis_surat_id' => ['required', 'exists:jenis_surat,id'],
            'data_json' => ['required', 'array'], // Validasi dasar, detail divalidasi di Controller
            'lampiran' => ['nullable', 'array'],
            'lampiran.*' => [
                'file', 
                'mimes:pdf,jpg,jpeg,png', 
                'max:2048' // Maksimal 2MB per file
            ],
            'target_signer_id' => [
    'required',
    'exists:users,id',
    function (string $attribute, mixed $value, \Closure $fail) {
        $calon = User::find($value);

        if (! $calon || ! $calon->hasRole(['Penandatangan', 'Super Admin']) || ! $calon->is_active) {
            $fail('User yang dipilih bukan penandatangan aktif.');
        }
    },
],
        ];
    }

    public function messages(): array
    {
        return [
            'lampiran.*.mimes' => 'File lampiran harus berupa PDF, JPG, atau PNG.',
            'lampiran.*.max' => 'Ukuran file lampiran maksimal 2 MB.',
        ];
    }
}