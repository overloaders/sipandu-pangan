<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opname_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_opname_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('stock_opname_id', 'fk_sop_hist_sop')
                ->references('id')->on('stock_opnames')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_sop_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['stock_opname_id', 'created_at'], 'idx_sop_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_status_histories');
    }
};
