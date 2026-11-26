<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barn_id')->constrained('livestock_barns')->onDelete('cascade');
            $table->foreignId('feed_supply_id')->constrained()->onDelete('cascade');
            $table->foreignId('wbp_id')->nullable()->constrained('wbp')->onDelete('set null');
            $table->date('tanggal_pemberian');
            $table->enum('waktu_pemberian', ['pagi', 'siang', 'sore', 'malam']);
            $table->decimal('jumlah', 10, 2);
            $table->string('satuan', 20)->default('kg');
            $table->decimal('biaya', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['barn_id', 'tanggal_pemberian']);
            $table->index('tanggal_pemberian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_logs');
    }
};
