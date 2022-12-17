<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_notifications', function (Blueprint $table) {
            $table->comment('使用者通知');
            $table->id()->comment('編號');
            $table->string('content')->comment('內容');
            $table->timestamp('created_at')->useCurrent()->comment('建立時間');

            $table->foreignId('user_id')->constrained(table: 'users');
            $table->morphs('notifiable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_notifications');
    }
};
