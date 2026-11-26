<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFinancialTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|in:draft,pending_approval,disetujui,ditolak,selesai',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status tidak valid',
        ];
    }
}
