<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('config_key');
            $table->text('config_value')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'config_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_configs');
    }
};
