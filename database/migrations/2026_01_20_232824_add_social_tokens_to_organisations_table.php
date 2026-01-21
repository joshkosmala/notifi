<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            // Facebook Page integration
            $table->string('facebook_page_id')->nullable();
            $table->string('facebook_page_name')->nullable();
            $table->text('facebook_page_token')->nullable();

            // X (Twitter) integration
            $table->string('x_user_id')->nullable();
            $table->string('x_username')->nullable();
            $table->text('x_access_token')->nullable();
            $table->text('x_refresh_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organisations', function (Blueprint $table) {
            $table->dropColumn([
                'facebook_page_id',
                'facebook_page_name',
                'facebook_page_token',
                'x_user_id',
                'x_username',
                'x_access_token',
                'x_refresh_token',
            ]);
        });
    }
};
