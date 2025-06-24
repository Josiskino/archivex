<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->string('avatar')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->json('preferences')->nullable(); // Préférences utilisateur
            
            // Index pour améliorer les performances
            $table->index(['status', 'created_at']);
            $table->index(['role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['role_id']);
            $table->dropColumn(['role_id', 'status', 'avatar', 'last_login_at', 'preferences']);
        });
    }
};
