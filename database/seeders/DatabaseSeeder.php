<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\Team;
use App\Models\Todo;
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
        $user->username = "admin";
        $user->first_name = "admin";
        $user->last_name = "admin";
        $user->email = "admin@gmail.com";
        $user->password = "$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi";
        $user->gender = 0;
        $user->is_manager = 1;
        $user->phone_number = "+961 78844775";
        $user->profile_picture = "imgs/gsdf/sdf.jpg";
        $user->save();

        Course::factory(10)
            ->hasTasks(random_int(1, 3))
            ->hasVideos(random_int(1, 3))
            ->hasArticles(random_int(1, 3))
            ->has(Quiz::factory()->hasQuestions(3))
            ->create();
        Todo::factory(10)->hasTasks(random_int(0, 3))->create();

        Team::factory(10)->hasMembers(10)->create();
    }
}
