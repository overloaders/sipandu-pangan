<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('client_id', 100)->unique();
            $table->string('client_secret_hash', 255);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->text('description')->nullable();
            $table->text('allowed_ips')->nullable()->comment('JSON array of allowed IPs');
            $table->integer('rate_limit_per_minute')->default(60);
            $table->integer('rate_limit_per_day')->default(10000);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_access_at')->nullable();
            $table->text('scopes')->nullable()->comment('JSON array of allowed scopes');
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_clients');
    }
};
