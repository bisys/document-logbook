import os
import re

controllers_paths = [
    "AccountingStaff", "AccountingManager", "AccountingGM"
]
models = ["SupplierPayment", "PettyCash", "InternationalTrip", "CashAdvanceDraw", "CashAdvanceRealization"]

base_path = r"d:\Project Apps\document_logbook\app\Http\Controllers"

t_names = {
    "SupplierPayment": "supplier_payments",
    "PettyCash": "petty_cashes",
    "InternationalTrip": "international_trips",
    "CashAdvanceDraw": "cash_advance_draws",
    "CashAdvanceRealization": "cash_advance_realizations"
}

for role in controllers_paths:
    for model in models:
        file_path = os.path.join(base_path, role, f"{model}Controller.php")
        if not os.path.exists(file_path): continue
        
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        if "function bulkApprove(" in content:
            continue

        match = re.search(r'public function approve\(Request \$request,\s*' + model + r'\s+\$([a-zA-Z0-9_]+)\)\s*\{(.*?)return redirect', content, re.DOTALL)
        if match:
            var_name = match.group(1)
            method_body = match.group(2)

            try_block_match = re.search(r'try\s*\{(.*)', method_body, re.DOTALL)
            if try_block_match:
                try_content = try_block_match.group(1).rstrip()
                if try_content.endswith('} catch'):
                    try_content = try_content[:-7].rstrip()

                table_name = t_names[model]

                lines = try_content.strip().split('\n')
                indented_try_content = '\n'.join(['                ' + line.strip() if line.strip() else '' for line in lines])

                bulk_approve_method = f"""
    public function bulkApprove(Request $request)
    {{
        $validated = $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:{table_name},id',
            'remark' => 'nullable|string|max:1000'
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($validated['document_ids'] as $docId) {{
            try {{
                ${var_name} = {model}::findOrFail($docId);
                {indented_try_content}
                $successCount++;
            }} catch (\Exception $e) {{
                $errors[] = "${var_name} ID {{$docId}}: " . $e->getMessage();
            }}
        }}

        if (count($errors) > 0) {{
            $errorMessage = "Approved {{$successCount}} documents. Errors on " . count($errors) . " documents: " . implode(', ', $errors);
            return redirect()->back()->with('error', $errorMessage);
        }}

        return redirect()->back()->with('success', "Successfully approved {{$successCount}} documents.");
    }}
"""
                # find the last closing brace
                last_brace_idx = content.rfind('}')
                if last_brace_idx != -1:
                    new_content = content[:last_brace_idx] + bulk_approve_method + content[last_brace_idx:]
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(new_content)
                    print(f"Added bulkApprove to {role}/{model}")
                else:
                    print(f"Could not find closing brace in {role}/{model}")
        else:
            print(f"Could not parse approve method in {role}/{model}")
