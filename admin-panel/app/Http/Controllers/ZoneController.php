<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $zones = \DB::connection('mysql_main')->table('zones')->get();
        return view('zone.index', compact('zones'));
    }

    public function edit($id)
    {
        return view('zone.edit')->with('id',$id);
    }

    public function create()
    {
        return view('zone.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'coordinates' => 'required|string',
                'enable' => 'nullable|boolean',
            ]);

            $coordinates = $validated['coordinates'];
            
            \DB::connection('mysql_main')->table('zones')->insert([
                'name' => $validated['name'],
                'coordinates' => $coordinates,
                'enable' => $request->input('enable', false) ? 1 : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Zone created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
