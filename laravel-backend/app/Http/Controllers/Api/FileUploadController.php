<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FileUploadController extends Controller
{
    /**
     * Upload driver avatar
     * POST /api/driver/upload-avatar
     */
    public function uploadDriverAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $uid = $request->input('uid');
            $file = $request->file('avatar');
            
            $filename = 'avatar_' . time() . '.' . $file->getClientOriginalExtension();
            
            $path = $file->move(
                storage_path('uploads/drivers/' . $uid),
                $filename
            );
            
            $url = url('/uploads/drivers/' . $uid . '/' . $filename);
            
            DB::connection('mysql_main')->table('drivers')
                ->where('uid', $uid)
                ->update(['profile_pic' => $url]);
            
            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'url' => $url
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload driver document
     * POST /api/driver/upload-document
     */
    public function uploadDriverDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|integer',
            'document_type' => 'required|string|in:license,car_front,car_back,car_side,vehicle_registration',
            'document' => 'required|image|mimes:jpeg,png,jpg,pdf|max:10240'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $driverId = $request->input('driver_id');
            $documentType = $request->input('document_type');
            $file = $request->file('document');
            
            $driver = DB::connection('mysql_main')->table('drivers')
                ->where('id', $driverId)
                ->first();
                
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found'
                ], 404);
            }
            
            $filename = $documentType . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            $path = $file->move(
                storage_path('uploads/drivers/' . $driver->uid . '/documents'),
                $filename
            );
            
            $url = url('/uploads/drivers/' . $driver->uid . '/documents/' . $filename);
            
            $documentId = DB::connection('mysql_main')->table('driver_documents')->insertGetId([
                'driver_id' => $driverId,
                'document_type' => $documentType,
                'document_name' => $file->getClientOriginalName(),
                'document_url' => $url,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Document uploaded successfully',
                'document' => [
                    'id' => $documentId,
                    'type' => $documentType,
                    'url' => $url,
                    'status' => 'pending'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver documents
     * GET /api/driver/documents?driver_id={id}
     */
    public function getDriverDocuments(Request $request)
    {
        $driverId = $request->input('driver_id');
        
        if (!$driverId) {
            return response()->json([
                'success' => false,
                'message' => 'driver_id is required'
            ], 400);
        }
        
        try {
            $documents = DB::connection('mysql_main')->table('driver_documents')
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
                'message' => 'Error fetching documents: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete driver document
     * DELETE /api/driver/document/{id}
     */
    public function deleteDriverDocument($id)
    {
        try {
            $document = DB::connection('mysql_main')->table('driver_documents')
                ->where('id', $id)
                ->first();
            
            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }
            
            $urlPath = parse_url($document->document_url, PHP_URL_PATH);
            $filePath = public_path($urlPath);
            
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            DB::connection('mysql_main')->table('driver_documents')
                ->where('id', $id)
                ->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload customer avatar
     * POST /api/customer/upload-avatar
     */
    public function uploadCustomerAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firebase_uid' => 'required|string',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $firebaseUid = $request->input('firebase_uid');
            $file = $request->file('avatar');
            
            $filename = 'avatar_' . time() . '.' . $file->getClientOriginalExtension();
            
            $path = $file->move(
                storage_path('uploads/customers/' . $firebaseUid),
                $filename
            );
            
            $url = url('/uploads/customers/' . $firebaseUid . '/' . $filename);
            
            DB::connection('mysql_main')->table('users')
                ->where('firebase_uid', $firebaseUid)
                ->update(['profile_pic' => $url]);
            
            return response()->json([
                'success' => true,
                'message' => 'Avatar uploaded successfully',
                'url' => $url
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
