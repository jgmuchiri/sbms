<?php

use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Use this user for login as admin
        User::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@app.com',
            'password' => bcrypt('password'),
            'confirmed' => 1,
            'role' => 'admin',
        ]);

        //creating 10 test users
        factory(User::class, 10)->create();

    }
}
