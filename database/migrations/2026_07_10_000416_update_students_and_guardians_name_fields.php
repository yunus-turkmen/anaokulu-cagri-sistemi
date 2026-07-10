<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $table) {
                if (! Schema::hasColumn('students', 'first_name')) {
                    $table->string('first_name')->nullable()->after('school_class_id');
                }

                if (! Schema::hasColumn('students', 'last_name')) {
                    $table->string('last_name')->nullable()->after('first_name');
                }

                if (! Schema::hasColumn('students', 'student_no')) {
                    $table->string('student_no')->nullable()->after('school_class_id');
                }

                if (! Schema::hasColumn('students', 'gender')) {
                    $table->string('gender')->nullable()->after('last_name');
                }
            });

            if (Schema::hasColumn('students', 'name')) {
                DB::statement("UPDATE students SET first_name = name WHERE first_name IS NULL");
            }
        }

        if (Schema::hasTable('guardians')) {
            Schema::table('guardians', function (Blueprint $table) {
                if (! Schema::hasColumn('guardians', 'first_name')) {
                    $table->string('first_name')->nullable()->after('school_id');
                }

                if (! Schema::hasColumn('guardians', 'last_name')) {
                    $table->string('last_name')->nullable()->after('first_name');
                }

                if (! Schema::hasColumn('guardians', 'can_pickup')) {
                    $table->boolean('can_pickup')->default(true)->after('relationship');
                }

                if (! Schema::hasColumn('guardians', 'emergency_contact')) {
                    $table->boolean('emergency_contact')->default(false)->after('can_pickup');
                }
            });

            if (Schema::hasColumn('guardians', 'name')) {
                DB::statement("UPDATE guardians SET first_name = name WHERE first_name IS NULL");
            }
        }
    }

    public function down(): void
    {
        //
    }
};
