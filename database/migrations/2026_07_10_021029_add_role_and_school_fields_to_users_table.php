<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable()->after('id');
            }

            if (! Schema::hasColumn('users', 'school_class_id')) {
                $table->unsignedBigInteger('school_class_id')
                    ->nullable()
                    ->after('school_id');
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 50)
                    ->default('school_admin')
                    ->after('password');
            }

            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)
                    ->default('active')
                    ->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'school_class_id')) {
                $table->dropColumn('school_class_id');
            }

            if (Schema::hasColumn('users', 'school_id')) {
                $table->dropColumn('school_id');
            }

            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};