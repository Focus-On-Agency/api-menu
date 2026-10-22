<?php

namespace App\Console\Commands;

use App\Models\Allergen;
use App\Models\Dish;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MigrateDishMarkers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'menu:migrate-dish-markers {--apply : Persist the changes instead of only previewing them}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Estrae gli allergeni incorporati nei titoli dei piatti (numeri in apice e foglia vegetariana), li collega ai piatti e ripulisce i titoli.';

    /**
     * Superscript digits mapped back to their plain counterpart.
     *
     * @var array<string, string>
     */
    private const SUPERSCRIPTS = [
        '⁰' => '0',
        '¹' => '1',
        '²' => '2',
        '³' => '3',
        '⁴' => '4',
        '⁵' => '5',
        '⁶' => '6',
        '⁷' => '7',
        '⁸' => '8',
        '⁹' => '9',
    ];

    /**
     * Marker used for the vegetarian entries.
     */
    private const LEAF = '🌿';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        $allergens = Allergen::all();
        $byIdentifier = $this->identifierLookup($allergens);
        $leaf = $this->leafAllergen($allergens);

        if ($leaf === null) {
            $this->warn('Nessun allergene con icona foglia trovato: i marker foglia saranno segnalati come non risolti.');
        }

        $this->line($apply
            ? 'Modalità APPLICAZIONE: le modifiche verranno salvate.'
            : 'Modalità ANTEPRIMA: nessuna modifica verrà salvata (usa --apply).'
        );

        $rows = $this->collectRows($allergens, $byIdentifier, $leaf);

        if ($rows === []) {
            $this->info('Nessun piatto con marker da migrare.');

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Titolo attuale', 'Titolo pulito', 'Allergeni', 'Segnalazioni'],
            array_map(fn (array $row) => [
                $row['id'],
                $row['name'],
                $row['clean'],
                implode(', ', $row['allergens']),
                implode('; ', $row['issues']),
            ], $rows)
        );

        if (! $apply) {
            $this->line('Anteprima completata.');

            return self::SUCCESS;
        }

        $this->applyRows($rows);

        return self::SUCCESS;
    }

    /**
     * Build one report row per dish that carries at least one marker.
     *
     * @param Collection<int, Allergen> $allergens
     * @param array<string, array<int, Allergen>> $byIdentifier
     *
     * @return array<int, array<string, mixed>>
     */
    private function collectRows(Collection $allergens, array $byIdentifier, ?Allergen $leaf): array
    {
        $rows = [];

        foreach (Dish::query()->orderBy('id')->get() as $dish) {
            $markers = $this->extractMarkers($dish->name);

            if ($markers['identifiers'] === [] && ! $markers['leaf']) {
                continue;
            }

            $resolved = [];
            $unresolved = [];
            $ambiguous = [];

            foreach ($markers['identifiers'] as $identifier) {
                $matches = $byIdentifier[$identifier] ?? [];

                if (count($matches) === 1) {
                    $resolved[$matches[0]->id] = $matches[0];
                } elseif (count($matches) > 1) {
                    $ambiguous[] = $identifier;
                } else {
                    $unresolved[] = $identifier;
                }
            }

            if ($markers['leaf']) {
                if ($leaf) {
                    $resolved[$leaf->id] = $leaf;
                } else {
                    $unresolved[] = self::LEAF;
                }
            }

            $issues = [];

            if ($ambiguous !== []) {
                $issues[] = 'ambigui: ' . implode('/', $ambiguous);
            }

            if ($unresolved !== []) {
                $issues[] = 'non risolti: ' . implode('/', $unresolved);
            }

            $rows[] = [
                'id' => $dish->id,
                'name' => $dish->name,
                'clean' => $this->cleanName($dish->name),
                'allergens' => array_map(fn (Allergen $allergen) => $allergen->name, array_values($resolved)),
                'issues' => $issues,
                'resolved' => array_keys($resolved),
                'cleanable' => $issues === [],
            ];
        }

        return $rows;
    }

    /**
     * Persist the resolved allergens and the cleaned titles.
     *
     * @param array<int, array<string, mixed>> $rows
     */
    private function applyRows(array $rows): void
    {
        $linked = 0;
        $cleaned = 0;

        foreach ($rows as $row) {
            if ($row['resolved'] === []) {
                continue;
            }

            DB::transaction(function () use ($row, &$linked, &$cleaned) {
                $dish = Dish::find($row['id']);

                // Only add the missing associations, never remove the existing ones.
                $dish->allergens()->syncWithoutDetaching($row['resolved']);
                $linked++;

                // The title is cleaned only when every marker was resolved.
                if ($row['cleanable'] && $dish->name !== $row['clean']) {
                    $dish->name = $row['clean'];
                    $dish->save();
                    $cleaned++;
                }
            });
        }

        $this->info("Piatti con allergeni collegati: {$linked}");
        $this->info("Titoli ripuliti: {$cleaned}");
    }

    /**
     * Normalize the allergen identifiers so that "01" and "1" match the same entry.
     *
     * @param Collection<int, Allergen> $allergens
     *
     * @return array<string, array<int, Allergen>>
     */
    private function identifierLookup(Collection $allergens): array
    {
        $lookup = [];

        foreach ($allergens as $allergen) {
            $key = $this->normalize($allergen->name);

            if ($key === '') {
                continue;
            }

            $lookup[$key][] = $allergen;
        }

        return $lookup;
    }

    /**
     * Find the allergen that represents the vegetarian leaf.
     *
     * @param Collection<int, Allergen> $allergens
     */
    private function leafAllergen(Collection $allergens): ?Allergen
    {
        return $allergens->first(function (Allergen $allergen) {
            $haystack = trim(($allergen->name ?? '') . ' ' . ($allergen->icon ?? ''));

            return str_contains($haystack, self::LEAF)
                || preg_match('/leaf|foglia|vegan|vegetarian/i', $haystack) === 1;
        });
    }

    /**
     * Extract the identifiers and the leaf flag from a dish title.
     *
     * @return array{identifiers: array<int, string>, leaf: bool}
     */
    private function extractMarkers(string $name): array
    {
        $identifiers = [];

        if (preg_match_all('/[⁰¹²³⁴⁵⁶⁷⁸⁹]+/u', $name, $matches)) {
            foreach ($matches[0] as $sequence) {
                $identifiers[] = $this->normalize(strtr($sequence, self::SUPERSCRIPTS));
            }
        }

        return [
            'identifiers' => array_values(array_unique($identifiers)),
            'leaf' => str_contains($name, self::LEAF),
        ];
    }

    /**
     * Remove every marker from the title and collapse the leftover spaces.
     */
    private function cleanName(string $name): string
    {
        $clean = str_replace(self::LEAF, ' ', $name);
        $clean = preg_replace('/[⁰¹²³⁴⁵⁶⁷⁸⁹]+/u', ' ', $clean);
        $clean = preg_replace('/\s+/u', ' ', $clean);

        return trim($clean);
    }

    /**
     * Normalize a numeric identifier, leaving any other value untouched.
     */
    private function normalize(?string $value): string
    {
        $value = trim((string) $value);

        return preg_match('/^\d+$/', $value) === 1 ? (string) (int) $value : $value;
    }
}
