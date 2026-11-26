<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_record_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('compliance_record_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('compliance_record_id', 'fk_cmp_hist_rec')
                ->references('id')->on('compliance_records')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_cmp_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['compliance_record_id', 'created_at'], 'idx_cmp_hist_rec_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_record_status_histories');
    }
};
