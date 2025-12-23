<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

$search = "Route::get('logs', [LogController::class, 'index']);";
$replace = "Route::get('logs', [LogController::class, 'index']);\n    Route::get('logs/details/{id}', [LogController::class, 'getLogDetails']);";

if (strpos($content, "Route::get('logs/details/{id}'") === false) {
    if (strpos($content, $search) !== false) {
        $content = str_replace($search, $replace, $content);
        file_put_contents($file, $content);
        echo "Details route added successfully.";
    } else {
        echo "Could not find index route to append to.";
    }
} else {
    echo "Details route already exists.";
}
