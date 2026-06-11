<?php
use App\Models\JobCardFabricDetail;
use App\Models\StockEntryItem;
use App\Models\JobCardIssueItem;

$details = JobCardFabricDetail::whereNotNull('stock_entry_id')->get();
foreach ($details as $detail) {
    // Get the current stock for this specific stock entry and art_no
    $items = StockEntryItem::where('stock_entry_id', $detail->stock_entry_id)
        ->where(function ($q) use ($detail) {
            $q->where('art_no', $detail->art_no)
              ->orWhereHas('rawMaterial', function ($q2) use ($detail) {
                  $q2->where('code', $detail->art_no)->orWhere('name', $detail->art_no);
              });
        })->get();

    if ($items->isEmpty()) continue;

    $netQty = $items->sum(function ($item) {
        return ($item->qty_in ?? 0) - ($item->qty_out ?? 0);
    });

    // Also need to add back what was already issued for THIS job card and THIS stock entry
    $alreadyIssued = JobCardIssueItem::where('job_card_entry_id', $detail->job_card_entry_id)
        ->whereHas('fabricDetail', function($q) use ($detail) {
            $q->where('stock_entry_id', $detail->stock_entry_id)
              ->where('art_no', $detail->art_no);
        })
        ->sum('qty_issue');

    $correctTotal = $netQty + $alreadyIssued;

    // If the saved stock_total_qty is substantially different from the correct total, it's a merged legacy value
    if (abs($detail->stock_total_qty - $correctTotal) > 0.01) {
        echo "Updating detail ID {$detail->id} (Art: {$detail->art_no}, SE: {$detail->stock_entry_id}) from {$detail->stock_total_qty} to {$correctTotal}\n";
        $detail->stock_total_qty = $correctTotal;
        $detail->save();
    }
}
