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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('filename')->unique();
            $table->string('path');
            $table->bigInteger('size'); // en bytes
            $table->string('mime_type');
            $table->string('extension', 10);
            $table->foreignId('folder_id')->nullable()->constrained('folders')->onDelete('set null');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->json('metadata')->nullable(); // Stockage de métadonnées supplémentaires
            $table->boolean('is_public')->default(false);
            $table->timestamp('last_accessed_at')->nullable();
            $table->string('checksum', 64)->nullable(); // Pour vérifier l'intégrité
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index(['uploaded_by', 'created_at']);
            $table->index(['folder_id']);
            $table->index(['mime_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
