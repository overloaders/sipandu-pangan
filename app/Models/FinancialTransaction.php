<?php

namespace App\Models;

use App\Traits\HasActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    use SoftDeletes, HasActivityLog;

    protected $fillable = [
        'kode_transaksi_keu', 'tipe', 'category_id', 'budget_id', 'jumlah',
        'deskripsi', 'sumber_dana', 'bukti_transaksi', 'metode_pembayaran',
        'unit_id', 'approved_by', 'status', 'tanggal_transaksi', 'keterangan',
    ];

    protected $casts = ['jumlah' => 'decimal:2', 'tanggal_transaksi' => 'date'];

    public function category() { return $this->belongsTo(FinancialCategory::class, 'category_id'); }
    public function budget() { return $this->belongsTo(Budget::class); }
    public function unit() { return $this->belongsTo(Unit::class); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function statusHistories() { return $this->hasMany(FinancialTransactionStatusHistory::class, 'financial_transaction_id'); }

    /**
     * Catat riwayat perubahan status secara otomatis (siapa & kapan).
     */
    protected static function booted(): void
    {
        static::created(function ($transaction) {
            if ($transaction->status) {
                FinancialTransactionStatusHistory::create([
                    'financial_transaction_id' => $transaction->id,
                    'status_lama' => null,
                    'status_baru' => $transaction->status,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });

        static::updated(function ($transaction) {
            $old = $transaction->getOriginal('status');
            $new = $transaction->status;
            if ($old !== $new) {
                FinancialTransactionStatusHistory::create([
                    'financial_transaction_id' => $transaction->id,
                    'status_lama' => $old,
                    'status_baru' => $new,
                    'changed_by_user_id' => auth()->id(),
                ]);
            }
        });
    }
}
