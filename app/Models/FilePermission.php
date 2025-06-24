<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FilePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'permissionable_type',
        'permissionable_id',
        'permissions',
        'granted_by',
        'expires_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'expires_at' => 'datetime',
    ];

    // Relations

    /**
     * Fichier concerné
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Entité ayant la permission (User ou Group)
     */
    public function permissionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Utilisateur qui a accordé la permission
     */
    public function grantor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }

    // Méthodes utilitaires

    /**
     * Vérifier si la permission a expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Vérifier si une permission spécifique est accordée
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Ajouter une permission
     */
    public function addPermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    /**
     * Retirer une permission
     */
    public function removePermission(string $permission): void
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->update(['permissions' => array_values($permissions)]);
    }

    // Scopes

    /**
     * Permissions actives (non expirées)
     */
    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Permissions expirées
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Permissions pour un utilisateur
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('permissionable_type', User::class)
                    ->where('permissionable_id', $user->id);
    }

    /**
     * Permissions pour un groupe
     */
    public function scopeForGroup($query, Group $group)
    {
        return $query->where('permissionable_type', Group::class)
                    ->where('permissionable_id', $group->id);
    }
}
