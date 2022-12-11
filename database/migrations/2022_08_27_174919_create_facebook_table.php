<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id', 50);
            $table->string('uid', 255);
            $table->string('type');
            $table->string('status')->default(0);
            $table->string('channel')->nullable();
            $table->string('amount', 10)->default(0);
            $table->string('speed', 10)->nullable();
            $table->string('minutes', 10)->nullable();
            $table->mediumText('content')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('total_payment', 255)->default(0);
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
        Schema::dropIfExists('facebook');
    }
}
