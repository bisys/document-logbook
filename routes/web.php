<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\CostCenterController;
use App\Http\Controllers\Admin\DocumentStatusController;
use App\Http\Controllers\Admin\ApprovalStatusController;
use App\Http\Controllers\Admin\ApprovalRoleController;
use App\Http\Controllers\Admin\RevisionStatusController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SupplierPaymentController as AdminSupplierPaymentController;
use App\Http\Controllers\User\SupplierPaymentController as UserSupplierPaymentController;
use App\Http\Controllers\AccountingStaff\SupplierPaymentController as AccountingStaffSupplierPaymentController;
use App\Http\Controllers\AccountingManager\SupplierPaymentController as AccountingManagerSupplierPaymentController;
use App\Http\Controllers\AccountingGM\SupplierPaymentController as AccountingGMSupplierPaymentController;

// Route::get('/', function () {
//     return view('dashboard');
// });

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['role:admin'])->prefix('/admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        });

        Route::resource('/department', DepartmentController::class);
        Route::resource('/position', PositionController::class);
        Route::resource('/role', RoleController::class);
        Route::get('/role/{role}/permission', [RoleController::class, 'permissions']);
        Route::post('/role/{role}/permission', [RoleController::class, 'syncPermissions']);
        Route::resource('/permission', PermissionController::class);
        Route::resource('/approval', ApprovalController::class);
        Route::resource('/document-type', DocumentTypeController::class);
        Route::resource('/cost-center', CostCenterController::class);
        Route::resource('/document-status', DocumentStatusController::class);
        Route::resource('/approval-status', ApprovalStatusController::class);
        Route::resource('/approval-role', ApprovalRoleController::class);
        Route::resource('/revision-status', RevisionStatusController::class);
        Route::resource('/user', UserController::class);

        Route::prefix('/supplier-payment')->name('admin.supplier-payment.')->group(function () {
            Route::get('/', [AdminSupplierPaymentController::class, 'index'])->name('index');
            Route::get('/{supplierPayment}', [AdminSupplierPaymentController::class, 'show'])->name('show');
            Route::post('/{supplierPayment}/update-status', [AdminSupplierPaymentController::class, 'updateStatus'])->name('update-status');
        });
    });

    Route::middleware(['role:accounting-staff'])->prefix('/accounting')->name('accounting-staff.')->group(function () {
        Route::get('/dashboard', function () {
            return view('accounting_staff.dashboard');
        });

        Route::prefix('/supplier-payment')->name('supplier-payment.')->group(function () {
            Route::get('/', [AccountingStaffSupplierPaymentController::class, 'index'])->name('index');
            Route::get('/{supplierPayment}', [AccountingStaffSupplierPaymentController::class, 'show'])->name('show');
            Route::post('/{supplierPayment}/add-revision', [AccountingStaffSupplierPaymentController::class, 'addRevision'])->name('add-revision');
            Route::post('/{supplierPayment}/approve', [AccountingStaffSupplierPaymentController::class, 'approve'])->name('approve');
            Route::post('/{supplierPayment}/reject', [AccountingStaffSupplierPaymentController::class, 'reject'])->name('reject');
        });
    });

    Route::middleware(['role:accounting-manager'])->prefix('/accounting-manager')->name('accounting-manager.')->group(function () {
        Route::get('/dashboard', function () {
            return view('accounting_manager.dashboard');
        });

        Route::prefix('/supplier-payment')->name('supplier-payment.')->group(function () {
            Route::get('/', [AccountingManagerSupplierPaymentController::class, 'index'])->name('index');
            Route::get('/{supplierPayment}', [AccountingManagerSupplierPaymentController::class, 'show'])->name('show');
            Route::post('/{supplierPayment}/approve', [AccountingManagerSupplierPaymentController::class, 'approve'])->name('approve');
            Route::post('/{supplierPayment}/reject', [AccountingManagerSupplierPaymentController::class, 'reject'])->name('reject');
        });
    });

    Route::middleware(['role:accounting-gm'])->prefix('/accounting-gm')->name('accounting-gm.')->group(function () {
        Route::get('/dashboard', function () {
            return view('accounting_gm.dashboard');
        });

        Route::prefix('/supplier-payment')->name('supplier-payment.')->group(function () {
            Route::get('/', [AccountingGMSupplierPaymentController::class, 'index'])->name('index');
            Route::get('/{supplierPayment}', [AccountingGMSupplierPaymentController::class, 'show'])->name('show');
            Route::post('/{supplierPayment}/approve', [AccountingGMSupplierPaymentController::class, 'approve'])->name('approve');
            Route::post('/{supplierPayment}/reject', [AccountingGMSupplierPaymentController::class, 'reject'])->name('reject');
        });
    });

    Route::middleware(['role:user'])->prefix('/user')->name('user.')->group(function () {
        Route::get('/dashboard', function () {
            return view('user.dashboard');
        });

        Route::prefix('/supplier-payment')->name('supplier-payment.')->group(function () {
            Route::get('/', [UserSupplierPaymentController::class, 'index'])->name('index');
            Route::get('/create', [UserSupplierPaymentController::class, 'create'])->name('create');
            Route::post('/', [UserSupplierPaymentController::class, 'store'])->name('store');
            Route::get('/{supplierPayment}', [UserSupplierPaymentController::class, 'show'])->name('show');
            Route::get('/{supplierPayment}/edit', [UserSupplierPaymentController::class, 'edit'])->name('edit');
            Route::put('/{supplierPayment}', [UserSupplierPaymentController::class, 'update'])->name('update');
            Route::post('/{supplierPayment}/{revision}/submit-revision', [UserSupplierPaymentController::class, 'submitRevision'])->name('submit-revision');
        });
    });

    // Legacy route - redirected to user.supplier-payment for backward compatibility
    // Route::resource('/supplier-payment', SupplierPaymentController::class);
});
