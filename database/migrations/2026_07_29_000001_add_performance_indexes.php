<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['game_id', 'status']);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->index('status');
            $table->index(['status', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['game_id', 'status']);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'sort_order']);
        });
    }
};