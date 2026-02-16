<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BuildMdLocationsFromXlsx extends Command
{
    protected $signature = 'md:build-locations-xlsx 
        {--path=storage/app/md/CUATM_2025.xlsx : Path către fișierul XLSX}
        {--out=storage/app/md/locations.json : Unde salvăm JSON-ul}';

    protected $description = 'Construiește locations.json (raioane + localități) din CUATM_2025.xlsx folosind DenumireRO';

    public function handle(): int
    {
        $path = $this->option('path');
        $out  = $this->option('out');

        if (!file_exists($path)) {
            $this->error("Nu găsesc XLSX la: {$path}");
            return self::FAILURE;
        }

        $this->info("Citesc XLSX: {$path}");

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (!$rows || count($rows) < 2) {
            $this->error("Fișierul pare gol.");
            return self::FAILURE;
        }

        // Headere (prima linie)
        $header = $rows[1];

        // Detectăm coloanele după nume (ca să nu depindem de A/B/C...)
        $col = $this->findColumns($header, [
            'CodStatistic'       => ['CodStatistic', 'codstatistic', 'CODSTATISTIC'],
            'ParentCodStatistic' => ['ParentCodStatistic', 'parentcodstatistic', 'PARENTCODSTATISTIC'],
            'Statut'             => ['Statut', 'statut', 'STATUT'],
            'DenumireRO'         => ['DenumireRO', 'denumirero', 'DENUMIRERO'],
        ]);

        foreach (['CodStatistic','ParentCodStatistic','Statut','DenumireRO'] as $need) {
            if (!isset($col[$need])) {
                $this->error("Nu găsesc coloana: {$need}. Verifică headerele din XLSX.");
                $this->line("Headere detectate: " . implode(', ', array_values($header)));
                return self::FAILURE;
            }
        }

        // Citim toate rândurile
        $items = [];
        for ($i = 2; $i <= count($rows); $i++) {
            $r = $rows[$i];

            $code = $this->asCode($r[$col['CodStatistic']] ?? '');
            $parent = $this->asCode($r[$col['ParentCodStatistic']] ?? '');
            $statut = trim((string)($r[$col['Statut']] ?? ''));
            $name = trim((string)($r[$col['DenumireRO']] ?? ''));

            if ($code === '' || $name === '') continue;

            $items[$code] = [
                'code' => $code,
                'parent' => $parent,
                'statut' => $statut,
                'name' => $this->cleanName($name),
            ];
        }

        // 1) Determinăm “raioanele” ca unități cu:
        //    - CodStatistic care se termină în "000"
        //    - ParentCodStatistic care se termină în "000000"
        // Asta îți dă Raioane (și exclude sectoarele Chișinău care au altă structură).
        $districtCodes = [];
        foreach ($items as $code => $it) {
            if ($this->endsWithZeros($it['code'], 3) && $this->endsWithZeros($it['parent'], 6)) {
                $districtCodes[] = $code;
            }
        }

        // Excludem Transnistria (listă blacklist)
        $blacklist = [
            'Tiraspol','Bender','Tighina','Rîbnița','Ribnita','Camenca','Kamenka',
            'Grigoriopol','Slobozia','Dubăsari (stînga nistrului)','Dubsari (stinga nistrului)',
            'Stînga Nistrului','Stinga Nistrului','Transnistr'
        ];

        $districts = [];        // [districtName]
        $districtMap = [];      // [districtName => [localityName,...]]

        // 2) Construim map: districtCode -> districtName
        $districtCodeToName = [];
        foreach ($districtCodes as $dc) {
            $dn = $items[$dc]['name'] ?? null;
            if (!$dn) continue;

            if ($this->containsAny($dn, $blacklist)) {
                continue; // eliminăm Transnistria
            }

            $districtCodeToName[$dc] = $dn;
        }

        // 3) Pentru fiecare item: dacă parent este un districtCode, îl adăugăm ca localitate
        foreach ($items as $code => $it) {
            $parent = $it['parent'];
            if (!isset($districtCodeToName[$parent])) continue;

            $districtName = $districtCodeToName[$parent];
            $localityName = $it['name'];

            // Exclude linii care sunt chiar districtul (cod=parent etc)
            if ($localityName === $districtName) continue;

            $districtMap[$districtName][] = $localityName;
        }

        // Sortare + unique
        foreach ($districtMap as $d => $locs) {
            $locs = array_values(array_unique($locs));
            sort($locs, SORT_LOCALE_STRING);
            $districtMap[$d] = $locs;
        }

        $districts = array_keys($districtMap);
        sort($districts, SORT_LOCALE_STRING);

        $payload = [
            'districts' => $districts,
            'localities' => $districtMap,
            'meta' => [
                'source' => basename($path),
                'generated_at' => now()->toDateTimeString(),
                'excluded_transnistria' => true,
            ],
        ];

        // Salvăm JSON
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $this->ensureDir(dirname($out));
        file_put_contents($out, $json);

        $this->info("✅ Gata! Am generat: {$out}");
        $this->info("Raioane: " . count($districts));
        $this->info("Exemplu raion: " . ($districts[0] ?? '-'));

        return self::SUCCESS;
    }

    private function findColumns(array $headerRow, array $wanted): array
    {
        // headerRow este gen: ['A'=>'CodUnic', 'B'=>'ParentCodUnic', ...]
        $map = [];
        foreach ($wanted as $key => $aliases) {
            foreach ($headerRow as $letter => $title) {
                $t = strtolower(trim((string)$title));
                foreach ($aliases as $a) {
                    if ($t === strtolower($a)) {
                        $map[$key] = $letter;
                    }
                }
            }
        }
        return $map;
    }

    private function asCode(string $v): string
    {
        $v = trim((string)$v);
        if ($v === '') return '';
        // păstrăm leading zeros
        $v = preg_replace('/\D+/', '', $v);
        return $v ?? '';
    }

    private function endsWithZeros(string $code, int $zeros): bool
    {
        if ($code === '') return false;
        return str_ends_with($code, str_repeat('0', $zeros));
    }

    private function cleanName(string $name): string
    {
        // curățăm diacritice dubioase / spații
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    private function containsAny(string $text, array $needles): bool
    {
        $t = mb_strtolower($text);
        foreach ($needles as $n) {
            if (mb_strpos($t, mb_strtolower($n)) !== false) return true;
        }
        return false;
    }

    private function ensureDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }
}
