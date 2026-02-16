<?php
// tools/build_locations_from_xlsx.php
// Rulare: php tools/build_locations_from_xlsx.php storage/app/md/CUATM_2025.xlsx

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($argc < 2) {
    echo "Usage: php tools/build_locations_from_xlsx.php storage/app/md/CUATM_2025.xlsx\n";
    exit(1);
}

$xlsxPath = $argv[1];
if (!file_exists($xlsxPath)) {
    echo "Nu găsesc XLSX: {$xlsxPath}\n";
    exit(1);
}

$storageDir = __DIR__ . '/../storage/app/md';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0777, true);
}

function cleanName(?string $name): string {
    $name = trim((string)$name);
    if ($name === '') return '';

    // taie după prima virgulă (ex: "Buciumeni, loc.st.c.f." -> "Buciumeni")
    $name = preg_replace('/\s*,\s*.*$/u', '', $name);

    // normalizează spații
    $name = preg_replace('/\s+/u', ' ', $name);

    return trim($name);
}

function isTransnistriaLike(string $name): bool {
    $n = mb_strtolower($name);
    // filtre generale (poți extinde)
    return str_contains($n, 'transn')
        || str_contains($n, 'stinga nistr')
        || str_contains($n, 'tiraspol')
        || str_contains($n, 'bender')
        || str_contains($n, 'tighina')
        || str_contains($n, 'dubasari (st') // uneori apare "Dubăsari (stînga Nistrului)"
        ;
}

$spreadsheet = IOFactory::load($xlsxPath);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);

// Detectăm header-ul (coloanele tale: CodUnic, ParentCodUnic, ... DenumireRO)
$headerRowIndex = null;
$colMap = []; // ex: ['codunic'=>'A', ...]
foreach ($rows as $i => $r) {
    $cells = array_map(fn($v) => is_string($v) ? trim($v) : $v, $r);
    if (
        (isset($cells['A']) && strtolower((string)$cells['A']) === 'codunic') ||
        (in_array('CodUnic', $cells, true) && in_array('DenumireRO', $cells, true))
    ) {
        $headerRowIndex = $i;
        foreach ($cells as $col => $name) {
            $k = strtolower(trim((string)$name));
            $colMap[$k] = $col;
        }
        break;
    }
}

if ($headerRowIndex === null) {
    echo "Nu am găsit header-ul (CodUnic / ParentCodUnic / DenumireRO) în XLSX.\n";
    exit(1);
}

$required = ['codunic', 'parentcodunic', 'denumirero'];
foreach ($required as $k) {
    if (!isset($colMap[$k])) {
        echo "Lipsește coloana: {$k}. Header detectat: " . implode(', ', array_keys($colMap)) . "\n";
        exit(1);
    }
}

$COD = $colMap['codunic'];
$PARENT = $colMap['parentcodunic'];
$NAME = $colMap['denumirero'];

// Citim toate nodurile
$nodes = [];       // cod => ['cod'=>, 'parent'=>, 'name'=>]
$children = [];    // parentCod => [childCod, ...]
foreach ($rows as $i => $r) {
    if ($i <= $headerRowIndex) continue;

    $cod = isset($r[$COD]) ? trim((string)$r[$COD]) : '';
    $parent = isset($r[$PARENT]) ? trim((string)$r[$PARENT]) : '';
    $name = isset($r[$NAME]) ? cleanName((string)$r[$NAME]) : '';

    if ($cod === '' || $name === '') continue;

    $nodes[$cod] = ['cod' => $cod, 'parent' => $parent, 'name' => $name];
    $children[$parent][] = $cod;
}

// Determinăm "districts" ca fiind copiii rădăcinii.
// În CUATM, rădăcina de obicei are parent = "" sau "0" sau "0000".
// Luăm toate aceste variante și le unim.
$rootParents = ['', '0', '0000', '00000', '000000'];
$districtCods = [];
foreach ($rootParents as $rp) {
    if (!empty($children[$rp])) {
        foreach ($children[$rp] as $c) $districtCods[$c] = true;
    }
}
$districtCods = array_keys($districtCods);

// Dacă aici îți intră și "Republica Moldova", îl scoatem.
$districtCods = array_values(array_filter($districtCods, function($cod) use ($nodes) {
    $n = $nodes[$cod]['name'] ?? '';
    $low = mb_strtolower($n);
    if ($low === 'republica moldova' || $low === 'moldova') return false;
    return true;
}));

// Mapare districtName => localities[]
$districtNames = [];
$localitiesMap = [];

foreach ($districtCods as $dCod) {
    if (!isset($nodes[$dCod])) continue;

    $districtName = $nodes[$dCod]['name'];

    // Exclude Transnistria din lista de raioane
    if (isTransnistriaLike($districtName)) continue;

    $districtNames[] = $districtName;

    // BFS: colectăm toate descendențele districtului
    $queue = [$dCod];
    $seen = [$dCod => true];
    $locals = [];

    while ($queue) {
        $cur = array_shift($queue);
        foreach ($children[$cur] ?? [] as $ch) {
            if (isset($seen[$ch])) continue;
            $seen[$ch] = true;
            $queue[] = $ch;

            // candidați localități: orice descendent cu nume
            $nm = $nodes[$ch]['name'] ?? '';
            if ($nm === '') continue;

            // scoatem Transnistria-like din localități
            if (isTransnistriaLike($nm)) continue;

            $locals[] = $nm;
        }
    }

    // Curățăm duplicate + sortare
    $locals = array_values(array_unique($locals));
    sort($locals, SORT_LOCALE_STRING);

    $localitiesMap[$districtName] = $locals;
}

// sortare raioane
$districtNames = array_values(array_unique($districtNames));
sort($districtNames, SORT_LOCALE_STRING);

// Salvăm JSON
$out = [
    'districts' => $districtNames,
    'localities' => $localitiesMap,
];

$outPath = $storageDir . '/locations.json';
file_put_contents($outPath, json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo "OK: locations.json generat la: {$outPath}\n";
echo "Districts: " . count($districtNames) . "\n";
echo "Exemplu district: " . ($districtNames[0] ?? '-') . "\n";
