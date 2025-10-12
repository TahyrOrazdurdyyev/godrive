<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FaqController extends Controller
{
    /**
     * Get all active FAQs
     * GET /api/faqs
     */
    public function getAll()
    {
        try {
            $faqs = DB::connection('mysql_main')
                ->table('faqs')
                ->where('enable', true)
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'faqs' => $faqs
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get FAQs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FAQ by ID
     * GET /api/faqs/{id}
     */
    public function show($id)
    {
        try {
            $faq = DB::connection('mysql_main')
                ->table('faqs')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();

            if (!$faq) {
                return response()->json([
                    'success' => false,
                    'message' => 'FAQ not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'faq' => $faq
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get FAQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
