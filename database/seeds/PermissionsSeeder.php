<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class PermissionsSeeder extends Seeder
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
            'client',
            'user'
        ];

        $permissions = [
            'profile' => 'r,u',
            'users' => 'c,r,u,d',
            'invoices' => 'c,r,u,d',
            'inventory' => 'c,r,u,d',
            'expenses' => 'c,r,u,d',
            'checks' => 'c,r,u,d',
            'contacts' => 'c,r,u,d',
            'projects' => 'c,r,u,d',
            'project-tasks' => 'c,r,u,d',
            'project-milestones' => 'c,r,u,d',
            'project-files' => 'c,r,u,d',
            'project-members' => 'c,r,u,d',
            'project-messages' => 'c,r,u,d',
            'blog' => 'c,r,u,d',
            'logs' => 'r,d',
            'settings' => 'r,d'
        ];

        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create permissions
        foreach ($permissions as $module => $perm) {
            $perms = explode(',', $perm);
            foreach ($perms as $p) {
                if ($p == 'c') {
                    Permission::create(['name' => 'create ' . $module]);
                }
                if ($p == 'r') {
                    Permission::create(['name' => 'read ' . $module]);
                }
                if ($p == 'u') {
                    Permission::create(['name' => 'update ' . $module]);
                }
                if ($p == 'd') {
                    Permission::create(['name' => 'delete ' . $module]);
                }
            }
        }

        // create roles and assign created permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'manager']);
        $role->givePermissionTo(['read profile', 'update profile']);

        $role = Role::create(['name' => 'client']);
        $role->givePermissionTo(['read profile', 'update profile']);

        $role = Role::create(['name' => 'user']);
        $role->givePermissionTo(['read profile', 'update profile']);

        //give admin all permissions
        $user = User::find(1);
        $user->assignRole('admin');
        $user->givePermissionTo(Permission::all());
    }
}
