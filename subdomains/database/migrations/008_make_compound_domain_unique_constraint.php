<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cloudflare_domains', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['name', 'prefix']);
        });
    }

    public function down(): void
    {
        Schema::table('cloudflare_domains', function (Blueprint $table) {
            $table->dropUnique(['name', 'prefix']);

            // Unique on 'name' only not restorable since it's a stricter requirement
        });
    }
};
