<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransactionStatusHistory extends Model
{
    protected $fillable = [
        'financial_transaction_id', 'status_lama', 'status_baru', 'changed_by_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(FinancialTransaction::class, 'financial_transaction_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
