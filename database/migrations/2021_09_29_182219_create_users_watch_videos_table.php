<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersWatchVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_watch_videos', function (Blueprint $table) {
            $table->foreignId("user_id")->constrained("users");
            $table->foreignId("video_id")->constrained("videos");
            $table->boolean("completed")->default(0);
            $table->time("left_at")->nullable();
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
        Schema::dropIfExists('users_watch_videos');
    }
}
