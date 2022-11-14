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
        Schema::create('comments', function (Blueprint $table) {
            $table->comment('評論');
            $table->id()->comment('編號');
            $table->string('content')->comment('內容');
            $table->timestamps();

            $table->foreignId('post_id')->constrained(table: 'posts');
            $table->foreignId('user_id')->constrained(table: 'users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
