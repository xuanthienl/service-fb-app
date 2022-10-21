<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'username'  => "admin",
                'name'      => "Xanh™",
                'email'     => 'admin@supportlive.me',
                'roles'     => '1',
                'password'  =>  bcrypt('0398140111a'),
            ],
            [
                'username'  => "admin1",
                'name'      => "black Red™",
                'email'     => 'admin1@supportlive.me',
                'roles'     => '1',
                'password'  =>  bcrypt('0357789143a'),
            ],
        ]);
    }
}
