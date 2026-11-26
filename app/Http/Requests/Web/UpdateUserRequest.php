<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:150',
            'email' => 'sometimes|email|max:150|unique:users,email,' . $this->route('user')?->id,
            'password' => 'nullable|string|min:8|confirmed',
            'nip' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:20',
            'unit_id' => 'nullable|exists:units,id',
            'roles' => 'sometimes|array|min:1',
            'roles.*' => 'exists:roles,name',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Nama maksimal 150 karakter',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'unit_id.exists' => 'Unit tidak valid',
            'roles.min' => 'Pilih minimal 1 role',
            'roles.*.exists' => 'Role tidak valid',
        ];
    }
}
