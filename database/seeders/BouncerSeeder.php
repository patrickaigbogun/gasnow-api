<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;

class BouncerSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache in case this is re-run
        Bouncer::refresh();

        // ---- Roles ----
        $roles = [
            'customer',
            'system-admin',
            'accounts',
            'customer-service',
            'setup-admin',
            'audit',
        ];

        foreach ($roles as $role) {
            Bouncer::role()->firstOrCreate(['name' => $role]);
        }

        // ---- Abilities ----
        $abilities = [
            // Customer
            'create-purchase', 'create-billing', 'read-billing', 'update-password',

            // System Admin
            'everything',

            // Accounts
            'read-purchases', 'read-billing', 'update-passwords',

            // Customer Service
            'read-purchases', 'update-purchases', 'delete-purchases', 'read-profiles', 'read-passwords',

            // Backend Admin
            'create-setup-admin', 'read-setup-admin', 'update-setup-admin', 'delete-setup-admin',
            'update-passwords', 'create-setup-system', 'read-setup-system', 'update-setup-system', 'delete-setup-system',

            // Audit
            'read-audit', 'update-password',
        ];

        foreach ($abilities as $ability) {
            Bouncer::ability()->firstOrCreate(['name' => $ability]);
        }

        // ---- Role Abilities ----
        Bouncer::allow('customer')->to(['create-purchase', 'create-billing', 'read-billing', 'update-password']);
        Bouncer::allow('system-admin')->everything();
        Bouncer::allow('accounts')->to(['read-purchases', 'read-billing', 'update-passwords']);
        Bouncer::allow('customer-service')->to(['read-purchases', 'update-purchases', 'delete-purchases', 'read-profiles', 'read-passwords']);
        Bouncer::allow('setup-admin')->to([
            'create-setup-admin', 'read-setup-admin', 'update-setup-admin', 'delete-setup-admin',
            'update-passwords', 'create-setup-system', 'read-setup-system', 'update-setup-system', 'delete-setup-system'
        ]);
        Bouncer::allow('audit')->to(['read-audit', 'update-password']);

        $this->command->info('Bouncer roles and abilities seeded successfully.');
    }
}
