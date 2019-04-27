<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'admin',
            'manager',
            'staff',
            'user',
            'contractor',
            'accountant',
            'sales',
        ];
        foreach ($roles as $role) {
            \App\Models\Role::create(['name' => $role]);
        }
    }
}
