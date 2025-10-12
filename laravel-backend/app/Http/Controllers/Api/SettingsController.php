<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Get application settings
     * GET /api/settings
     */
    public function getSettings()
    {
        try {
            $settingsRows = DB::connection('mysql_main')
                ->table('settings')
                ->get();

            // Convert to key-value object
            $settings = new \stdClass();
            foreach ($settingsRows as $row) {
                $settings->{$row->setting_key} = json_decode($row->setting_value);
            }

            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
