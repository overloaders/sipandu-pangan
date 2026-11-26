<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 50)->comment('login_failed, unauthorized_access, token_revoked, password_changed, suspicious_activity');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('api_client_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45);
            $table->string('user_agent', 500)->nullable();
            $table->text('details')->nullable()->comment('JSON details');
            $table->enum('severity', ['low', 'medium', 'high', 'critical']);
            $table->boolean('is_resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index(['event_type', 'created_at']);
            $table->index(['severity', 'is_resolved']);
            $table->index(['user_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
