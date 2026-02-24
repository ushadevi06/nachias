<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Charge;
use App\Models\City;
use App\Models\Place;
use App\Models\RawMaterial;
use App\Models\User;
use App\Models\TaskLog;

class AjaxController extends Controller
{
    public function fetchCities($state_id)
    {
        $cities = City::active()->where('state_id', $state_id)->select('id', 'city_name')->get();
        return response()->json($cities);
    }

    public function fetchPlaces($city_id)
    {
        $places = Place::active()->where('city_id', $city_id)->get(['id', 'place_name']);
        return response()->json($places);
    }

    public function getRawMaterialsByCategory($categoryId)
    {
        $rawMaterials = RawMaterial::where('store_category_id', $categoryId)->where('status', 'Active')->get(['id', 'name', 'code', 'uom_id']);
        return response()->json($rawMaterials);
    }

    public function getCharges()
    {
        $charges = Charge::where('status', 'Active')->orderBy('charge_name')->get(['id', 'charge_name']);
        return response()->json($charges);
    }
    
    public function getMaterialsByCategory($categoryId)
    {
        if (!$categoryId) {
            return response()->json(['materials' => []], 400);
        }
        $materials = RawMaterial::where('store_category_id', $categoryId)->whereNull('deleted_at')->select('id', 'name', 'code', 'uom_id')->get();
        return response()->json(['materials' => $materials]);
    }
    public function getEmployeesByPlant($plantId = null, $stageId = null)
    {
        $query = User::where('status', 'Active')->where('id', '!=', 1);
        if ($plantId && $plantId != 'null' && $plantId != 'undefined' && $plantId != 'all') {
            $query->where('service_provider_id', $plantId);
        }

        if ($stageId && $stageId != 'null' && $stageId != 'undefined') {
            $query->where('operation_stage_id', $stageId);
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
        $providers = \App\Models\ServiceProvider::active()
            ->where('operation_stage_id', $stageId)
            ->get(['id', 'name']);
        
        $results = $providers->map(function($p) {
            return ['id' => $p->id, 'text' => $p->name];
        });

        return response()->json(['success' => true, 'providers' => $providers, 'results' => $results]);
    }
    public function getServicesByStage($stageId)
    {
        $schedule = \App\Models\ProcessSchedule::with(['operationStage'])->find($stageId);
        
        if (!$schedule) {
            $schedule = \App\Models\ProcessSchedule::with(['operationStage'])->where('operation_stage_id', $stageId)->first();
        }
        
        if (!$schedule) {
            return response()->json(['success' => false, 'results' => [], 'message' => 'Schedule not found']);
        }
        $services = \App\Models\ProductionService::where('operation_stage_id', $schedule->operation_stage_id)->where('status', 'Active')->get()->map(function($s) use ($schedule) {
            return [
                'id' => $s->id,
                'text' => ($s->service_name ?? '') . ' - ' . ($s->service_code ?? ''),
                'qty' => $schedule->planned_qty ?? 0
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
}
