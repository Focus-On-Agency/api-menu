<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert the legacy name columns into JSON translation maps.
     */
    public function up(): void
    {
        foreach ($this->translatable() as $table) {
            $this->toJson($table);
        }
    }

    /**
     * Restore the legacy single-column name layout.
     */
    public function down(): void
    {
        foreach ($this->translatable() as $table) {
            $this->toString($table);
        }
    }

    /**
     * Tables whose name field is now translatable.
     *
     * @return array<int, string>
     */
    private function translatable(): array
    {
        return ['dishes', 'categories'];
    }

    /**
     * Backfill the current name into the Italian locale, then switch the
     * column type to JSON.
     */
    private function toJson(string $table): void
    {
        DB::table($table)->orderBy('id')->chunkById(500, function ($rows) use ($table) {
            foreach ($rows as $row) {
                $translations = filled($row->name ?? null)
                    ? ['it' => $row->name]
                    : null;

                DB::table($table)->where('id', $row->id)->update([
                    'name' => $translations === null
                        ? null
                        : json_encode($translations, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }
        });

        Schema::table($table, function (Blueprint $table) {
            $table->json('name')->nullable()->change();
        });
    }

    /**
     * Collapse the JSON map back to one string, preferring Italian.
     */
    private function toString(string $table): void
    {
        Schema::table($table, function (Blueprint $table) {
            $table->longText('name_legacy')->nullable();
        });

        DB::table($table)->orderBy('id')->chunkById(500, function ($rows) use ($table) {
            foreach ($rows as $row) {
                $translations = json_decode($row->name ?? '', true) ?: [];
                $fallback = $translations['it']
                    ?? collect($translations)->first(fn ($value) => filled($value))
                    ?? null;

                DB::table($table)->where('id', $row->id)->update([
                    'name_legacy' => $fallback,
                ]);
            }
        });

        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table($table, function (Blueprint $table) {
            $table->renameColumn('name_legacy', 'name');
        });
    }
};
