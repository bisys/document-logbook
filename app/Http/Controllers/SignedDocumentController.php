<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SignedDocument;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SignedDocumentController extends Controller
{
    public function index()
    {
        $files = SignedDocument::latest()->get();
        return view('user.signed_documents.index', compact('files'));
    }

    public function create()
    {
        $files = SignedDocument::latest()->get();
        return view('accounting_staff.signed_documents.create', compact('files'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:cash_advance_draw,international_trip',
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf|max:500',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $timestamp = Carbon::now()->format('Ymd_His');
                $docType = $request->input('document_type');
                $filename = $timestamp . '_' . ($index + 1) . '_' . $docType . '_signed_acct.pdf';
                
                $path = $file->storeAs('signed_documents', $filename, 'public');

                SignedDocument::create([
                    'file_name' => $filename,
                    'file_path' => $path,
                ]);
            }

            return redirect()->back()->with('success', 'Files successfully uploaded.');
        }

        return redirect()->back()->with('error', 'No files were uploaded.');
    }
}
