<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_item_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_item_id');
            $table->unsignedBigInteger('changed_by_user_id')->nullable();
            $table->string('status_lama', 30)->nullable();
            $table->string('status_baru', 30);
            $table->timestamps();

            $table->foreign('inventory_item_id', 'fk_inv_hist_item')
                ->references('id')->on('inventory_items')->cascadeOnDelete();
            $table->foreign('changed_by_user_id', 'fk_inv_hist_user')
                ->references('id')->on('users')->nullOnDelete();
            $table->index(['inventory_item_id', 'created_at'], 'idx_inv_hist_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_item_status_histories');
    }
};
