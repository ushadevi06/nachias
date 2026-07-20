<?php
$files = \Illuminate\Support\Facades\File::allFiles(app_path());
foreach ($files as $file) {
    $content = file_get_contents($file->getPathname());
    if (stripos($content, '38') !== false && stripos($content, 'M') !== false && stripos($content, 'size') !== false) {
        echo "Found in: " . $file->getPathname() . "\n";
    }
}
