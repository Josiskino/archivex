<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_name',
        'filename',
        'path',
        'size',
        'mime_type',
        'extension',
        'folder_id',
        'uploaded_by',
        'metadata',
        'is_public',
        'last_accessed_at',
        'checksum',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_public' => 'boolean',
        'last_accessed_at' => 'datetime',
    ];

    // Relations

    /**
     * Dossier parent
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Utilisateur qui a uploadé le fichier
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Permissions spécifiques à ce fichier
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(FilePermission::class);
    }

    // Méthodes utilitaires

    /**
     * Vérifier si un utilisateur/groupe a une permission sur ce fichier
     */
    public function hasPermissionFor($entity, string $permission): bool
    {
        $entityType = get_class($entity);
        $entityId = $entity->id;

        return $this->permissions()
            ->where('permissionable_type', $entityType)
            ->where('permissionable_id', $entityId)
            ->whereJsonContains('permissions', $permission)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Accorder une permission à un utilisateur/groupe
     */
    public function grantPermission($entity, array $permissions, User $grantedBy, $expiresAt = null): void
    {
        $this->permissions()->updateOrCreate([
            'permissionable_type' => get_class($entity),
            'permissionable_id' => $entity->id,
        ], [
            'permissions' => $permissions,
            'granted_by' => $grantedBy->id,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Révoquer les permissions d'un utilisateur/groupe
     */
    public function revokePermission($entity): void
    {
        $this->permissions()
            ->where('permissionable_type', get_class($entity))
            ->where('permissionable_id', $entity->id)
            ->delete();
    }

    /**
     * Obtenir l'URL du fichier
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    /**
     * Formater la taille du fichier
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Vérifier si le fichier est une image
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Vérifier si le fichier est un document
     */
    public function isDocument(): bool
    {
        $documentTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        return in_array($this->mime_type, $documentTypes);
    }

    /**
     * Mettre à jour le dernier accès
     */
    public function updateLastAccess(): void
    {
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Supprimer le fichier physique et l'enregistrement
     */
    public function deleteFile(): bool
    {
        if (Storage::disk('public')->exists($this->path)) {
            Storage::disk('public')->delete($this->path);
        }
        
        return $this->delete();
    }

    // Scopes

    /**
     * Fichiers publics
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Fichiers privés
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Fichiers d'un utilisateur
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('uploaded_by', $user->id);
    }

    /**
     * Fichiers dans un dossier
     */
    public function scopeInFolder($query, $folderId)
    {
        return $query->where('folder_id', $folderId);
    }
}
