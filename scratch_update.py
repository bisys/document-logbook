import os

base_dir = r'd:\\Project Apps\\document_logbook\\resources\\views'
roles = ['accounting_manager', 'accounting_gm']
docs = ['petty_cash', 'cash_advance_draw', 'international_trip', 'supplier_payment']

for role in roles:
    for doc in docs:
        if role == 'accounting_manager' and doc == 'petty_cash':
            continue # already done
        
        file_path = os.path.join(base_dir, role, doc, 'show.blade.php')
        if not os.path.exists(file_path):
            continue
            
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        camel_doc = ''.join(word.capitalize() for word in doc.split('_'))
        camel_doc = camel_doc[0].lower() + camel_doc[1:]
        
        search_pattern = '''        </div></div></div>
        @endif

        <div class="row">'''
        
        replacement = f'''        </div></div></div>
        @endif

        {{{{-- Payment Receipt Status --}}}}
        @php
        $isFullyApproved = optional(${camel_doc}->status)->slug === 'fully-approved';
        @endphp
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-money-bill-wave mr-2"></i>Payment Receipt</h4>
                </div>
                <div class="card-body">
                    @if(${camel_doc}->is_paid)
                        <div class="alert alert-success mb-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x mr-3"></i>
                                <div>
                                    <strong>Payment Processed</strong><br>
                                    <span class="text-muted" style="color: white !important;">Processed by: <strong>{{{{ optional(${camel_doc}->paidByUser)->name ?? '-' }}}}</strong></span><br>
                                    <span class="text-muted" style="color: white !important;">Date: <strong>{{{{ optional(${camel_doc}->paid_at)->format('d M Y H:i') }}}}</strong></span><br>
                                    <a href="{{{{ asset('storage/'.${camel_doc}->payment_receipt_path) }}}}" target="_blank" class="btn btn-sm btn-light mt-2 text-dark">View Receipt</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-clock mr-2"></i> Payment has not been processed yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">'''
        
        if search_pattern in content:
            new_content = content.replace(search_pattern, replacement)
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f'Updated {role}/{doc}')
        else:
            print(f'Pattern not found in {role}/{doc}')
