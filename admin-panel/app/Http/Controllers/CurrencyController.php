<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $currencies = Currency::orderBy('created_at', 'desc')->get();
        return view("currency.index", compact('currencies'));
    }

    public function create()
    {
        return view('currency.create');
    }

    public function edit($id)
    {
        $currency = Currency::find($id);
        return view('currency.edit', compact('id', 'currency'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'symbol' => 'required|string|max:10',
                'code' => 'required|string|max:10'
            ]);

            $data = [
                'name' => $validated['name'],
                'symbol' => $validated['symbol'],
                'code' => strtoupper($validated['code']),
                'symbol_at_right' => $request->has('symbol_at_right') ? 1 : 0,
                'is_active' => $request->has('is_active') ? 1 : 0
            ];

            $id = $request->input('id');
            
            if ($id && $id != '0') {
                // Update existing
                $currency = Currency::find($id);
                if (!$currency) {
                    return redirect()->back()->with('error', 'Currency not found');
                }
                $currency->update($data);
            } else {
                // Create new
                Currency::create($data);
            }

            return redirect()->route('currency')->with('success', 'Currency saved successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Request $request, $id)
    {
        try {
            $currency = Currency::find($id);
            
            if (!$currency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Currency not found'
                ], 404);
            }

            $currency->is_active = $request->input('is_active') ? 1 : 0;
            $currency->save();

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
            $currency = Currency::find($id);
            
            if (!$currency) {
                return response()->json([
                    'success' => false,
                    'message' => 'Currency not found'
                ], 404);
            }

            $currency->delete();

            return response()->json([
                'success' => true,
                'message' => 'Currency deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
