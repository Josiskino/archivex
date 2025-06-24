<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'parent_id',
        'created_by',
        'is_public',
        'path',
        'depth',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Relations

    /**
     * Dossier parent
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /**
     * Sous-dossiers
     */
    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    /**
     * Créateur du dossier
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Fichiers dans ce dossier
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    // Méthodes utilitaires

    /**
     * Obtenir tous les dossiers descendants
     */
    public function descendants(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id')->with('descendants');
    }

    /**
     * Obtenir le chemin complet
     */
    public function getFullPath(): string
    {
        if (!$this->parent) {
            return $this->name;
        }
        
        return $this->parent->getFullPath() . '/' . $this->name;
    }

    /**
     * Mettre à jour le chemin et la profondeur
     */
    public function updatePath(): void
    {
        $this->path = $this->getFullPath();
        $this->depth = $this->parent ? $this->parent->depth + 1 : 0;
        $this->save();
        
        // Mettre à jour les enfants
        $this->children()->each(function ($child) {
            $child->updatePath();
        });
    }

    /**
     * Nombre total de fichiers (incluant sous-dossiers)
     */
    public function getTotalFilesCountAttribute(): int
    {
        $count = $this->files()->count();
        
        foreach ($this->children as $child) {
            $count += $child->total_files_count;
        }
        
        return $count;
    }

    /**
     * Taille totale du dossier
     */
    public function getTotalSizeAttribute(): int
    {
        $size = $this->files()->sum('size');
        
        foreach ($this->children as $child) {
            $size += $child->total_size;
        }
        
        return $size;
    }

    /**
     * Vérifier si l'utilisateur peut accéder à ce dossier
     */
    public function canAccess(User $user): bool
    {
        // Créateur du dossier
        if ($this->created_by === $user->id) {
            return true;
        }
        
        // Dossier public
        if ($this->is_public) {
            return true;
        }
        
        // Admin peut accéder à tout
        if ($user->isAdmin()) {
            return true;
        }
        
        return false;
    }

    /**
     * Supprimer le dossier et tout son contenu
     */
    public function deleteWithContents(): bool
    {
        // Supprimer récursivement les sous-dossiers
        $this->children()->each(function ($child) {
            $child->deleteWithContents();
        });
        
        // Supprimer tous les fichiers
        $this->files()->each(function ($file) {
            $file->deleteFile();
        });
        
        return $this->delete();
    }

    // Scopes

    /**
     * Dossiers racines (sans parent)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Dossiers publics
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Dossiers d'un utilisateur
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('created_by', $user->id);
    }

    /**
     * Recherche par nom
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
    }
}
