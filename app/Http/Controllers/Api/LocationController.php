<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function index()
    {
        $path = storage_path('app/md/locations.json');

        if (!file_exists($path)) {
            return response()->json([
                'ok' => false,
                'message' => 'Locations file not found',
            ], 404);
        }

        $data = json_decode(file_get_contents($path), true);

        if (!is_array($data)) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid JSON',
            ], 500);
        }

        // 🔥 IMPORTANT — decode unicode
        $districts = array_map(function ($d) {
            return json_decode('"' . $d . '"');
        }, $data['districts'] ?? []);

        $localities = [];

        foreach ($data['localities'] ?? [] as $district => $locs) {

            $decodedDistrict = json_decode('"' . $district . '"');

            $localities[$decodedDistrict] = array_map(function ($l) {
                return json_decode('"' . $l . '"');
            }, $locs);
        }

        return response()->json([
            'ok' => true,
            'districts' => $districts,
            'localities' => $localities,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}