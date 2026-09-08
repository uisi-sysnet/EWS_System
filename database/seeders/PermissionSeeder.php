<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // ========== CREATE PERMISSIONS ==========
        $permissions = [
            // Superadmin only permissions
            'view serial port setup',
            'manage system configuration',
            'manage database backup',
            'view audit logs',
            
            // Admin permissions
            'view api settings',
            'manage signals',
            'manage locations devices',
            'view statistics',
            'manage users',
            'view system logs',
            'create user',
            'edit user',
            'delete user',
            
            // Common user permissions
            'view beacon map',
            'view iot logs',
            'view dashboard',
            'edit own profile',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
        
        // ========== CREATE ROLES ==========
        // Superadmin Role
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $superadminRole->syncPermissions(Permission::all()); // All permissions
        
        // Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions([
            'view api settings',
            'manage signals',
            'manage locations devices',
            'view statistics',
            'manage users',
            'view system logs',
            'create user',
            'edit user',
            'delete user',
            'view beacon map',
            'view iot logs',
            'view dashboard',
        ]);
        
        // User Role
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions([
            'view beacon map',
            'view iot logs',
            'view dashboard',
            'edit own profile',
        ]);
        
        // ========== ASSIGN ROLES TO EXISTING USERS ==========
        $users = User::all();
        foreach ($users as $user) {
            // Map your existing user_level to Spatie roles
            switch ($user->user_level) {
                case 'superadmin':
                    $user->assignRole('superadmin');
                    break;
                case 'admin':
                    $user->assignRole('admin');
                    break;
                case 'user':
                    $user->assignRole('user');
                    break;
            }
        }
        
        $this->command->info('Permissions and roles seeded successfully!');
    }
}