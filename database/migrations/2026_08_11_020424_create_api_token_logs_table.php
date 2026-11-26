<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_token_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_client_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('personal_access_token_id', 100)->nullable();
            $table->string('endpoint', 500);
            $table->string('method', 10);
            $table->string('ip_address', 45);
            $table->string('user_agent', 500)->nullable();
            $table->integer('response_code');
            $table->integer('response_time_ms')->nullable();
            $table->text('request_params')->nullable()->comment('Sanitized request params');
            $table->string('request_id', 36)->nullable();
            $table->timestamps();

            $table->index(['api_client_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
            $table->index('response_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_token_logs');
    }
};
