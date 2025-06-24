<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rôle Administrateur
        $adminRole = Role::firstOrCreate([
            'name' => 'Administrateur'
        ], [
            'description' => 'Accès complet au système avec tous les privilèges administrateur',
            'color' => 'red',
            'is_system' => true,
        ]);

        // Assigner toutes les permissions à l'admin
        $allPermissions = Permission::all();
        $adminRole->permissions()->sync($allPermissions->pluck('id'));

        // Rôle Gestionnaire
        $managerRole = Role::firstOrCreate([
            'name' => 'Gestionnaire'
        ], [
            'description' => 'Gestion des fichiers et utilisateurs sans accès système',
            'color' => 'blue',
            'is_system' => true,
        ]);

        $managerPermissions = Permission::whereIn('name', [
            'files.read', 'files.write', 'files.delete', 'files.share', 'files.assign',
            'folders.read', 'folders.write', 'folders.delete', 'folders.manage',
            'users.read', 'users.write', 'users.assign_roles', 'users.manage_groups',
            'groups.read', 'groups.write', 'groups.assign_permissions',
            'roles.read',
            'reports.view', 'reports.export'
        ])->get();
        $managerRole->permissions()->sync($managerPermissions->pluck('id'));

        // Rôle Utilisateur Standard
        $userRole = Role::firstOrCreate([
            'name' => 'Utilisateur'
        ], [
            'description' => 'Utilisateur standard avec accès de base aux fichiers',
            'color' => 'green',
            'is_system' => true,
        ]);

        $userPermissions = Permission::whereIn('name', [
            'files.read', 'files.write', 'files.share',
            'folders.read', 'folders.write'
        ])->get();
        $userRole->permissions()->sync($userPermissions->pluck('id'));

        // Rôle Lecture Seule
        $readOnlyRole = Role::firstOrCreate([
            'name' => 'Lecture seule'
        ], [
            'description' => 'Accès en lecture uniquement',
            'color' => 'gray',
            'is_system' => true,
        ]);

        $readOnlyPermissions = Permission::whereIn('name', [
            'files.read',
            'folders.read'
        ])->get();
        $readOnlyRole->permissions()->sync($readOnlyPermissions->pluck('id'));

        // Rôle Modérateur
        $moderatorRole = Role::firstOrCreate([
            'name' => 'Modérateur'
        ], [
            'description' => 'Modération du contenu et gestion limitée des utilisateurs',
            'color' => 'purple',
            'is_system' => false,
        ]);

        $moderatorPermissions = Permission::whereIn('name', [
            'files.read', 'files.write', 'files.delete', 'files.share',
            'folders.read', 'folders.write', 'folders.delete',
            'users.read', 'users.write',
            'groups.read',
            'reports.view'
        ])->get();
        $moderatorRole->permissions()->sync($moderatorPermissions->pluck('id'));

        $this->command->info('Rôles créés avec succès !');
        $this->command->info('- Administrateur: ' . $adminRole->permissions->count() . ' permissions');
        $this->command->info('- Gestionnaire: ' . $managerRole->permissions->count() . ' permissions');
        $this->command->info('- Utilisateur: ' . $userRole->permissions->count() . ' permissions');
        $this->command->info('- Lecture seule: ' . $readOnlyRole->permissions->count() . ' permissions');
        $this->command->info('- Modérateur: ' . $moderatorRole->permissions->count() . ' permissions');
    }
}
