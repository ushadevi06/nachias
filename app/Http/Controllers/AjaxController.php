<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Charge;
use App\Models\City;
use App\Models\Place;
use App\Models\RawMaterial;
use App\Models\User;

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
    public function getEmployeesByPlant($plantId = null)
    {
        $query = User::where('status', 'Active')->where('id', '!=', 1);
        if ($plantId && $plantId != 'null' && $plantId != 'undefined' && $plantId != 'all') {
            $query->where('service_provider_id', $plantId);
        }
        
        if (request()->has('department_id')) {
            $query->where('department_id', request()->department_id);
        }

        if (request()->has('q')) {
            $query->where('name', 'like', '%' . request()->q . '%');
        }

        $employees = $query->get(['id', 'name']);
        
        $formatted = $employees->map(function($e) {
            return ['id' => $e->id, 'text' => $e->name];
        });

        return response()->json(['success' => true, 'employees' => $employees, 'results' => $formatted]);
    }
}
