<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = Schema::getColumnListing('replies');

        Schema::table('replies', function (Blueprint $table) use ($columns) {
            if (! in_array('body', $columns, true)) {
                $table->text('body')->nullable();
            }

            if (! in_array('topic_id', $columns, true)) {
                $table->foreignId('topic_id')->nullable()->constrained()->cascadeOnDelete();
            }

            if (! in_array('user_id', $columns, true)) {
                $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        $columns = collect(Schema::getColumns('replies'))
            ->where('nullable', true)
            ->pluck('name')
            ->filter(fn (string $column) => in_array($column, ['body', 'topic_id', 'user_id'], true))
            ->all();

        if ($columns === []) {
            return;
        }

        Schema::table('replies', function (Blueprint $table) use ($columns) {
            foreach (['topic_id', 'user_id'] as $foreignId) {
                if (in_array($foreignId, $columns, true)) {
                    $table->dropConstrainedForeignId($foreignId);
                }
            }

            $body = array_values(array_intersect($columns, ['body']));
            if ($body !== []) {
                $table->dropColumn($body);
            }
        });
    }
};
