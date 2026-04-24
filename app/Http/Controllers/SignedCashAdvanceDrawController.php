<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SignedCashAdvanceDraw;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SignedCashAdvanceDrawController extends Controller
{
    public function index()
    {
        $files = SignedCashAdvanceDraw::latest()->get();
        return view('user.signed_cash_advance_draws.index', compact('files'));
    }

    public function create()
    {
        $files = SignedCashAdvanceDraw::latest()->get();
        return view('accounting_staff.signed_cash_advance_draws.create', compact('files'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf|max:500',
        ]);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $timestamp = Carbon::now()->format('Ymd_His');
                $filename = $timestamp . '_' . ($index + 1) . '_cash_advance_draw_signed_acct.pdf';
                
                $path = $file->storeAs('signed_cash_advance_draws', $filename, 'public');

                SignedCashAdvanceDraw::create([
                    'file_name' => $filename,
                    'file_path' => $path,
                ]);
            }

            return redirect()->back()->with('success', 'Files successfully uploaded.');
        }

        return redirect()->back()->with('error', 'No files were uploaded.');
    }
}
