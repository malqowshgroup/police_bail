<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder {
    public function run(): void {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions grouped by module
        $permissions = [
            // policiers
            'policiers.voir','policiers.creer','policiers.modifier','policiers.supprimer',
            // logements
            'logements.voir','logements.creer','logements.modifier','logements.supprimer',
            // proprietaires
            'proprietaires.voir','proprietaires.creer','proprietaires.modifier',
            // contrats
            'contrats.voir','contrats.creer','contrats.modifier','contrats.suspendre','contrats.resilier','contrats.valider',
            // bordereaux
            'bordereaux.voir','bordereaux.creer','bordereaux.controler','bordereaux.valider',
            // reglements
            'reglements.voir','reglements.generer','reglements.valider',
            // documents
            'documents.voir','documents.uploader','documents.valider',
            // rapports
            'rapports.voir','rapports.exporter',
            // administration
            'administration.utilisateurs','administration.parametres',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $agentSaisie = Role::firstOrCreate(['name' => 'agent_saisie', 'guard_name' => 'web']);
        $agentSaisie->syncPermissions([
            'policiers.voir','policiers.creer','policiers.modifier',
            'logements.voir','logements.creer','logements.modifier',
            'proprietaires.voir','proprietaires.creer','proprietaires.modifier',
            'contrats.voir','contrats.creer','contrats.modifier',
            'bordereaux.voir','bordereaux.creer',
            'reglements.voir',
            'documents.voir','documents.uploader',
            'rapports.voir',
        ]);

        $agentControle = Role::firstOrCreate(['name' => 'agent_controle', 'guard_name' => 'web']);
        $agentControle->syncPermissions([
            'policiers.voir',
            'logements.voir',
            'proprietaires.voir',
            'contrats.voir','contrats.modifier',
            'bordereaux.voir','bordereaux.controler',
            'reglements.voir',
            'documents.voir','documents.valider',
            'rapports.voir',
        ]);

        $agentValidation = Role::firstOrCreate(['name' => 'agent_validation', 'guard_name' => 'web']);
        $agentValidation->syncPermissions([
            'policiers.voir',
            'logements.voir',
            'proprietaires.voir',
            'contrats.voir','contrats.valider','contrats.suspendre',
            'bordereaux.voir','bordereaux.valider',
            'reglements.voir','reglements.valider',
            'documents.voir','documents.valider',
            'rapports.voir','rapports.exporter',
        ]);

        $responsableBaux = Role::firstOrCreate(['name' => 'responsable_baux', 'guard_name' => 'web']);
        $responsableBaux->syncPermissions([
            'policiers.voir','policiers.creer','policiers.modifier',
            'logements.voir','logements.creer','logements.modifier','logements.supprimer',
            'proprietaires.voir','proprietaires.creer','proprietaires.modifier',
            'contrats.voir','contrats.creer','contrats.modifier','contrats.suspendre','contrats.resilier','contrats.valider',
            'bordereaux.voir','bordereaux.creer','bordereaux.controler','bordereaux.valider',
            'reglements.voir','reglements.generer','reglements.valider',
            'documents.voir','documents.uploader','documents.valider',
            'rapports.voir','rapports.exporter',
        ]);

        $directeurSolde = Role::firstOrCreate(['name' => 'directeur_solde', 'guard_name' => 'web']);
        $directeurSolde->syncPermissions([
            'policiers.voir',
            'logements.voir',
            'proprietaires.voir',
            'contrats.voir','contrats.valider','contrats.suspendre','contrats.resilier',
            'bordereaux.voir','bordereaux.valider',
            'reglements.voir','reglements.generer','reglements.valider',
            'documents.voir',
            'rapports.voir','rapports.exporter',
        ]);

        $administrateur = Role::firstOrCreate(['name' => 'administrateur', 'guard_name' => 'web']);
        $administrateur->syncPermissions(Permission::all());
    }
}
