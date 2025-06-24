<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Group;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le rôle administrateur
        $adminRole = Role::where('name', 'Administrateur')->first();
        $userRole = Role::where('name', 'Utilisateur')->first();

        if (!$adminRole) {
            $this->command->error('Le rôle Administrateur n\'existe pas. Veuillez d\'abord exécuter RoleSeeder.');
            return;
        }

        // Créer l'utilisateur administrateur principal
        $admin = User::firstOrCreate([
            'email' => 'admin@archivex.com'
        ], [
            'name' => 'Administrateur ArchiveX',
            'password' => Hash::make('admin123'), // Mot de passe temporaire
            'role_id' => $adminRole->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Ajouter l'admin aux groupes Direction et Équipe Technique
        $directionGroup = Group::where('name', 'Direction')->first();
        $techGroup = Group::where('name', 'Équipe Technique')->first();
        
        if ($directionGroup) {
            $admin->groups()->syncWithoutDetaching([$directionGroup->id]);
        }
        if ($techGroup) {
            $admin->groups()->syncWithoutDetaching([$techGroup->id]);
        }

        // Créer un gestionnaire de test
        $manager = User::firstOrCreate([
            'email' => 'manager@archivex.com'
        ], [
            'name' => 'Gestionnaire Test',
            'password' => Hash::make('manager123'),
            'role_id' => Role::where('name', 'Gestionnaire')->first()?->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Ajouter le gestionnaire au groupe Direction
        if ($directionGroup) {
            $manager->groups()->syncWithoutDetaching([$directionGroup->id]);
        }

        // Créer un utilisateur standard de test
        $user = User::firstOrCreate([
            'email' => 'user@archivex.com'
        ], [
            'name' => 'Utilisateur Test',
            'password' => Hash::make('user123'),
            'role_id' => $userRole?->id,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Ajouter l'utilisateur au groupe Équipe Commerciale
        $salesGroup = Group::where('name', 'Équipe Commerciale')->first();
        if ($salesGroup) {
            $user->groups()->syncWithoutDetaching([$salesGroup->id]);
        }

        // Créer quelques utilisateurs supplémentaires pour les tests
        $additionalUsers = [
            [
                'name' => 'Marie Dupont',
                'email' => 'marie.dupont@archivex.com',
                'role' => 'Utilisateur',
                'groups' => ['Équipe Commerciale']
            ],
            [
                'name' => 'Pierre Martin',
                'email' => 'pierre.martin@archivex.com',
                'role' => 'Utilisateur',
                'groups' => ['Équipe Technique']
            ],
            [
                'name' => 'Sophie Leroy',
                'email' => 'sophie.leroy@archivex.com',
                'role' => 'Gestionnaire',
                'groups' => ['Ressources Humaines']
            ],
            [
                'name' => 'Thomas Petit',
                'email' => 'thomas.petit@archivex.com',
                'role' => 'Utilisateur',
                'groups' => ['Comptabilité']
            ],
            [
                'name' => 'Julie Moreau',
                'email' => 'julie.moreau@archivex.com',
                'role' => 'Lecture seule',
                'groups' => ['Stagiaires']
            ]
        ];

        foreach ($additionalUsers as $userData) {
            $role = Role::where('name', $userData['role'])->first();
            if (!$role) continue;

            $newUser = User::firstOrCreate([
                'email' => $userData['email']
            ], [
                'name' => $userData['name'],
                'password' => Hash::make('password123'),
                'role_id' => $role->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            // Assigner aux groupes
            foreach ($userData['groups'] as $groupName) {
                $group = Group::where('name', $groupName)->first();
                if ($group) {
                    $newUser->groups()->syncWithoutDetaching([$group->id]);
                }
            }
        }

        $this->command->info('Utilisateurs créés avec succès !');
        $this->command->info('');
        $this->command->info('📧 Comptes de test créés :');
        $this->command->info('👑 Admin: admin@archivex.com / admin123');
        $this->command->info('🔧 Gestionnaire: manager@archivex.com / manager123');
        $this->command->info('👤 Utilisateur: user@archivex.com / user123');
        $this->command->info('');
        $this->command->info('⚠️  Pensez à changer les mots de passe en production !');
    }
}
