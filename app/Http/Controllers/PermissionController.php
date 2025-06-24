<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function getUsers(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        
        // Récupérer les utilisateurs avec leurs relations
        $users = User::with(['role', 'groups'])
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%"))
            ->when($status && $status !== 'all', fn($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->get();

        // Transformer les données pour correspondre au format attendu par le frontend
        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'role' => [
                    'id' => $user->role?->id,
                    'name' => $user->role?->name ?? 'Aucun rôle',
                    'color' => $user->role?->color ?? 'gray'
                ],
                'groups' => $user->groups->map(function ($group) {
                    return [
                        'id' => $group->id,
                        'name' => $group->name,
                        'color' => $group->color
                    ];
                }),
                'status' => $user->status,
                'lastLogin' => $user->last_login_at?->toISOString(),
                'createdAt' => $user->created_at->toISOString()
            ];
        });

        return response()->json([
            'success' => true,
            'users' => $formattedUsers
        ]);
    }
    
    public function createUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|string',
            'groups' => 'array',
            'groups.*' => 'string',
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        // Ici vous créeriez l'utilisateur en base
        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        //     'role_id' => $request->role_id,
        //     'status' => 'active'
        // ]);
        
        // $user->groups()->sync($request->groups ?? []);
        
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur créé avec succès',
            'user' => [
                'id' => uniqid(),
                'name' => $request->name,
                'email' => $request->email,
                'status' => 'active',
                'createdAt' => now()->toISOString()
            ]
        ]);
    }
    
    public function updateUser(Request $request, $userId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($userId)],
            'role_id' => 'required|string',
            'groups' => 'array',
            'status' => 'required|in:active,inactive,suspended'
        ]);
        
        // Ici vous mettriez à jour l'utilisateur
        // $user = User::findOrFail($userId);
        // $user->update($request->only(['name', 'email', 'role_id', 'status']));
        // $user->groups()->sync($request->groups ?? []);
        
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur mis à jour avec succès'
        ]);
    }
    
    public function deleteUser($userId)
    {
        // Vérifier les permissions
        if (Auth::id() == $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne pouvez pas supprimer votre propre compte'
            ], 400);
        }
        
        // Ici vous supprimeriez l'utilisateur
        // User::findOrFail($userId)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Utilisateur supprimé avec succès'
        ]);
    }
    
    public function getRoles()
    {
        // Ici vous récupéreriez depuis la base
        // $roles = Role::with('permissions')->withCount('users')->get();
        
        $roles = [
            [
                'id' => '1',
                'name' => 'Administrateur',
                'description' => 'Accès complet au système',
                'color' => 'red',
                'permissions' => ['files.read', 'files.write', 'files.delete', 'users.read', 'users.write', 'users.delete', 'admin.access'],
                'usersCount' => 2
            ],
            [
                'id' => '2',
                'name' => 'Gestionnaire',
                'description' => 'Gestion des fichiers et utilisateurs',
                'color' => 'blue',
                'permissions' => ['files.read', 'files.write', 'files.delete', 'users.read', 'users.write'],
                'usersCount' => 5
            ],
            [
                'id' => '3',
                'name' => 'Utilisateur',
                'description' => 'Accès de base aux fichiers',
                'color' => 'green',
                'permissions' => ['files.read', 'files.write'],
                'usersCount' => 25
            ]
        ];
        
        return response()->json([
            'success' => true,
            'roles' => $roles
        ]);
    }
    
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'required|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'string',
            'color' => 'required|string'
        ]);
        
        // Ici vous créeriez le rôle
        // $role = Role::create($request->only(['name', 'description', 'color']));
        // $role->permissions()->sync($request->permissions);
        
        return response()->json([
            'success' => true,
            'message' => 'Rôle créé avec succès',
            'role' => [
                'id' => uniqid(),
                'name' => $request->name,
                'description' => $request->description,
                'color' => $request->color,
                'permissions' => $request->permissions,
                'usersCount' => 0
            ]
        ]);
    }
    
    public function getGroups()
    {
        // Ici vous récupéreriez depuis la base
        // $groups = Group::with('permissions')->withCount('users')->get();
        
        $groups = [
            [
                'id' => '1',
                'name' => 'Équipe Technique',
                'description' => 'Développeurs et techniciens',
                'permissions' => ['files.read', 'files.write', 'files.delete'],
                'usersCount' => 8,
                'color' => 'purple'
            ],
            [
                'id' => '2',
                'name' => 'Équipe Commerciale',
                'description' => 'Commerciaux et marketing',
                'permissions' => ['files.read', 'files.write'],
                'usersCount' => 12,
                'color' => 'orange'
            ],
            [
                'id' => '3',
                'name' => 'Direction',
                'description' => 'Direction et management',
                'permissions' => ['files.read', 'files.write', 'files.delete', 'users.read', 'users.write'],
                'usersCount' => 4,
                'color' => 'indigo'
            ]
        ];
        
        return response()->json([
            'success' => true,
            'groups' => $groups
        ]);
    }
    
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'permissions' => 'required|array',
            'permissions.*' => 'string',
            'color' => 'required|string'
        ]);
        
        // Ici vous créeriez le groupe
        // $group = Group::create($request->only(['name', 'description', 'color']));
        // $group->permissions()->sync($request->permissions);
        
        return response()->json([
            'success' => true,
            'message' => 'Groupe créé avec succès',
            'group' => [
                'id' => uniqid(),
                'name' => $request->name,
                'description' => $request->description,
                'color' => $request->color,
                'permissions' => $request->permissions,
                'usersCount' => 0
            ]
        ]);
    }
    
    public function getPermissions()
    {
        // Ici vous récupéreriez depuis la base
        // $permissions = Permission::orderBy('category')->orderBy('name')->get();
        
        $permissions = [
            ['id' => '1', 'name' => 'files.read', 'description' => 'Lire les fichiers', 'category' => 'Fichiers'],
            ['id' => '2', 'name' => 'files.write', 'description' => 'Écrire des fichiers', 'category' => 'Fichiers'],
            ['id' => '3', 'name' => 'files.delete', 'description' => 'Supprimer des fichiers', 'category' => 'Fichiers'],
            ['id' => '4', 'name' => 'users.read', 'description' => 'Voir les utilisateurs', 'category' => 'Utilisateurs'],
            ['id' => '5', 'name' => 'users.write', 'description' => 'Modifier les utilisateurs', 'category' => 'Utilisateurs'],
            ['id' => '6', 'name' => 'users.delete', 'description' => 'Supprimer les utilisateurs', 'category' => 'Utilisateurs'],
            ['id' => '7', 'name' => 'admin.access', 'description' => 'Accès administration', 'category' => 'Administration'],
        ];
        
        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }
    
    public function toggleUserStatus(Request $request, $userId)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended'
        ]);
        
        // Ici vous mettriez à jour le statut
        // User::findOrFail($userId)->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Statut utilisateur mis à jour'
        ]);
    }
}
