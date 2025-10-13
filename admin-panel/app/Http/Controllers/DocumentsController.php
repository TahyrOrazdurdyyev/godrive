<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\DocumentType;
use Illuminate\Support\Str;

class DocumentsController extends Controller
{

    public function __construct()
    {
       $this->middleware('auth');
    }

    public function index()
    {
        $documents = DocumentType::orderBy('created_at', 'desc')->get();
        return view("documents.index", compact('documents'));
    }

    public function deletedIndex()
    {
        return view("documents.deleted-document-index");
    }

    public function save($id)
    {
        $document = null;
        if ($id != '0') {
            $document = DocumentType::find($id);
        }
        return view("documents.save", compact('id', 'document'));
    }

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $id = $request->input('id');
        
        if ($id && $id != '0') {
            // Update existing
            $document = DocumentType::find($id);
            if (!$document) {
                return redirect()->back()->with('error', 'Document not found');
            }
            $document->update([
                'title' => $validated['title'],
                'is_enabled' => $request->has('is_enabled') ? 1 : 0
            ]);
        } else {
            // Create new
            DocumentType::create([
                'id' => Str::uuid(),
                'title' => $validated['title'],
                'is_enabled' => $request->has('is_enabled') ? 1 : 0
            ]);
        }

        return redirect()->route('documents')->with('success', 'Document saved successfully');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
    }
}
    public function toggleStatus(Request $request, $id)
    {
        try {
            $document = DocumentType::find($id);
            
            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            $document->is_enabled = $request->input('is_enabled') ? 1 : 0;
            $document->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $document = DocumentType::find($id);
            
            if (!$document) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
