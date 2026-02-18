<?php
// database/migrations/xxxx_add_background_color_and_icon_to_promos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->string('background_color')->nullable()->after('description');
            $table->string('icon')->nullable()->after('background_color');
        });
    }

    public function down()
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['background_color', 'icon']);
        });
    }
};