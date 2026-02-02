<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;

class DocumentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $documentTypes = DocumentType::all();

        return view('admin.document_type.index', compact('documentTypes'));
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
            'name' => 'required|string|max:255|unique:document_types,name',
            'full_name' => 'required|string|max:255',
        ]);

        DocumentType::create([
            'name' => $request->name,
            'full_name' => $request->full_name,
        ]);

        return redirect()->back()->with('success', 'Document Type created successfully.');
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
    public function update(Request $request, DocumentType $documentType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name,' . $documentType->id,
            'full_name' => 'required|string|max:255',
        ]);

        $documentType->update([
            'name' => $request->name,
            'full_name' => $request->full_name,
        ]);

        return redirect()->back()->with('success', 'Document Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();

        return redirect()->back()->with('success', 'Document Type deleted successfully.');
    }
}
