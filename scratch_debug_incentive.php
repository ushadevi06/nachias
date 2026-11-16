<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$from = "2026-10-01";
$to = "2026-12-31";

$rows = DB::table('sales_invoices as si')
    ->leftJoin('customers as c', 'si.customer_id', '=', 'c.id')
    ->leftJoin('zones as z', 'c.zone_id', '=', 'z.id')
    ->leftJoin('sales_agents as sa', 'si.agent_id', '=', 'sa.id')
    ->leftJoin('sales_invoice_items as sii', function($join) {
        $join->on('si.id', '=', 'sii.sales_invoice_id')
             ->whereNull('sii.deleted_at');
    })
    ->whereNull('si.deleted_at')
    ->where(function($q) {
        $q->whereNull('si.invoice_status')
          ->orWhere('si.invoice_status', '!=', 'Cancelled');
    })
    ->whereBetween('si.inv_date', [$from, $to])
    ->groupBy('si.agent_id', 'sa.name', 'sa.code', 'c.zone_id', 'z.zone_name')
    ->select(
        'si.agent_id',
        DB::raw('COALESCE(sa.name, "Unknown") as agent_name'),
        DB::raw('COALESCE(sa.code, "-") as agent_code'),
        DB::raw('COALESCE(z.zone_name, "Unknown Zone") as zone_name'),
        DB::raw('COUNT(DISTINCT si.id) as inv_count'),
        DB::raw('SUM(sii.quantity) as total_qty'),
        DB::raw("SUM(CASE WHEN COALESCE(si.discount_percent, 0) > 10 THEN sii.quantity ELSE 0 END) as discount_qty"),
        DB::raw("SUM(CASE WHEN COALESCE(si.discount_percent, 0) <= 10 THEN sii.quantity ELSE 0 END) as regular_qty")
    )
    ->orderBy('zone_name')
    ->get();

echo "Sales Invoices grouped by Agent and Customer Zone (Q3 2026):\n";
$grandTotal = 0;
foreach ($rows as $r) {
    echo "Zone: {$r->zone_name} | Agent: {$r->agent_name} ({$r->agent_code}) [AgentID: {$r->agent_id}] | Inv Count: {$r->inv_count} | Regular: {$r->regular_qty} | Disc: {$r->discount_qty} | Total: {$r->total_qty}\n";
    $grandTotal += $r->total_qty;
}
echo "\nGrand Total Invoice Qty: $grandTotal\n";
