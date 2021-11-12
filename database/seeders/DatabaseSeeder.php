<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $user = new User();
        $user->username = "Sparkle.Admin";
        $user->first_name = "SparkleTMS";
        $user->last_name = "Admin";
        $user->email = "admin@sparkletms.tk";
        $user->password = "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi";
        $user->gender = 0;
        $user->role = "Manager";
        $user->phone_number = "+961 78844775";
        $user->profile_picture = "default.png";
        $user->save();
    }
}
