<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('themes', function (Blueprint $table) {
            $table->boolean('is_home_theme')->default(false)->after('banner_url');
        });

        DB::statement('CREATE UNIQUE INDEX themes_single_home_theme_idx ON themes (is_home_theme) WHERE is_home_theme = true');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS themes_single_home_theme_idx');

        Schema::table('themes', function (Blueprint $table) {
            $table->dropColumn('is_home_theme');
        });
    }
};
