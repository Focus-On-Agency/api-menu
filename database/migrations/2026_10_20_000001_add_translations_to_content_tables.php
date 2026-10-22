<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations: convert the legacy description columns into JSON
     * translation maps.
     */
    public function up(): void
    {
        foreach ($this->translatable() as $table => $sources) {
            $this->toJson($table, $sources);
        }
    }

    /**
     * Reverse the migrations: restore one text column per locale.
     */
    public function down(): void
    {
        foreach ($this->translatable() as $table => $sources) {
            $this->toColumns($table, $sources);
        }
    }

    /**
     * Legacy description columns per locale, used to backfill the JSON map.
     * After this migration every translatable table stores its description in
     * a single JSON column keyed by locale (spatie/laravel-translatable).
     *
     * @return array<string, array<string, string>>
     */
    private function translatable(): array
    {
        return [
            'dishes' => ['it' => 'description', 'en' => 'description_en'],
            'categories' => ['it' => 'description'],
            'allergens' => ['it' => 'description'],
        ];
    }

    /**
     * Merge the legacy description columns into a JSON map, then convert the
     * column type and drop the extra locale columns.
     *
     * @param array<string, string> $sources
     */
    private function toJson(string $table, array $sources): void
    {
        DB::table($table)->orderBy('id')->chunkById(500, function ($rows) use ($table, $sources) {
            foreach ($rows as $row) {
                $translations = [];

                foreach ($sources as $locale => $column) {
                    if (filled($row->{$column} ?? null)) {
                        $translations[$locale] = $row->{$column};
                    }
                }

                DB::table($table)->where('id', $row->id)->update([
                    'description' => $translations === []
                        ? null
                        : json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }
        });

        // Only dishes carried a second locale column.
        $legacyColumns = array_values(array_diff($sources, ['description']));

        if ($legacyColumns !== []) {
            Schema::table($table, function (Blueprint $table) use ($legacyColumns) {
                $table->dropColumn($legacyColumns);
            });
        }

        Schema::table($table, function (Blueprint $table) {
            $table->json('description')->nullable()->change();
        });
    }

    /**
     * Restore the legacy layout: one text column per locale.
     *
     * @param array<string, string> $sources
     */
    private function toColumns(string $table, array $sources): void
    {
        // Temporary text columns hold the decoded values while the JSON column
        // is dropped and renamed back into place.
        Schema::table($table, function (Blueprint $table) use ($sources) {
            foreach ($sources as $column) {
                $table->longText($column === 'description' ? 'description_legacy' : $column)->nullable();
            }
        });

        DB::table($table)->orderBy('id')->chunkById(500, function ($rows) use ($table, $sources) {
            foreach ($rows as $row) {
                $translations = json_decode($row->description ?? '', true) ?: [];
                $update = [];

                foreach ($sources as $locale => $column) {
                    $target = $column === 'description' ? 'description_legacy' : $column;
                    $update[$target] = $translations[$locale] ?? null;
                }

                DB::table($table)->where('id', $row->id)->update($update);
            }
        });

        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table($table, function (Blueprint $table) {
            $table->renameColumn('description_legacy', 'description');
        });
    }
};
