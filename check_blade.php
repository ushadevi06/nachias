<?php
// Check for @if inside JS template literals (backtick strings) that Blade would also process
$file = __DIR__ . '/resources/views/job_card_entry/add.blade.php';
$content = file_get_contents($file);

// Find all @if locations with context
preg_match_all('/(@if\s*\([^)]*\)|@elseif\s*\([^)]*\)|@else\b|@endif\b)/', $content, $matches, PREG_OFFSET_CAPTURE);

$depth = 0;
$lineNo = function($offset) use ($content) {
    return substr_count(substr($content, 0, $offset), "\n") + 1;
};

foreach ($matches[0] as $m) {
    $token = trim($m[0]);
    $line = $lineNo($m[1]);
    if (preg_match('/^@if\s*\(/', $token)) { $depth++; echo "OPEN  [depth=$depth] L$line: $token\n"; }
    elseif ($token === '@else') { echo "ELSE  [depth=$depth] L$line\n"; }
    elseif (preg_match('/^@elseif/', $token)) { echo "ELIF  [depth=$depth] L$line: $token\n"; }
    elseif ($token === '@endif') { $depth--; echo "CLOSE [depth=$depth] L$line\n"; }
}
echo "\nFinal depth: $depth\n";

// Also check for $hasTasks variable usage — it might not be defined in controller
echo "\nChecking \$hasTasks usage:\n";
$lines = file($file);
foreach ($lines as $i => $line) {
    if (strpos($line, 'hasTasks') !== false) {
        echo "L" . ($i+1) . ": " . trim($line) . "\n";
    }
}
