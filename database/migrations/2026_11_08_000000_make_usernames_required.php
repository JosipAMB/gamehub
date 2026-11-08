<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $usersWithoutUsername = DB::table('users')
            ->whereNull('username')
            ->orderBy('id')
            ->get(['id']);

        foreach ($usersWithoutUsername as $user) {
            $username = 'user'.$user->id;
            $suffix = 1;

            while (DB::table('users')->where('username', $username)->exists()) {
                $username = 'user'.$user->id.'-'.$suffix++;
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $username]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->change();
        });
    }
};
