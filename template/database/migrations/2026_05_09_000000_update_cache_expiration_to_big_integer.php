<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->hasIntegerExpiration('cache')) {
            Schema::table('cache', function (Blueprint $table) {
                $table->bigInteger('expiration')->change();
            });
        }

        if ($this->hasIntegerExpiration('cache_locks')) {
            Schema::table('cache_locks', function (Blueprint $table) {
                $table->bigInteger('expiration')->change();
            });
        }
    }

    public function down(): void
    {
        if ($this->hasBigIntegerExpiration('cache')) {
            Schema::table('cache', function (Blueprint $table) {
                $table->integer('expiration')->change();
            });
        }

        if ($this->hasBigIntegerExpiration('cache_locks')) {
            Schema::table('cache_locks', function (Blueprint $table) {
                $table->integer('expiration')->change();
            });
        }
    }

    private function hasIntegerExpiration(string $table): bool
    {
        return $this->hasColumnOfType($table, 'expiration', 'integer');
    }

    private function hasBigIntegerExpiration(string $table): bool
    {
        return $this->hasColumnOfType($table, 'expiration', 'bigint');
    }

    private function hasColumnOfType(string $table, string $column, string $type): bool
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return false;
        }

        return Schema::getColumnType($table, $column) === $type;
    }
};
