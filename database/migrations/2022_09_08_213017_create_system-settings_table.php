<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system-settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bot_token')->nullable();
            $table->string('bot_name')->nullable();
            $table->string('chat_id')->nullable();
            $table->string('chat_type')->nullable();
            $table->string('chat_title')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('account_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('bank_branch')->nullable();
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
        Schema::dropIfExists('system-settings');
    }
}
