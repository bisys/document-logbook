# Approval Workflow Documentation

## Overview

Sistem approval document untuk Supplier Payment telah diupdate untuk menggunakan workflow bertahap berdasarkan ApprovalRole dengan sequence yang terurut.

## Approval Sequence

Dokumen harus melalui tahapan approval secara berurutan seperti berikut:

1. **Sequence 1** - Accounting Staff (Initial Review)
2. **Sequence 2** - Accounting Manager (Manager Review)
3. **Sequence 3** - Accounting GM (Final Approval)

Approval **TIDAK BOLEH dilangkahi** (skip). Setiap tahap harus diselesaikan sebelum tahap berikutnya dapat dilakukan.

## Database Setup

### ApprovalRole Table

Pastikan ApprovalRole table sudah memiliki data dengan sequence yang benar:

```sql
INSERT INTO approval_roles (name, slug, sequence, created_at, updated_at) VALUES
('Accounting Staff', 'accounting-staff', 1, NOW(), NOW()),
('Accounting Manager', 'accounting-manager', 2, NOW(), NOW()),
('Accounting GM', 'accounting-gm', 3, NOW(), NOW());
```

### Approvals Table Structure

Kolom yang penting:

- `approvable_id` dan `approvable_type` - Polymorphic relation ke parent document
- `approval_role_id` - Foreign key ke ApprovalRole
- `user_id` - Siapa yang melakukan approval
- `approval_status_id` - Status approval (1=approved, 2=rejected, etc)
- `approval_at` - Waktu approval dilakukan (bukan `approved_at`)
- `remark` - Catatan/komentar approval

## Model Changes

### Approval Model

Relationship telah diupdate:

```php
public function role()
{
    return $this->belongsTo(ApprovalRole::class, 'approval_role_id');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}
```

## Service Class

### ApprovalService (`app/Services/ApprovalService.php`)

Baru dibuat untuk menghandle business logic approval:

**Main Methods:**

1. `getAllApprovalRoles()` - Dapat semua approval roles terurut by sequence
2. `getNextApprovalRole(Model $document)` - Dapatkan role approval berikutnya yang belum selesai
3. `getNextApprovalSequence(Model $document)` - Dapatkan sequence number role berikutnya
4. `getApprovalByRole(Model $document, $approvalRoleId)` - Dapatkan approval record untuk role tertentu
5. `allPreviousApprovalsComplete(Model $document, $approvalRoleId)` - Cek apakah semua approval sebelumnya sudah complete
6. `isValidApprovalSequence(Model $document, $approvalRoleId)` - Validasi bahwa role ini adalah tahap approval berikutnya
7. `getApprovalChain(Model $document)` - Dapatkan chain approval lengkap dengan status
8. `isApproved(Approval $approval)` - Cek apakah approval adalah status "approved"

## Controller Changes

### 1. AccountingStaffSupplierPaymentController

**Sequence: 1 (First Approval)**

- Constructor sekarang menerima ApprovalService
- `index()` - Menampilkan dokumen yang belum punya approval dari staff
- `show()` - Menampilkan detail + approval chain
- `approve()` - Validasi tidak ada pending revisions, lalu buat approval record dengan approval_role_id = staff role id
- `reject()` - Create rejection record dengan approval_role_id = staff role id

### 2. AccountingManagerSupplierPaymentController

**Sequence: 2 (Manager Approval)**

- Constructor sekarang menerima ApprovalService
- `index()` - Menampilkan dokumen WHERE staff role sudah approve DAN manager belum
    - Filter menggunakan `isValidApprovalSequence()` untuk pastikan manager role adalah next approval
- `show()` - Menampilkan detail + approval chain
- `approve()` -
    - Validasi `allPreviousApprovalsComplete()` untuk pastikan staff sudah approve
    - Validasi `isValidApprovalSequence()` untuk pastikan ini tahap manager
    - Create approval record dengan approval_role_id = manager role id
- `reject()` - Create rejection record dengan approval_role_id = manager role id

### 3. AccountingGMSupplierPaymentController

**Sequence: 3 (Final Approval)**

- Constructor sekarang menerima ApprovalService
- `index()` - Menampilkan dokumen WHERE manager role sudah approve DAN GM belum
    - Filter menggunakan `isValidApprovalSequence()` untuk pastikan GM role adalah next approval
- `show()` - Menampilkan detail + approval chain
- `approve()` -
    - Validasi `allPreviousApprovalsComplete()` untuk pastikan staff DAN manager sudah approve
    - Validasi `isValidApprovalSequence()` untuk pastikan ini tahap GM
    - Create approval record dengan approval_role_id = GM role id
    - Update document status ke 'fully-approved' (ini approval final)
- `reject()` - Create rejection record dengan approval_role_id = GM role id

## View Changes

### Show Views

Semua show views (staff, manager, GM) telah diupdate untuk menampilkan approval chain dengan cara baru:

```blade
@forelse($approvalChain as $item)
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <strong>{{ $item['role']->name }}</strong>
            <div class="text-muted small">Sequence: {{ $item['role']->sequence }}</div>
        </div>
        <div>
            @if($item['status'] === 'approved')
                <span class="badge badge-success">Approved</span>
            @elseif($item['status'] === 'rejected')
                <span class="badge badge-danger">Rejected</span>
            @else
                <span class="badge badge-secondary">Pending</span>
            @endif
        </div>
    </div>
    <!-- Approval details: user, timestamp, remark -->
</div>
@endforelse
```

Data `$approvalChain` struktur:

```php
[
    'role' => ApprovalRole object,
    'approval' => Approval object atau null,
    'status' => 'approved'|'rejected'|'pending',
    'sequence' => integer
]
```

## Workflow Summary

### Document Journey:

1. **User** submit dokumen → Document Status = "waiting-approval"
2. **Accounting Staff** review dokumen
    - Jika ada issue → Request Revision
    - Jika OK → Approve (create approval dengan role_id=staff)
3. **Accounting Manager** review (hanya tampil jika staff sudah approve)
    - Jika ada issue → Reject
    - Jika OK → Approve (create approval dengan role_id=manager)
4. **Accounting GM** review (hanya tampil jika manager sudah approve)
    - Jika ada issue → Reject
    - Jika OK → Approve (create approval dengan role_id=gm) → Document Status = "fully-approved"

### Important Rules:

- ❌ Manager TIDAK bisa approve jika Staff belum approve
- ❌ GM TIDAK bisa approve jika Manager atau Staff belum approve
- ✅ Approval HARUS berurutan sesuai sequence
- ✅ Document bisa di-reject di setiap tahap
- ✅ User bisa request revision jika ada issue (Staff only)

## Testing Checklist

- [ ] ApprovalRole table sudah populated dengan 3 records (sequence 1, 2, 3)
- [ ] Accounting Staff hanya bisa melihat dokumen tanpa approval dari staff
- [ ] Accounting Staff bisa approve → create Approval record dengan approval_role_id sesuai
- [ ] Accounting Manager hanya bisa melihat dokumen yg sudah di-approve staff
- [ ] Accounting Manager tidak bisa approve jika staff belum approve (error thrown)
- [ ] Accounting GM hanya bisa melihat dokumen yg sudah di-approve manager
- [ ] Accounting GM tidak bisa approve jika staff atau manager belum approve (error thrown)
- [ ] Approval chain view menampilkan sequence dengan benar
- [ ] Dokumen menunjukkan status "fully-approved" jika semua 3 approval selesai
- [ ] Rejection di setiap tahap mengubah document status ke "rejected"
- [ ] Previous approval tidak bisa dihapus/diubah

## Migration Notes

Jika migrating dari sistem lama:

1. Update semua existing approval records dengan approval_role_id yang benar berdasarkan peran user
2. Pastikan ApprovalRole table sudah punya data dengan sequence yang benar
3. Validate bahwa sequence approval di records sudah sesuai urutan
4. Jika ada dokumen dalam limbo, sesuaikan status dan approval records
