<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('kiosks', 'device_code')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->string('device_code', 16)
                    ->nullable()
                    ->unique()
                    ->after('id');
            });
        }

        if (! Schema::hasColumn('kiosks', 'api_key')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->string('api_key', 64)
                    ->nullable()
                    ->unique()
                    ->after('device_code');
            });
        }

        if (! Schema::hasColumn('kiosks', 'name')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->string('name')
                    ->nullable()
                    ->after('school_id');
            });
        }

        if (! Schema::hasColumn('kiosks', 'location')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->string('location')
                    ->nullable()
                    ->after('name');
            });
        }

        if (! Schema::hasColumn('kiosks', 'device_name')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->string('device_name')
                    ->nullable()
                    ->after('location');
            });
        }

        if (! Schema::hasColumn('kiosks', 'app_version')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->string('app_version')
                    ->nullable()
                    ->after('device_name');
            });
        }

        if (! Schema::hasColumn('kiosks', 'ip_address')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->string('ip_address', 45)
                    ->nullable()
                    ->after('app_version');
            });
        }

        if (! Schema::hasColumn('kiosks', 'last_seen_at')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->timestamp('last_seen_at')
                    ->nullable()
                    ->after('ip_address');
            });
        }

        if (! Schema::hasColumn('kiosks', 'status')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->string('status')
                    ->default('active')
                    ->after('last_seen_at');
            });
        }

        if (! Schema::hasColumn('kiosks', 'is_active')) {
            Schema::table('kiosks', function (Blueprint $table) {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('status');
            });
        }
    }

    public function down(): void
    {
        $columns = [
            'device_code',
            'device_name',
            'app_version',
            'ip_address',
            'last_seen_at',
            'location',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('kiosks', $column)) {
                Schema::table('kiosks', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        // api_key, name, status ve is_active önceden var olabileceği için silmiyoruz.
    }
};