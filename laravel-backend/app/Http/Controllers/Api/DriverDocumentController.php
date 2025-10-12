<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DriverDocumentController extends Controller
{
    /**
     * Upload driver document
     * POST /api/driver/documents/upload
     */
    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'driver_id' => 'required|integer',
                'document_type' => 'required|string',
                'document' => 'required|string', // base64 encoded
                'document_name' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Decode base64 image
            $image = $request->document;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace('data:image/jpg;base64,', '', $image);
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(40) . '.jpg';

            // Save to storage/app/public/driver_documents
            Storage::disk('public')->put('driver_documents/' . $imageName, base64_decode($image));

            // Full URL
            $url = url('storage/driver_documents/' . $imageName);

            // Save to database
            $documentId = DB::connection('mysql_main')
                ->table('driver_documents')
                ->insertGetId([
                    'driver_id' => $request->driver_id,
                    'document_type' => $request->document_type,
                    'document_url' => $url,
                    'document_name' => $request->document_name ?? $imageName,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'document_id' => $documentId,
                'url' => $url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver documents
     * GET /api/driver/documents?driver_id={id}
     */
    public function getDocuments(Request $request)
    {
        try {
            $driverId = $request->input('driver_id');

            if (!$driverId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver ID is required'
                ], 400);
            }

            $documents = DB::connection('mysql_main')
                ->table('driver_documents')
                ->where('driver_id', $driverId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'documents' => $documents
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete driver document
     * DELETE /api/driver/documents/{id}
     */
    public function delete($id)
    {
        try {
            $document = DB::connection('mysql_main')
                ->table('driver_documents')
                ->where('id', $id)
                ->first();

            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            // Delete file from storage
            $filename = basename($document->document_url);
            Storage::disk('public')->delete('driver_documents/' . $filename);

            // Delete from database
            DB::connection('mysql_main')
                ->table('driver_documents')
                ->where('id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
