<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTakeCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_take_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users")->onDelete("cascade");
            $table->foreignId("course_id")->constrained("courses")->onDelete("cascade");
            $table->boolean("completed")->default(0);
            $table->integer("grade");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_take_courses');
    }
}
