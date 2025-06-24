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
        Schema::create('file_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->string('permissionable_type'); // User ou Group
            $table->unsignedBigInteger('permissionable_id');
            $table->json('permissions'); // ['read', 'write', 'delete']
            $table->foreignId('granted_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            // Index polymorphe
            $table->index(['permissionable_type', 'permissionable_id']);
            $table->index(['file_id', 'permissionable_type', 'permissionable_id'], 'file_perm_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_permissions');
    }
};
