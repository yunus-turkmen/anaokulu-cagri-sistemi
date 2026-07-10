<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->string('qr_code', 191)->nullable()->unique();
            $table->string('card_uid', 191)->nullable()->unique();
            $table->string('photo')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropUnique(['qr_code']);
            $table->dropUnique(['card_uid']);
            $table->dropColumn(['qr_code', 'card_uid', 'photo']);
        });
    }
};