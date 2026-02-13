<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalRole;
use Illuminate\Support\Facades\App;

class ApprovalRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $approvalRoles = ApprovalRole::all();

        return view('admin.approval_role.index', compact('approvalRoles'));
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
            'name' => 'required|string|max:255|unique:approval_roles,name',
            'sequence' => 'required|integer|unique:approval_roles,sequence',
        ]);

        ApprovalRole::create([
            'name' => $request->name,
            'sequence' => $request->sequence,
        ]);

        return redirect()->back()->with('success', 'Approval role created successfully.');
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
    public function update(Request $request, ApprovalRole $approvalRole)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:approval_roles,name,' . $approvalRole->id,
            'sequence' => 'required|integer|unique:approval_roles,sequence,' . $approvalRole->id,
        ]);

        $approvalRole->update([
            'name' => $request->name,
            'sequence' => $request->sequence,
        ]);

        return redirect()->back()->with('success', 'Approval role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ApprovalRole $approvalRole)
    {
        $approvalRole->delete();

        return redirect()->back()->with('success', 'Approval role deleted successfully.');
    }
}
