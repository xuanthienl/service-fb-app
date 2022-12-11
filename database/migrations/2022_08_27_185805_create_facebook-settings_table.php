<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacebookSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook-settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->nullable();
            $table->string('channel_type')->nullable();
            $table->string('channel_name')->nullable();
            $table->string('channel_price')->default(0)->nullable();
            $table->mediumText('channel_description')->nullable();
            $table->string('amount_min')->nullable();
            $table->string('amount_max')->nullable();
            $table->string('minutes_min')->nullable();
            $table->string('minutes_max')->nullable();
            $table->string('speed_min')->nullable();
            $table->string('speed_max')->nullable();
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
        Schema::dropIfExists('facebook-settings');
    }
}
