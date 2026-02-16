<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BuildMdLocations extends Command
{
    protected $signature = 'md:build-locations {--file=storage/app/md/localities.csv}';
    protected $description = 'Build districts + localities JSON for Moldova (no Transnistria), from CUATM CSV';

    public function handle(): int
    {
        $filePath = $this->option('file');

        // acceptă și path relativ, și absolut
        $abs = $filePath;
        if (!str_starts_with($abs, DIRECTORY_SEPARATOR) && !preg_match('/^[A-Z]:\\\\/i', $abs)) {
            $abs = base_path($filePath);
        }

        if (!file_exists($abs)) {
            $this->error("Nu găsesc fișierul: {$abs}");
            $this->line("Pune CSV aici: storage/app/md/localities.csv (sau dă --file=...)");
            return self::FAILURE;
        }

        $raw = file_get_contents($abs);
        if (!$raw || strlen(trim($raw)) < 10) {
            $this->error("Fișierul e gol sau invalid: {$abs}");
            return self::FAILURE;
        }

        // detectare delimitator ; sau ,
        $firstLine = strtok($raw, "\n");
        $delim = (substr_count((string)$firstLine, ';') > substr_count((string)$firstLine, ',')) ? ';' : ',';

        $rows = [];
        $fh = fopen($abs, 'r');
        if (!$fh) {
            $this->error("Nu pot deschide fișierul.");
            return self::FAILURE;
        }

        $header = fgetcsv($fh, 0, $delim);
        if (!$header) {
            $this->error("Nu pot citi header-ul CSV.");
            return self::FAILURE;
        }

        // normalize header
        $h = array_map(function ($x) {
            $x = trim((string)$x);
            $x = mb_strtolower($x);
            $x = str_replace([' ', '-', "\t"], '_', $x);
            return $x;
        }, $header);

        // helper: găsește coloană după mai multe variante posibile
        $findCol = function(array $candidates) use ($h) {
            foreach ($candidates as $cand) {
                $cand = mb_strtolower($cand);
                $cand = str_replace([' ', '-', "\t"], '_', $cand);
                $idx = array_search($cand, $h, true);
                if ($idx !== false) return $idx;
            }
            return null;
        };

        // CUATM tipic are ceva gen: cuatm/code, denumire/name, level, parent
        $colCode   = $findCol(['cuatm', 'code', 'cod', 'cod_cuatm', 'id']);
        $colName   = $findCol(['name', 'denumire', 'denumire_ro', 'name_ro', 'name_rom', 'denumirea']);
        $colLevel  = $findCol(['level', 'nivel', 'rank']);
        $colParent = $findCol(['parent', 'parent_code', 'parinte', 'id_parinte', 'parentid', 'parent_id']);

        if ($colCode === null || $colName === null) {
            $this->error("Nu pot detecta coloanele necesare (cod + denumire).");
            $this->line("Header detectat: " . implode(' | ', $header));
            $this->line("Te rog exportă CSV cu coloane pentru cod și denumire (RO).");
            return self::FAILURE;
        }

        // citim toate rândurile în memorie
        while (($r = fgetcsv($fh, 0, $delim)) !== false) {
            if (count($r) < 2) continue;
            $rows[] = $r;
        }
        fclose($fh);

        // Heuristic pentru level dacă nu există colLevel:
        // - cod raion de obicei mai scurt (ex 4 cifre)
        // - localitate mai lung (6/8/10 cifre)
        $getLevel = function($code, $row) use ($colLevel) {
            if ($colLevel !== null && isset($row[$colLevel])) {
                return trim((string)$row[$colLevel]);
            }
            $c = preg_replace('/\D+/', '', (string)$code);
            $len = strlen($c);
            if ($len <= 4) return 'district';
            return 'locality';
        };

        // listă simplă de excluderi Transnistria (denumiri care apar în CUATM)
        $isTransnistria = function(string $name): bool {
            $n = mb_strtolower($name);
            // orice mențiune comună care indică zona transnistreană
            return str_contains($n, 'transnistr') ||
                   str_contains($n, 'stînga nistrului') ||
                   str_contains($n, 'tiraspol') ||
                   str_contains($n, 'bender') ||
                   str_contains($n, 'tighina') ||
                   str_contains($n, 'grigoriopol') ||
                   str_contains($n, 'dubăsari') ||
                   str_contains($n, 'rîbni') ||
                   str_contains($n, 'slobozia') ||
                   str_contains($n, 'camenca') ||
                   str_contains($n, 'dnestrovsk');
        };

        // 1) indexăm toate entitățile: code => [name,parent,level]
        $items = [];
        foreach ($rows as $row) {
            $code = trim((string)$row[$colCode]);
            $name = trim((string)$row[$colName]);

            if ($code === '' || $name === '') continue;

            // dacă numele e transnistria, excludem
            if ($isTransnistria($name)) continue;

            $parent = ($colParent !== null && isset($row[$colParent])) ? trim((string)$row[$colParent]) : null;
            $level  = $getLevel($code, $row);

            $items[$code] = [
                'code' => $code,
                'name' => $name,
                'parent' => $parent,
                'level' => $level,
            ];
        }

        // 2) detectăm districte (raioane) – cele care par “district”
        $districtsByCode = [];
        foreach ($items as $code => $it) {
            if ($it['level'] === 'district') {
                $districtsByCode[$code] = $it['name'];
            }
        }

        if (empty($districtsByCode)) {
            $this->warn("Nu am detectat raioane prin 'level'. Folosesc fallback: coduri scurte (<=4 cifre).");
            foreach ($items as $code => $it) {
                $c = preg_replace('/\D+/', '', $code);
                if (strlen($c) <= 4) {
                    $districtsByCode[$code] = $it['name'];
                }
            }
        }

        // 3) legăm localitățile de raion
        // Preferăm parent_code (dacă există). Dacă nu, fallback pe primele 4 cifre.
        $localitiesMap = []; // districtName => [localityName...]
        foreach ($items as $code => $it) {
            if ($it['level'] !== 'locality') continue;

            $districtCode = null;

            // dacă avem parent și parent e district
            if (!empty($it['parent']) && isset($districtsByCode[$it['parent']])) {
                $districtCode = $it['parent'];
            } else {
                // fallback: primele 4 cifre din cod
                $digits = preg_replace('/\D+/', '', $code);
                if (strlen($digits) >= 4) {
                    $maybe = substr($digits, 0, 4);
                    // caută în cheile district (care pot fi și ele cu 0-uri)
                    foreach ($districtsByCode as $dCode => $dName) {
                        $dDigits = preg_replace('/\D+/', '', $dCode);
                        if ($dDigits === $maybe) {
                            $districtCode = $dCode;
                            break;
                        }
                    }
                }
            }

            if (!$districtCode) continue;

            $districtName = $districtsByCode[$districtCode] ?? null;
            if (!$districtName) continue;

            $localitiesMap[$districtName] ??= [];
            $localitiesMap[$districtName][] = $it['name'];
        }

        // sortare alfabetică
        $districts = array_values(array_unique(array_values($districtsByCode)));
        sort($districts, SORT_LOCALE_STRING);

        foreach ($localitiesMap as $dName => $list) {
            $list = array_values(array_unique($list));
            sort($list, SORT_LOCALE_STRING);
            $localitiesMap[$dName] = $list;
        }

        // scoatem raioanele fără localități (ca să nu apară junk)
        $districts = array_values(array_filter($districts, fn($d) => !empty($localitiesMap[$d] ?? [])));

        $payload = [
            'districts' => $districts,
            'localities' => $localitiesMap,
            'meta' => [
                'generated_at' => now()->toDateTimeString(),
                'source' => $filePath,
            ],
        ];

        Storage::disk('local')->put('md/locations.json', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info("✅ locations.json creat: storage/app/md/locations.json");
        $this->info("Raioane: " . count($districts));
        $this->info("Exemplu raion: " . ($districts[0] ?? 'N/A'));
        return self::SUCCESS;
    }
}
