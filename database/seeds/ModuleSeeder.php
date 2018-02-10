<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->truncate();
        $modules = [
            'profile',
            'users',
            'invoices',
            'inventory',
            'expenses',
            'checks',
            'contacts',
            'projects',
            'project-tasks',
            'project-milestones',
            'project-files',
            'project-members',
            'project-messages',
            'blog',
            'logs',
            'settings'
        ];
        foreach ($modules as $module) {
            $this->command->info("Creating  module " . $module);
            \App\Models\Modules::create(
                [
                    'name' => $module
                ]
            );
        }

        $this->command->info("All modules have been created!");
    }
}
