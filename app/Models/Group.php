<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'created_by',
    ];

    // Relations

    /**
     * Créateur du groupe
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Utilisateurs du groupe
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_group')->withTimestamps();
    }

    /**
     * Permissions du groupe
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'group_permission')->withTimestamps();
    }

    /**
     * Permissions spécifiques aux fichiers
     */
    public function filePermissions(): MorphMany
    {
        return $this->morphMany(FilePermission::class, 'permissionable');
    }

    // Méthodes utilitaires

    /**
     * Vérifier si le groupe a une permission
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    /**
     * Ajouter un utilisateur au groupe
     */
    public function addUser(User $user): void
    {
        $this->users()->syncWithoutDetaching([$user->id]);
    }

    /**
     * Retirer un utilisateur du groupe
     */
    public function removeUser(User $user): void
    {
        $this->users()->detach($user->id);
    }

    /**
     * Nombre d'utilisateurs dans le groupe
     */
    public function getUsersCountAttribute(): int
    {
        return $this->users()->count();
    }

    /**
     * Synchroniser les permissions
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }
}
