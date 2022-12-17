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
        Schema::create('users_login_records', function (Blueprint $table) {
            $table->comment('使用者登入紀錄');
            $table->id()->comment('編號');
            $table->ipAddress('ip')->comment('IP位址');
            $table->string('host', 100)->comment('服務器域名');
            $table->string('user_agent')->comment('用戶代理');
            $table->timestamp('request_time')->comment('請求開始時間');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_login_records');
    }
};
