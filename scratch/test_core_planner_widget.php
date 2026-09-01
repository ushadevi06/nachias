<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

auth()->loginUsingId(1);
$c = new App\Http\Controllers\HomeController();

// 1. Test Dashboard Index Render
$res = $c->index();
$html = $res->render();
echo "Dashboard View Render OK! HTML size: " . strlen($html) . " bytes\n";

// 2. Test Core Planner AJAX Page 1
$req1 = Illuminate\Http\Request::create('/dashboard/core-material-planner', 'GET', ['page' => 1, 'per_page' => 10]);
$res1 = $c->getCoreMaterialPlannerAjax($req1)->getData(true);
echo "Core Planner Page 1:\n";
echo "- Total records: " . $res1['total_records'] . " | Showing: " . $res1['from'] . " to " . $res1['to'] . " (last page: " . $res1['last_page'] . ")\n";
echo "- First item: Art=" . $res1['data'][0]['art_no'] . " | Item=" . $res1['data'][0]['item_name'] . " | Stock=" . $res1['data'][0]['stock'] . " | WIP=" . $res1['data'][0]['wip'] . " | FG=" . $res1['data'][0]['fg'] . "\n";
echo "- Totals: Stock=" . $res1['totals']['total_stock'] . " | WIP=" . $res1['totals']['total_wip'] . " | FG=" . $res1['totals']['total_fg'] . "\n";

// 3. Test Core Planner AJAX Page 2
$req2 = Illuminate\Http\Request::create('/dashboard/core-material-planner', 'GET', ['page' => 2, 'per_page' => 10]);
$res2 = $c->getCoreMaterialPlannerAjax($req2)->getData(true);
echo "\nCore Planner Page 2:\n";
echo "- Total records: " . $res2['total_records'] . " | Showing: " . $res2['from'] . " to " . $res2['to'] . "\n";
echo "- First item: Art=" . $res2['data'][0]['art_no'] . " | Stock=" . $res2['data'][0]['stock'] . "\n";

// 4. Test Core Planner Search
$reqSearch = Illuminate\Http\Request::create('/dashboard/core-material-planner', 'GET', ['page' => 1, 'per_page' => 10, 'search' => 'NAWABI']);
$resSearch = $c->getCoreMaterialPlannerAjax($reqSearch)->getData(true);
echo "\nCore Planner Search 'NAWABI':\n";
echo "- Results count: " . count($resSearch['data']) . " | Total: " . $resSearch['total_records'] . "\n";
echo "- Art: " . $resSearch['data'][0]['art_no'] . " | Stock=" . $resSearch['data'][0]['stock'] . " | WIP=" . $resSearch['data'][0]['wip'] . " | FG=" . $resSearch['data'][0]['fg'] . "\n";
