<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Démarrage du seeding de la base de données ArchiveX...');
        $this->command->info('');

        // 1. Permissions (base du système)
        $this->command->info('📋 Création des permissions...');
        $this->call(PermissionSeeder::class);
        $this->command->info('');

        // 2. Rôles avec leurs permissions
        $this->command->info('👥 Création des rôles...');
        $this->call(RoleSeeder::class);
        $this->command->info('');

        // 3. Utilisateurs avec rôles
        $this->command->info('🔑 Création des utilisateurs...');
        $this->call(AdminUserSeeder::class);
        $this->command->info('');

        // 4. Groupes avec permissions (nécessite des utilisateurs pour les créateurs)
        $this->command->info('🏢 Création des groupes...');
        $this->call(GroupSeeder::class);
        $this->command->info('');

        $this->command->info('✅ Base de données seedée avec succès !');
        $this->command->info('');
        $this->command->info('🎯 Résumé de ce qui a été créé :');
        $this->command->info('• Permissions système complètes');
        $this->command->info('• 5 rôles prédéfinis (Admin, Gestionnaire, Utilisateur, Lecture seule, Modérateur)');
        $this->command->info('• 8 utilisateurs de test');
        $this->command->info('• 6 groupes organisationnels');
        $this->command->info('');
        $this->command->info('🔐 Vous pouvez maintenant vous connecter avec :');
        $this->command->info('• admin@archivex.com / admin123 (Administrateur)');
        $this->command->info('• manager@archivex.com / manager123 (Gestionnaire)');
        $this->command->info('• user@archivex.com / user123 (Utilisateur)');
    }
}
