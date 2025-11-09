<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('usuarios')) {
            return;
        }

        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('usuarios', 'avatar')) {
                $table->string('avatar')->nullable()->after('google_id');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('usuarios')) {
            return;
        }

        Schema::table('usuarios', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('usuarios', 'google_id')) {
                $cols[] = 'google_id';
            }
            if (Schema::hasColumn('usuarios', 'avatar')) {
                $cols[] = 'avatar';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};