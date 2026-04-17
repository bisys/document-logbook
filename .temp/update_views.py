import os, glob, re

view_dirs = glob.glob('d:/Project Apps/document_logbook/resources/views/accounting_*/')
document_types = ['supplier_payment', 'petty_cash', 'international_trip', 'cash_advance_draw', 'cash_advance_realization']
models_camel = {
    'supplier_payment': 'supplierPayment',
    'petty_cash': 'pettyCash',
    'international_trip': 'internationalTrip',
    'cash_advance_draw': 'cashAdvanceDraw',
    'cash_advance_realization': 'cashAdvanceRealization'
}

for role_dir in view_dirs:
    role = os.path.basename(role_dir[:-1])
    role_slug = role.replace('accounting_', 'accounting-')
    role_name = role.split('_')[1] # staff, manager, gm
    approval_slug = f"waiting-approval-{role_name}"

    for doc_type in document_types:
        f = os.path.join(role_dir, doc_type, 'index.blade.php')
        if not os.path.exists(f): continue

        content = open(f, 'r', encoding='utf-8').read()

        if "id=\"btn-bulk-approve\"" in content:
            continue
            
        # 1. Add button to card-header
        content = re.sub(
            r'(<div class="card-header">.*?<h4>.*?</h4>)',
            r'\1\n                        <div class="card-header-action">\n                            <form id="bulk-approve-form" action="{{ route(\'' + role_slug + r'.' + doc_type.replace('_', '-') + r'.bulk-approve\') }}" method="POST">\n                                @csrf\n                                <input type="hidden" name="remark" id="bulk-remark">\n                                <div id="bulk-inputs"></div>\n                                <button type="button" class="btn btn-success" id="btn-bulk-approve" style="display: none;">Approve Selected (<span id="selected-count">0</span>)</button>\n                            </form>\n                        </div>',
            content,
            count=1,
            flags=re.DOTALL|re.IGNORECASE
        )

        # 2. Add checkbox to thead
        content = re.sub(
            r'(<thead>\s*<tr>\s*)<th>',
            r'\1<th class="text-center">\n                                            <div class="custom-checkbox custom-control">\n                                                <input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">\n                                                <label for="checkbox-all" class="custom-control-label">&nbsp;</label>\n                                            </div>\n                                        </th>\n                                        <th>',
            content, 1,
            flags=re.IGNORECASE
        )

        var = models_camel[doc_type]
        
        # 3. Add checkbox to tbody
        content = re.sub(
            r'(@foreach\([^)]+ as \$' + var + r'\)\s*<tr>\s*)<td>',
            lambda match: match.group(1) + r"""<td class="text-center">
                                            @if(optional($""" + var + r"""->status)->slug === '""" + approval_slug + r"""')
                                            <div class="custom-checkbox custom-control">
                                                <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input doc-checkbox" id="checkbox-{{ $""" + var + r"""->id }}" value="{{ $""" + var + r"""->id }}">
                                                <label for="checkbox-{{ $""" + var + r"""->id }}" class="custom-control-label">&nbsp;</label>
                                            </div>
                                            @endif
                                        </td>
                                        <td>""",
            content,
            count=1
        )

        # 4. Add JavaScript
        js_script = r"""
<!-- Script for Bulk Approval -->
<script>
    $(document).ready(function() {
        function updateBulkApproveButton() {
            var selectedCount = $('.doc-checkbox:checked').length;
            $('#selected-count').text(selectedCount);
            if (selectedCount > 0) {
                $('#btn-bulk-approve').show();
            } else {
                $('#btn-bulk-approve').hide();
            }
        }

        $('#table-1').on('change', 'input[type="checkbox"]', function() {
            setTimeout(updateBulkApproveButton, 50);
        });

        $('#btn-bulk-approve').click(function() {
            var selectedIds = [];
            $('.doc-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            swal({
                title: 'Approve Selected Documents?',
                text: 'You are about to approve ' + selectedIds.length + ' document(s). You can add an optional remark below:',
                content: {
                    element: "input",
                    attributes: {
                        placeholder: "Optional Remark",
                        type: "text",
                    },
                },
                icon: 'warning',
                buttons: true,
            })
            .then((remark) => {
                if (remark !== null) {
                    $('#bulk-remark').val(remark);
                    
                    $('#bulk-inputs').empty();
                    selectedIds.forEach(function(id) {
                        $('#bulk-inputs').append('<input type="hidden" name="document_ids[]" value="' + id + '">');
                    });
                    
                    $('#bulk-approve-form').submit();
                }
            });
        });
    });
</script>
@endpush
"""
        content = content.replace("@endpush", js_script)
        
        open(f, 'w', encoding='utf-8').write(content)
