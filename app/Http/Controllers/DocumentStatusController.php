<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentStatus;

class DocumentStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentStatuses = DocumentStatus::all();

        return view('admin.document_status.index', compact('documentStatuses'));
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
            'status' => 'required|string|max:255|unique:document_statuses,status',
        ]);

        DocumentStatus::create([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Document Status created successfully.');
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
    public function update(Request $request, DocumentStatus $documentStatus)
    {
        $request->validate([
            'status' => 'required|string|max:255|unique:document_statuses,status,' . $documentStatus->id,
        ]);

        $documentStatus->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Document Status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentStatus $documentStatus)
    {
        $documentStatus->delete();

        return redirect()->back()->with('success', 'Document Status deleted successfully.');
    }
}
