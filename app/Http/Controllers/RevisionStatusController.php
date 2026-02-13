<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RevisionStatus;

class RevisionStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $revisionStatuses = RevisionStatus::all();

        return view('admin.revision_status.index', compact('revisionStatuses'));
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
            'status' => 'required|string|max:255|unique:revision_statuses,status',
        ]);

        RevisionStatus::create([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Revision status created successfully.');
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
    public function update(Request $request, RevisionStatus $revisionStatus)
    {
        $request->validate([
            'status' => 'required|string|max:255|unique:revision_statuses,status,' . $revisionStatus->id,
        ]);

        $revisionStatus->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Revision status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RevisionStatus $revisionStatus)
    {
        $revisionStatus->delete();

        return redirect()->back()->with('success', 'Revision status deleted successfully.');
    }
}
