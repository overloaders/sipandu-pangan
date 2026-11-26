<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transaction_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financial_transaction_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            // Nama identifier eksplisit — nama otomatis melebihi batas 64 char MySQL
            $table->foreign('financial_transaction_id', 'fk_fintx_hist_txn')
                ->references('id')->on('financial_transactions')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_fintx_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['financial_transaction_id', 'created_at'], 'idx_fintx_hist_txn_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transaction_status_histories');
    }
};
