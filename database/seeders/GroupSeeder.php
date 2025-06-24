<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Group;
use App\Models\Permission;
use App\Models\User;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // S'assurer qu'il y a au moins un utilisateur admin pour créer les groupes
        $adminUser = User::whereHas('role', function($query) {
            $query->where('name', 'Administrateur');
        })->first();

        if (!$adminUser) {
            $this->command->warn('Aucun utilisateur admin trouvé. Les groupes seront créés sans créateur.');
        }

        // Groupe Équipe Technique
        $techGroup = Group::firstOrCreate([
            'name' => 'Équipe Technique'
        ], [
            'description' => 'Développeurs, administrateurs système et techniciens IT',
            'color' => 'purple',
            'created_by' => $adminUser?->id,
        ]);

        $techPermissions = Permission::whereIn('name', [
            'files.read', 'files.write', 'files.delete', 'files.share',
            'folders.read', 'folders.write', 'folders.delete', 'folders.manage',
            'admin.logs', 'admin.backup',
            'reports.view', 'reports.export', 'reports.create'
        ])->get();
        $techGroup->permissions()->sync($techPermissions->pluck('id'));

        // Groupe Équipe Commerciale
        $salesGroup = Group::firstOrCreate([
            'name' => 'Équipe Commerciale'
        ], [
            'description' => 'Commerciaux, marketing et relations clients',
            'color' => 'orange',
            'created_by' => $adminUser?->id,
        ]);

        $salesPermissions = Permission::whereIn('name', [
            'files.read', 'files.write', 'files.share',
            'folders.read', 'folders.write',
            'reports.view', 'reports.export'
        ])->get();
        $salesGroup->permissions()->sync($salesPermissions->pluck('id'));

        // Groupe Direction
        $managementGroup = Group::firstOrCreate([
            'name' => 'Direction'
        ], [
            'description' => 'Direction générale et management',
            'color' => 'indigo',
            'created_by' => $adminUser?->id,
        ]);

        $managementPermissions = Permission::whereIn('name', [
            'files.read', 'files.write', 'files.delete', 'files.share', 'files.assign',
            'folders.read', 'folders.write', 'folders.delete', 'folders.manage',
            'users.read', 'users.write', 'users.assign_roles',
            'groups.read', 'groups.write',
            'reports.view', 'reports.export', 'reports.create'
        ])->get();
        $managementGroup->permissions()->sync($managementPermissions->pluck('id'));

        // Groupe Ressources Humaines
        $hrGroup = Group::firstOrCreate([
            'name' => 'Ressources Humaines'
        ], [
            'description' => 'Gestion du personnel et administration RH',
            'color' => 'pink',
            'created_by' => $adminUser?->id,
        ]);

        $hrPermissions = Permission::whereIn('name', [
            'files.read', 'files.write', 'files.share',
            'folders.read', 'folders.write',
            'users.read', 'users.write', 'users.manage_groups',
            'reports.view', 'reports.export'
        ])->get();
        $hrGroup->permissions()->sync($hrPermissions->pluck('id'));

        // Groupe Comptabilité
        $financeGroup = Group::firstOrCreate([
            'name' => 'Comptabilité'
        ], [
            'description' => 'Service comptabilité et finances',
            'color' => 'green',
            'created_by' => $adminUser?->id,
        ]);

        $financePermissions = Permission::whereIn('name', [
            'files.read', 'files.write', 'files.share',
            'folders.read', 'folders.write',
            'reports.view', 'reports.export', 'reports.create'
        ])->get();
        $financeGroup->permissions()->sync($financePermissions->pluck('id'));

        // Groupe Stagiaires
        $internGroup = Group::firstOrCreate([
            'name' => 'Stagiaires'
        ], [
            'description' => 'Stagiaires et personnel temporaire',
            'color' => 'yellow',
            'created_by' => $adminUser?->id,
        ]);

        $internPermissions = Permission::whereIn('name', [
            'files.read',
            'folders.read'
        ])->get();
        $internGroup->permissions()->sync($internPermissions->pluck('id'));

        $this->command->info('Groupes créés avec succès !');
        $this->command->info('- Équipe Technique: ' . $techGroup->permissions->count() . ' permissions');
        $this->command->info('- Équipe Commerciale: ' . $salesGroup->permissions->count() . ' permissions');
        $this->command->info('- Direction: ' . $managementGroup->permissions->count() . ' permissions');
        $this->command->info('- Ressources Humaines: ' . $hrGroup->permissions->count() . ' permissions');
        $this->command->info('- Comptabilité: ' . $financeGroup->permissions->count() . ' permissions');
        $this->command->info('- Stagiaires: ' . $internGroup->permissions->count() . ' permissions');
    }
}
