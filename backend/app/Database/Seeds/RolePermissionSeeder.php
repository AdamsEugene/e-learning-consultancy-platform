<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Seed roles
        $roles = [
            ['name' => 'super admin'],
            ['name' => 'admin'],
            ['name' => 'user'],
            ['name' => 'guest'],
        ];

        foreach ($roles as $role) {
            $this->db->table('roles')->insert($role);
        }

        // Seed permissions
        $permissions = [
            ['name' => 'admin'],
            ['name' => 'view'],
            ['name' => 'edit'],
        ];

        foreach ($permissions as $permission) {
            $this->db->table('permissions')->insert($permission);
        }
        
    }
}
