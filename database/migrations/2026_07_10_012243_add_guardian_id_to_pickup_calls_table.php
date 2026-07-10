<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pickup_calls', function (Blueprint $table) {

            if (! Schema::hasColumn('pickup_calls', 'guardian_id')) {
                $table->unsignedBigInteger('guardian_id')
                    ->nullable()
                    ->after('student_id');

                $table->foreign('guardian_id')
                    ->references('id')
                    ->on('guardians')
                    ->nullOnDelete();
            }

        });
    }

    public function down(): void
    {
        Schema::table('pickup_calls', function (Blueprint $table) {

            $table->dropForeign(['guardian_id']);
            $table->dropColumn('guardian_id');

        });
    }
};