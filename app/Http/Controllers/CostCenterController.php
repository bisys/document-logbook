<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CostCenter;

class CostCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $costCenters = CostCenter::all();
        return view('admin.cost_center.index', compact('costCenters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:255|unique:cost_centers,number',
            'name' => 'required|string|max:255',
        ]);

        CostCenter::create([
            'number' => $request->number,
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Cost Center created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CostCenter $costCenter)
    {
        $request->validate([
            'number' => 'required|string|max:255|unique:cost_centers,number,' . $costCenter->id,
            'name' => 'required|string|max:255',
        ]);

        $costCenter->update([
            'number' => $request->number,
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Cost Center updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CostCenter $costCenter)
    {
        $costCenter->delete();

        return redirect()->back()->with('success', 'Cost Center deleted successfully.');
    }
}
