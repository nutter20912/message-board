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
        Schema::create('user_relationship', function (Blueprint $table) {
            $table->comment('使用者關係');
            $table->id()->comment('編號');
            $table->tinyInteger('type')->comment('類型');
            $table->timestamps();

            $table->foreignId('owner_id')->comment('所有者編號')->constrained('users');
            $table->foreignId('child_id')->comment('下層編號')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_relationship');
    }
};
