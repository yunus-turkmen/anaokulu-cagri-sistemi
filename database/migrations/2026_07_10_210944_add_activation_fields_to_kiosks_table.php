<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('kiosks', 'device_token_hash')) {
            Schema::table('kiosks', function (Blueprint $table): void {
                $table->string('device_token_hash', 64)
                    ->nullable()
                    ->after('api_key');
            });
        }

        if (! Schema::hasColumn('kiosks', 'activated_at')) {
            Schema::table('kiosks', function (Blueprint $table): void {
                $table->timestamp('activated_at')
                    ->nullable()
                    ->after('device_token_hash');
            });
        }

        if (! Schema::hasColumn('kiosks', 'user_agent')) {
            Schema::table('kiosks', function (Blueprint $table): void {
                $table->text('user_agent')
                    ->nullable()
                    ->after('ip_address');
            });
        }
    }

    public function down(): void
    {
        Schema::table('kiosks', function (Blueprint $table): void {
            $columns = [];

            foreach ([
                'device_token_hash',
                'activated_at',
                'user_agent',
            ] as $column) {
                if (Schema::hasColumn('kiosks', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};