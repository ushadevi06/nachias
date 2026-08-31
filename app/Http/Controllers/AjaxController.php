<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Charge;
use App\Models\City;
use App\Models\Place;
use App\Models\RawMaterial;
use App\Models\User;
use App\Models\Customer;
use App\Models\Item;
use App\Models\TaskLog;
use App\Models\ProcessSchedule;
use App\Models\ProductionService;
use App\Models\ServiceProvider;
use App\Models\Zone;
use App\Models\SalesAgent;
use App\Models\StockEntryItem;

class AjaxController extends Controller
{
    public function fetchCities($state_id)
    {
        $stateIds = explode(',', $state_id);
        $cities = City::active()->whereIn('state_id', $stateIds)->select('id', 'city_name', 'state_id')->orderBy('id', 'desc')->get();
        return response()->json($cities);
    }

    public function fetchZones($state_id)
    {
        $stateIds = explode(',', $state_id);
        $zones = Zone::active()->where(function($q) use ($stateIds) {
            foreach($stateIds as $sId) {
                if (trim($sId) !== '') {
                    $q->orWhereRaw("FIND_IN_SET(?, state_ids)", [trim($sId)]);
                }
            }
        })->select('id', 'zone_name')->get();
        return response()->json($zones);
    }

    public function fetchAgentsByZone($zone_id)
    {
        $query = SalesAgent::active();
        if ($zone_id && $zone_id != 0) {
            $query->where('zone_id', $zone_id);
        }
        $agents = $query->select('id', 'name','code')->get();
        return response()->json($agents);
    }

    public function fetchPlaces($city_id)
    {
        $places = Place::active()->where('city_id', $city_id)->get(['id', 'place_name']);
        return response()->json($places);
    }

    public function fetchZonesByCity($city_id)
    {
        $zones = Zone::active()->whereRaw("FIND_IN_SET(?, city_ids)", [$city_id])->get(['id', 'zone_name']);
        return response()->json($zones);
    }

    public function getRawMaterialsByCategory($categoryId)
    {
        $rawMaterials = RawMaterial::where('store_category_id', $categoryId)->where('status', 'Active')->orderBy('id', 'desc')->get(['id', 'name', 'code', 'uom_id']);
        return response()->json($rawMaterials);
    }

    public function getCharges()
    {
        $charges = Charge::where('status', 'Active')->orderBy('id', 'desc')->get(['id', 'charge_name']);
        return response()->json($charges);
    }
    
    public function getMaterialsByCategory($categoryId)
    {
        if (!$categoryId) {
            return response()->json(['materials' => []], 400);
        }
        $materials = RawMaterial::where('store_category_id', $categoryId)->whereNull('deleted_at')->select('id', 'name', 'code', 'uom_id')->orderBy('id', 'desc')->get();
        return response()->json(['materials' => $materials]);
    }
    public function getEmployeesByPlant($plantId = null, $stageId = null)
    {
        $query = User::where('status', 'Active')->where('id', '!=', 1);
        if ($plantId && $plantId != 'null' && $plantId != 'undefined' && $plantId != 'all') {
            $query->where('service_provider_id', $plantId);
        }
        if ($stageId && $stageId != 'null' && $stageId != 'undefined') {
            $query->where(function ($q) use ($stageId) {
                $q->whereJsonContains('operation_stage_id', (string) $stageId)
                ->orWhere('operation_stage_id', (string) $stageId);
            });
        }
        
        if (request()->has('department_id')) {
            $query->where('department_id', request()->department_id);
        }

        if (request()->has('role_id')) {
            $query->where('role_id', request()->role_id);
        }

        if (request()->has('q')) {
            $query->where('name', 'like', '%' . request()->q . '%');
        }

        $employees = $query->get(['id', 'name', 'emp_id']);
        // dd($employees);
        
        $formatted = $employees->map(function($e) {
            return [
                'id' => $e->id, 
                'text' => $e->name . ($e->emp_id ? " ({$e->emp_id})" : ""),
                'emp_id' => $e->emp_id
            ];
        });

        return response()->json(['success' => true, 'employees' => $employees, 'results' => $formatted]);
    }
    public function getServiceProvidersByStage($stageId)
    {
        $providers = ServiceProvider::active()->where('operation_stage_id', $stageId)->get(['id', 'name', 'code']);
        
        $results = $providers->map(function($p) {
            $displayName = $p->code ? ($p->name . ' - ' . $p->code) : $p->name;
            return ['id' => $p->id, 'text' => $displayName, 'name' => $displayName];
        });

        return response()->json(['success' => true, 'providers' => $results, 'results' => $results]);
    }
    public function getServicesByStage($stageId)
    {
        $schedule = ProcessSchedule::with(['operationStage', 'jobCard'])->find($stageId);
        
        if (!$schedule) {
            $schedule = ProcessSchedule::with(['operationStage', 'jobCard'])->where('operation_stage_id', $stageId)->first();
        }
        
        if (!$schedule) {
            return response()->json(['success' => false, 'results' => [], 'message' => 'Schedule not found']);
        }
        
        $query = ProductionService::where('operation_stage_id', $schedule->operation_stage_id)->where('status', 'Active');
        if ($schedule->jobCard && $schedule->jobCard->process_group_id) {
            $query->whereHas('processGroups', function($q) use ($schedule) {
                $q->where('process_groups.id', $schedule->jobCard->process_group_id);
            });
        }
        
        $services = $query->get()->map(function($s) use ($schedule) {
            $qty = $schedule->planned_qty ?? 0;
            if ($s->base_quantity_source == 'FS Qty' && $schedule->jobCard) {
                $qty = $schedule->jobCard->total_qty_fs ?? $qty;
            } elseif ($s->base_quantity_source == 'HS Qty' && $schedule->jobCard) {
                $qty = $schedule->jobCard->total_qty_hs ?? $qty;
            } elseif ($schedule->jobCard) {
                $qty = $schedule->jobCard->grand_total_qty ?? $qty;
            }
            return [
                'id' => $s->id,
                'text' => ($s->service_name ?? ''),
                'qty' => $qty
            ];
        })->values()->all();
        return response()->json(['success' => true, 'results' => $services]);
    }
    public function getTaskLogs($taskId)
    {
        $logs = TaskLog::with('user')->where('task_id', $taskId)->latest()->get()->map(function($log) {
            return [
                'id' => $log->id,
                'action' => $log->action,
                'description' => $log->description,
                'user_name' => $log->user->name ?? 'System',
                'created_at' => $log->created_at->format('d M Y, h:i A'),
                'time_ago' => $log->created_at->diffForHumans()
            ];
        });
        
        return response()->json(['success' => true, 'logs' => $logs]);
    }

    public function getItemDetails($id)
    {
        $item = Item::with(['uom'])->find($id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found']);
        }

        $stockDetails = StockEntryItem::with('color:id,color_name')->where('finished_item_code', 'like', $item->code . '%')->whereNull('deleted_at')->select('id', 'size', 'color_id', 'finished_item_code', 'price', DB::raw('(qty_in - qty_out) as balance'))->get();

        $stockData = $stockDetails->map(function($s) {
            $sleeve = null;
            if (str_contains($s->finished_item_code, 'Half') || str_contains($s->finished_item_code, 'H/S')) {
                $sleeve = 'Half';
            } elseif (str_contains($s->finished_item_code, 'Full') || str_contains($s->finished_item_code, 'F/S')) {
                $sleeve = 'Full';
            } else {
                $sleeve = 'Full';
            }
            
            return [
                'stock_entry_item_id' => $s->id,
                'size' => $s->size,
                'sleeve' => $sleeve,
                'color_id' => $s->color_id,
                'color_name' => $s->color ? $s->color->color_name : null,
                'rate' => (float)$s->price,
                'balance' => (float)$s->balance
            ];
        })->filter(function($s) {
            return $s['balance'] > 0;
        })->values();

        $totalStock = $stockData->sum('balance');
        $defaultRate = $item->wholesale_price ?? ($stockData->first()['rate'] ?? 0);

        return response()->json([
            'success'   => true,
            'rate'      => (float)$defaultRate,
            'uom_id'    => $item->uom_id,
            'color_id'  => is_array($item->color_id) ? ($item->color_id[0] ?? null) : $item->color_id,
            'size_id'   => $item->size_ratio_id, 
            'art_no'    => $item->design_art_no,
            'mrp'       => (float)$item->mrp,
            'available_stock' => (float)$totalStock,
            'stock_breakdown' => $stockData
        ]);
    }

    public function getItemsByBrandCategory($brandCategoryId)
    {
        $items = Item::active()
            ->where('brand_category_id', $brandCategoryId)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('stock_entry_items')
                    ->whereColumn('stock_entry_items.finished_item_code', 'like', DB::raw("CONCAT(items.code, '%')"))
                    ->whereNull('deleted_at')
                    ->groupBy('finished_item_code')
                    ->havingRaw('SUM(qty_in) - SUM(qty_out) > 0');
            })
            ->get(['id', 'name', 'code']);
        return response()->json(['success' => true, 'items' => $items]);
    }

    public function getCustomerDetails($id)
    {
        $customer = Customer::with(['tax', 'zone', 'state', 'city'])->find($id);
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }

        $addressParts = array_filter([
            $customer->address_line_1,
            $customer->address_line_2,
            $customer->address_line_3,
            $customer->city->city_name ?? '',
            $customer->state->state_name ?? '',
            $customer->zip_code
        ]);
        $formattedAddress = implode(', ', $addressParts);

        return response()->json([
            'success' => true,
            'customer' => $customer,
            'address' => $formattedAddress,
            'box_discount_amount' => (float)$customer->box_discount_amount,
            'sales_discount' => (float)$customer->sales_discount
        ]);
    }

    public function searchRawMaterials(Request $request)
    {
        $search = $request->q;
        $materials = RawMaterial::active()
            ->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('code', 'like', "%$search%");
            })
            ->limit(20)
            ->get(['id', 'name', 'code']);

        $formatted = $materials->map(function($m) {
            return [
                'id' => $m->id,
                'text' => $m->name . " (" . $m->code . ")"
            ];
        });

        return response()->json(['results' => $formatted]);
    }
}
