<?php

declare(strict_types=1);
/**
 * This file is part of the extension library for Hyperf.
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateUserSocialitesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_socialites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email');
            $table->string('provider_id');
            $table->enum('provider_name', ['google', 'github', 'gitlab', 'facebook', 'linkedin', 'bitbucket']);
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            if (Schema::hasTable('users')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_socialites');
    }
}
