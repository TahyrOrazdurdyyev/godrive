<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    /**
     * Get all active languages
     * GET /api/languages
     */
    public function index()
    {
        try {
            $languages = DB::connection('mysql_main')->table('languages')
                ->where('enable', 1)
                ->orderBy('is_default', 'desc')
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'languages' => $languages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get language by code
     * GET /api/languages/{code}
     */
    public function show($code)
    {
        try {
            $language = DB::connection('mysql_main')->table('languages')
                ->where('code', $code)
                ->where('enable', 1)
                ->first();

            if (!$language) {
                return response()->json([
                    'success' => false,
                    'message' => 'Language not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'language' => $language
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
