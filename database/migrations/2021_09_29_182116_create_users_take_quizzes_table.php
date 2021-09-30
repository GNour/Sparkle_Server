<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTakeQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_take_quizzes', function (Blueprint $table) {
            $table->foreignId("user_id")->constrained("users");
            $table->foreignId("quiz_id")->constrained("quizzes");
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
        Schema::dropIfExists('users_take_quizzes');
    }
}
