<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Charge;
use App\Models\City;
use App\Models\Place;
use App\Models\RawMaterial;

class AjaxController extends Controller
{
    public function fetchCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->select('id', 'city_name')->get();
        return response()->json($cities);
    }

    public function fetchPlaces($city_id)
    {
        $places = Place::where('city_id', $city_id)->get(['id', 'place_name']);
        return response()->json($places);
    }

    public function getRawMaterialsByCategory($categoryId)
    {
        $rawMaterials = RawMaterial::where('store_category_id', $categoryId)
            ->where('status', 'Active')
            ->get(['id', 'name', 'code']);

        return response()->json($rawMaterials);
    }

    public function getCharges()
    {
        $charges = Charge::where('status', 'Active')
            ->orderBy('charge_name')
            ->get(['id', 'charge_name']);

        return response()->json($charges);
    }
}
