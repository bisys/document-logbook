<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;

class ApprovalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $approvals = Approval::all();

        return view('admin.approval.index', compact('approvals'));
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
            'approval' => 'required|string|max:255|unique:approvals,approval',
        ]);

        Approval::create([
            'approval' => $request->approval,
        ]);

        return redirect()->back()->with('success', 'Approval created successfully.');
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
    public function update(Request $request, Approval $approval)
    {
        $request->validate([
            'approval' => 'required|string|max:255|unique:approvals,approval,' . $approval->id,
        ]);

        $approval->update([
            'approval' => $request->approval,
        ]);

        return redirect()->back()->with('success', 'Approval updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Approval $approval)
    {
        $approval->delete();

        return redirect()->back()->with('success', 'Approval deleted successfully.');
    }
}
