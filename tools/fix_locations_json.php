<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$root = realpath(__DIR__ . '/..');

$xlsx = $root . '/storage/app/md/CUATM_2025.xlsx';
$json = $root . '/storage/app/md/locations.json';

if (!file_exists($xlsx)) die("Nu găsesc XLSX: $xlsx\n");
if (!file_exists($json)) die("Nu găsesc JSON: $json\n");

$spreadsheet = IOFactory::load($xlsx);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);

// map cod -> denumire (A=CodUnic, F=DenumireRO)
$nameByCode = [];
foreach ($rows as $i => $r) {
    if ($i === 1) continue; // header
    $code = trim((string)($r['A'] ?? ''));
    $name = trim((string)($r['F'] ?? ''));
    if ($code !== '' && $name !== '') {
        $nameByCode[$code] = $name;
    }
}

$data = json_decode(file_get_contents($json), true);
$districtCodes = $data['districts'] ?? [];
$mapCodes = $data['localities'] ?? [];

$districtNames = [];
foreach ($districtCodes as $c) {
    $c = trim((string)$c);
    $districtNames[] = $nameByCode[$c] ?? $c;
}

$newMap = [];
foreach ($mapCodes as $districtCode => $locCodes) {
    $districtCode = trim((string)$districtCode);
    $districtName = $nameByCode[$districtCode] ?? $districtCode;

    $locNames = [];
    if (is_array($locCodes)) {
        foreach ($locCodes as $lc) {
            $lc = trim((string)$lc);
            $locNames[] = $nameByCode[$lc] ?? $lc;
        }
    }

    $locNames = array_values(array_unique($locNames));
    sort($locNames, SORT_LOCALE_STRING);

    $newMap[$districtName] = $locNames;
}

// scoatem transnistria dacă apare
$districtNames = array_values(array_filter($districtNames, function ($d) {
    $x = mb_strtolower(trim((string)$d));
    return !str_contains($x, 'transn') && !str_contains($x, 'tiraspol') && !str_contains($x, 'stinga nistr');
}));

sort($districtNames, SORT_LOCALE_STRING);

$out = [
    'districts' => $districtNames,
    'localities' => $newMap,
];

file_put_contents($json, json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "OK: locations.json rescris cu denumiri.\n";
