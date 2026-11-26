<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_health_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('health_record_id')->constrained('livestock_health_records')->onDelete('cascade');
            $table->foreignId('livestock_inventory_id')->constrained('livestock_inventory')->onDelete('cascade');
            $table->string('obat_diberikan', 200)->nullable();
            $table->decimal('dosis', 8, 2)->nullable();
            $table->date('jadwal_ulang')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['health_record_id', 'livestock_inventory_id'], 'lh_details_hrid_inv_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_health_details');
    }
};
