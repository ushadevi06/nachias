<?php
use App\Models\JobCardFabricDetail;
use App\Models\StockEntryItem;

$details = JobCardFabricDetail::whereNull('stock_entry_id')->get();
foreach ($details as $detail) {
    $jc = $detail->jobCardEntry;
    if (!$jc || !$jc->stock_entry_ids) continue;
    $seIdsStr = json_decode($jc->stock_entry_ids, true);
    if (!is_array($seIdsStr)) continue;
    
    $matchedSeId = null;
    foreach ($seIdsStr as $cid) {
        if (strpos($cid, '::') !== false) {
            list($seId, $target) = explode('::', $cid, 2);
            if (strpos($target, '|') !== false) {
                list($type, $val) = explode('|', $target, 2);
                if (($type === 'art' && $val == $detail->art_no) || ($type === 'rm')) {
                    $matchedSeId = $seId;
                    break; 
                }
            }
        }
    }
    
    if (!$matchedSeId) {
        // Fallback: search for first stock entry item that matches art_no inside the seIds
        $plainSeIds = array_map(function($cid) {
            return strpos($cid, '::') !== false ? explode('::', $cid)[0] : $cid;
        }, $seIdsStr);
        $stockItem = StockEntryItem::whereIn('stock_entry_id', $plainSeIds)->where('art_no', $detail->art_no)->first();
        if ($stockItem) {
            $matchedSeId = $stockItem->stock_entry_id;
        }
    }

    if ($matchedSeId) {
        $detail->stock_entry_id = $matchedSeId;
        $detail->save();
        echo "Updated detail {$detail->id} to SE {$matchedSeId}\n";
    }
}
