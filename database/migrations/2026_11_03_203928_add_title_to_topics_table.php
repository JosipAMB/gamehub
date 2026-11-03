<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->string('title');
            $table->text('body');
            $table->foreignId('thread_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            $table->dropForeign(['thread_id']);
            $table->dropForeign(['user_id']);

            $table->dropColumn([
                'title',
                'body',
                'thread_id',
                'user_id',
            ]);
        });
    }
};