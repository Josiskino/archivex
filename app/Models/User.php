<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'avatar',
        'last_login_at',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'preferences' => 'array',
        ];
    }

    // Relations

    /**
     * Relation avec le rôle
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relation avec les groupes
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'user_group')->withTimestamps();
    }

    /**
     * Fichiers uploadés par l'utilisateur
     */
    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(File::class, 'uploaded_by');
    }

    /**
     * Dossiers créés par l'utilisateur
     */
    public function folders(): HasMany
    {
        return $this->hasMany(Folder::class, 'created_by');
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
     * Vérifier si l'utilisateur a une permission
     */
    public function hasPermission(string $permission): bool
    {
        // Permission via le rôle
        if ($this->role && $this->role->hasPermission($permission)) {
            return true;
        }

        // Permission via les groupes
        foreach ($this->groups as $group) {
            if ($group->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin(): bool
    {
        return $this->hasPermission('admin.access');
    }

    /**
     * Vérifier si l'utilisateur peut accéder à un fichier
     */
    public function canAccessFile(File $file, string $permission = 'read'): bool
    {
        // Propriétaire du fichier
        if ($file->uploaded_by === $this->id) {
            return true;
        }

        // Admin a accès à tout
        if ($this->isAdmin()) {
            return true;
        }

        // Vérifier les permissions spécifiques
        return $file->hasPermissionFor($this, $permission);
    }

    /**
     * Obtenir toutes les permissions de l'utilisateur
     */
    public function getAllPermissions(): array
    {
        $permissions = [];

        // Permissions du rôle
        if ($this->role) {
            $permissions = array_merge($permissions, $this->role->permissions->pluck('name')->toArray());
        }

        // Permissions des groupes
        foreach ($this->groups as $group) {
            $permissions = array_merge($permissions, $group->permissions->pluck('name')->toArray());
        }

        return array_unique($permissions);
    }

    /**
     * Mettre à jour la dernière connexion
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
