<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalStatus;

class ApprovalStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $approvalStatuses = ApprovalStatus::all();

        return view('admin.approval_status.index', compact('approvalStatuses'));
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
            'status' => 'required|string|max:255|unique:approval_statuses,status',
        ]);

        ApprovalStatus::create([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Approval status created successfully.');
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
    public function update(Request $request, ApprovalStatus $approvalStatus)
    {
        $request->validate([
            'status' => 'required|string|max:255|unique:approval_statuses,status,' . $approvalStatus->id,
        ]);

        $approvalStatus->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Approval status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApprovalStatus $approvalStatus)
    {
        $approvalStatus->delete();

        return redirect()->back()->with('success', 'Approval status deleted successfully.');
    }
}
