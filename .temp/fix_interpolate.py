import os, glob

for f in glob.glob('d:/Project Apps/document_logbook/app/Http/Controllers/Accounting*/*Controller.php'):
    content = open(f, 'r', encoding='utf-8').read()
    content = content.replace('"$supplierPayment ID', '"SupplierPayment ID')
    content = content.replace('"$pettyCash ID', '"PettyCash ID')
    content = content.replace('"$internationalTrip ID', '"InternationalTrip ID')
    content = content.replace('"$cashAdvanceDraw ID', '"CashAdvanceDraw ID')
    content = content.replace('"$cashAdvanceRealization ID', '"CashAdvanceRealization ID')
    open(f, 'w', encoding='utf-8').write(content)
