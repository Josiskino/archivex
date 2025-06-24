<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Permissions Fichiers
            ['name' => 'files.read', 'description' => 'Lire les fichiers', 'category' => 'Fichiers'],
            ['name' => 'files.write', 'description' => 'Créer et modifier des fichiers', 'category' => 'Fichiers'],
            ['name' => 'files.delete', 'description' => 'Supprimer des fichiers', 'category' => 'Fichiers'],
            ['name' => 'files.share', 'description' => 'Partager des fichiers', 'category' => 'Fichiers'],
            ['name' => 'files.assign', 'description' => 'Assigner des fichiers à des utilisateurs/groupes', 'category' => 'Fichiers'],
            
            // Permissions Dossiers
            ['name' => 'folders.read', 'description' => 'Accéder aux dossiers', 'category' => 'Dossiers'],
            ['name' => 'folders.write', 'description' => 'Créer et modifier des dossiers', 'category' => 'Dossiers'],
            ['name' => 'folders.delete', 'description' => 'Supprimer des dossiers', 'category' => 'Dossiers'],
            ['name' => 'folders.manage', 'description' => 'Gérer l\'organisation des dossiers', 'category' => 'Dossiers'],
            
            // Permissions Utilisateurs
            ['name' => 'users.read', 'description' => 'Voir la liste des utilisateurs', 'category' => 'Utilisateurs'],
            ['name' => 'users.write', 'description' => 'Créer et modifier des utilisateurs', 'category' => 'Utilisateurs'],
            ['name' => 'users.delete', 'description' => 'Supprimer des utilisateurs', 'category' => 'Utilisateurs'],
            ['name' => 'users.assign_roles', 'description' => 'Assigner des rôles aux utilisateurs', 'category' => 'Utilisateurs'],
            ['name' => 'users.manage_groups', 'description' => 'Gérer l\'appartenance aux groupes', 'category' => 'Utilisateurs'],
            
            // Permissions Groupes
            ['name' => 'groups.read', 'description' => 'Voir les groupes', 'category' => 'Groupes'],
            ['name' => 'groups.write', 'description' => 'Créer et modifier des groupes', 'category' => 'Groupes'],
            ['name' => 'groups.delete', 'description' => 'Supprimer des groupes', 'category' => 'Groupes'],
            ['name' => 'groups.assign_permissions', 'description' => 'Assigner des permissions aux groupes', 'category' => 'Groupes'],
            
            // Permissions Rôles
            ['name' => 'roles.read', 'description' => 'Voir les rôles', 'category' => 'Rôles'],
            ['name' => 'roles.write', 'description' => 'Créer et modifier des rôles', 'category' => 'Rôles'],
            ['name' => 'roles.delete', 'description' => 'Supprimer des rôles', 'category' => 'Rôles'],
            ['name' => 'roles.assign_permissions', 'description' => 'Assigner des permissions aux rôles', 'category' => 'Rôles'],
            
            // Permissions Administration
            ['name' => 'admin.access', 'description' => 'Accès à l\'interface d\'administration', 'category' => 'Administration'],
            ['name' => 'admin.system', 'description' => 'Configuration système', 'category' => 'Administration'],
            ['name' => 'admin.logs', 'description' => 'Consulter les logs système', 'category' => 'Administration'],
            ['name' => 'admin.backup', 'description' => 'Gérer les sauvegardes', 'category' => 'Administration'],
            
            // Permissions Rapports
            ['name' => 'reports.view', 'description' => 'Consulter les rapports', 'category' => 'Rapports'],
            ['name' => 'reports.export', 'description' => 'Exporter les rapports', 'category' => 'Rapports'],
            ['name' => 'reports.create', 'description' => 'Créer des rapports personnalisés', 'category' => 'Rapports'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        $this->command->info('Permissions créées avec succès !');
    }
}
