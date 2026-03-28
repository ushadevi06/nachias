<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function error_message($messages)
    {
        return response()->json([
            'status' => 0,
            'errors' => collect($messages)->mapWithKeys(function ($messages, $field) {
                return [$field => $messages[0]];
            })
        ], 422);
    }
}
