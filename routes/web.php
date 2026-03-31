<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\AccountingStaff\DashboardController as AccountingStaffDashboardController;
use App\Http\Controllers\AccountingManager\DashboardController as AccountingManagerDashboardController;
use App\Http\Controllers\AccountingGM\DashboardController as AccountingGMDashboardController;
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
use App\Http\Controllers\Admin\PettyCashController as AdminPettyCashController;
use App\Http\Controllers\Admin\InternationalTripController as AdminInternationalTripController;
use App\Http\Controllers\Admin\CashAdvanceDrawController as AdminCashAdvanceDrawController;
use App\Http\Controllers\Admin\CashAdvanceRealizationController as AdminCashAdvanceRealizationController;
use App\Http\Controllers\User\SupplierPaymentController as UserSupplierPaymentController;
use App\Http\Controllers\User\PettyCashController as UserPettyCashController;
use App\Http\Controllers\User\InternationalTripController as UserInternationalTripController;
use App\Http\Controllers\User\CashAdvanceDrawController as UserCashAdvanceDrawController;
use App\Http\Controllers\User\CashAdvanceRealizationController as UserCashAdvanceRealizationController;
use App\Http\Controllers\AccountingStaff\SupplierPaymentController as AccountingStaffSupplierPaymentController;
use App\Http\Controllers\AccountingStaff\PettyCashController as AccountingStaffPettyCashController;
use App\Http\Controllers\AccountingStaff\InternationalTripController as AccountingStaffInternationalTripController;
use App\Http\Controllers\AccountingStaff\CashAdvanceDrawController as AccountingStaffCashAdvanceDrawController;
use App\Http\Controllers\AccountingStaff\CashAdvanceRealizationController as AccountingStaffCashAdvanceRealizationController;
use App\Http\Controllers\AccountingManager\SupplierPaymentController as AccountingManagerSupplierPaymentController;
use App\Http\Controllers\AccountingManager\PettyCashController as AccountingManagerPettyCashController;
use App\Http\Controllers\AccountingManager\InternationalTripController as AccountingManagerInternationalTripController;
use App\Http\Controllers\AccountingManager\CashAdvanceDrawController as AccountingManagerCashAdvanceDrawController;
use App\Http\Controllers\AccountingManager\CashAdvanceRealizationController as AccountingManagerCashAdvanceRealizationController;
use App\Http\Controllers\AccountingGM\SupplierPaymentController as AccountingGMSupplierPaymentController;
use App\Http\Controllers\AccountingGM\PettyCashController as AccountingGMPettyCashController;
use App\Http\Controllers\AccountingGM\InternationalTripController as AccountingGMInternationalTripController;
use App\Http\Controllers\AccountingGM\CashAdvanceDrawController as AccountingGMCashAdvanceDrawController;
use App\Http\Controllers\AccountingGM\CashAdvanceRealizationController as AccountingGMCashAdvanceRealizationController;

// Route::get('/', function () {
//     return view('dashboard');
// });

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/change-password', [AuthController::class,'changePasswordForm'])->name('change-password');
    Route::post('/change-password', [AuthController::class,'changePassword'])->name('change-password');
    
    Route::get('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/delete-all', [NotificationController::class, 'deleteAll'])->name('notifications.delete-all');

    Route::middleware(['role:admin'])->prefix('/admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

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

        Route::prefix('/petty-cash')->name('admin.petty-cash.')->group(function () {
            Route::get('/', [AdminPettyCashController::class, 'index'])->name('index');
            Route::get('/{pettyCash}', [AdminPettyCashController::class, 'show'])->name('show');
            Route::post('/{pettyCash}/update-status', [AdminPettyCashController::class, 'updateStatus'])->name('update-status');
        });

        Route::prefix('/international-trip')->name('admin.international-trip.')->group(function () {
            Route::get('/', [AdminInternationalTripController::class, 'index'])->name('index');
            Route::get('/{internationalTrip}', [AdminInternationalTripController::class, 'show'])->name('show');
            Route::post('/{internationalTrip}/update-status', [AdminInternationalTripController::class, 'updateStatus'])->name('update-status');
        });

        Route::prefix('/cash-advance-draw')->name('admin.cash-advance-draw.')->group(function () {
            Route::get('/', [AdminCashAdvanceDrawController::class, 'index'])->name('index');
            Route::get('/{cashAdvanceDraw}', [AdminCashAdvanceDrawController::class, 'show'])->name('show');
            Route::post('/{cashAdvanceDraw}/update-status', [AdminCashAdvanceDrawController::class, 'updateStatus'])->name('update-status');
        });

        Route::prefix('/cash-advance-realization')->name('admin.cash-advance-realization.')->group(function () {
            Route::get('/', [AdminCashAdvanceRealizationController::class, 'index'])->name('index');
            Route::get('/{cashAdvanceRealization}', [AdminCashAdvanceRealizationController::class, 'show'])->name('show');
            Route::post('/{cashAdvanceRealization}/update-status', [AdminCashAdvanceRealizationController::class, 'updateStatus'])->name('update-status');
        });
    });

    Route::middleware(['role:accounting-staff'])->prefix('/accounting')->name('accounting-staff.')->group(function () {
        Route::get('/dashboard', [AccountingStaffDashboardController::class, 'index']);

        Route::prefix('/supplier-payment')->name('supplier-payment.')->group(function () {
            Route::get('/', [AccountingStaffSupplierPaymentController::class, 'index'])->name('index');
            Route::get('/{supplierPayment}', [AccountingStaffSupplierPaymentController::class, 'show'])->name('show');
            Route::post('/{supplierPayment}/add-revision', [AccountingStaffSupplierPaymentController::class, 'addRevision'])->name('add-revision');
            Route::post('/{supplierPayment}/approve', [AccountingStaffSupplierPaymentController::class, 'approve'])->name('approve');
            Route::post('/{supplierPayment}/reject', [AccountingStaffSupplierPaymentController::class, 'reject'])->name('reject');
        });

        Route::prefix('/petty-cash')->name('petty-cash.')->group(function () {
            Route::get('/', [AccountingStaffPettyCashController::class, 'index'])->name('index');
            Route::get('/{pettyCash}', [AccountingStaffPettyCashController::class, 'show'])->name('show');
            Route::post('/{pettyCash}/add-revision', [AccountingStaffPettyCashController::class, 'addRevision'])->name('add-revision');
            Route::post('/{pettyCash}/approve', [AccountingStaffPettyCashController::class, 'approve'])->name('approve');
            Route::post('/{pettyCash}/reject', [AccountingStaffPettyCashController::class, 'reject'])->name('reject');
        });

        Route::prefix('/international-trip')->name('international-trip.')->group(function () {
            Route::get('/', [AccountingStaffInternationalTripController::class, 'index'])->name('index');
            Route::get('/{internationalTrip}', [AccountingStaffInternationalTripController::class, 'show'])->name('show');
            Route::post('/{internationalTrip}/add-revision', [AccountingStaffInternationalTripController::class, 'addRevision'])->name('add-revision');
            Route::post('/{internationalTrip}/approve', [AccountingStaffInternationalTripController::class, 'approve'])->name('approve');
            Route::post('/{internationalTrip}/reject', [AccountingStaffInternationalTripController::class, 'reject'])->name('reject');
        });

        Route::prefix('/cash-advance-draw')->name('cash-advance-draw.')->group(function () {
            Route::get('/', [AccountingStaffCashAdvanceDrawController::class, 'index'])->name('index');
            Route::get('/{cashAdvanceDraw}', [AccountingStaffCashAdvanceDrawController::class, 'show'])->name('show');
            Route::post('/{cashAdvanceDraw}/add-revision', [AccountingStaffCashAdvanceDrawController::class, 'addRevision'])->name('add-revision');
            Route::post('/{cashAdvanceDraw}/approve', [AccountingStaffCashAdvanceDrawController::class, 'approve'])->name('approve');
            Route::post('/{cashAdvanceDraw}/reject', [AccountingStaffCashAdvanceDrawController::class, 'reject'])->name('reject');
        });

        Route::prefix('/cash-advance-realization')->name('cash-advance-realization.')->group(function () {
            Route::get('/', [AccountingStaffCashAdvanceRealizationController::class, 'index'])->name('index');
            Route::get('/{cashAdvanceRealization}', [AccountingStaffCashAdvanceRealizationController::class, 'show'])->name('show');
            Route::post('/{cashAdvanceRealization}/add-revision', [AccountingStaffCashAdvanceRealizationController::class, 'addRevision'])->name('add-revision');
            Route::post('/{cashAdvanceRealization}/approve', [AccountingStaffCashAdvanceRealizationController::class, 'approve'])->name('approve');
            Route::post('/{cashAdvanceRealization}/reject', [AccountingStaffCashAdvanceRealizationController::class, 'reject'])->name('reject');
        });
    });

    Route::middleware(['role:accounting-manager'])->prefix('/accounting-manager')->name('accounting-manager.')->group(function () {
        Route::get('/dashboard', [AccountingManagerDashboardController::class, 'index']);

        Route::prefix('/supplier-payment')->name('supplier-payment.')->group(function () {
            Route::get('/', [AccountingManagerSupplierPaymentController::class, 'index'])->name('index');
            Route::get('/{supplierPayment}', [AccountingManagerSupplierPaymentController::class, 'show'])->name('show');
            Route::post('/{supplierPayment}/approve', [AccountingManagerSupplierPaymentController::class, 'approve'])->name('approve');
            Route::post('/{supplierPayment}/reject', [AccountingManagerSupplierPaymentController::class, 'reject'])->name('reject');
        });

        Route::prefix('/petty-cash')->name('petty-cash.')->group(function () {
            Route::get('/', [AccountingManagerPettyCashController::class, 'index'])->name('index');
            Route::get('/{pettyCash}', [AccountingManagerPettyCashController::class, 'show'])->name('show');
            Route::post('/{pettyCash}/approve', [AccountingManagerPettyCashController::class, 'approve'])->name('approve');
            Route::post('/{pettyCash}/reject', [AccountingManagerPettyCashController::class, 'reject'])->name('reject');
        });

        Route::prefix('/international-trip')->name('international-trip.')->group(function () {
            Route::get('/', [AccountingManagerInternationalTripController::class, 'index'])->name('index');
            Route::get('/{internationalTrip}', [AccountingManagerInternationalTripController::class, 'show'])->name('show');
            Route::post('/{internationalTrip}/approve', [AccountingManagerInternationalTripController::class, 'approve'])->name('approve');
            Route::post('/{internationalTrip}/reject', [AccountingManagerInternationalTripController::class, 'reject'])->name('reject');
        });

        Route::prefix('/cash-advance-draw')->name('cash-advance-draw.')->group(function () {
            Route::get('/', [AccountingManagerCashAdvanceDrawController::class, 'index'])->name('index');
            Route::get('/{cashAdvanceDraw}', [AccountingManagerCashAdvanceDrawController::class, 'show'])->name('show');
            Route::post('/{cashAdvanceDraw}/approve', [AccountingManagerCashAdvanceDrawController::class, 'approve'])->name('approve');
            Route::post('/{cashAdvanceDraw}/reject', [AccountingManagerCashAdvanceDrawController::class, 'reject'])->name('reject');
        });

        Route::prefix('/cash-advance-realization')->name('cash-advance-realization.')->group(function () {
            Route::get('/', [AccountingManagerCashAdvanceRealizationController::class, 'index'])->name('index');
            Route::get('/{cashAdvanceRealization}', [AccountingManagerCashAdvanceRealizationController::class, 'show'])->name('show');
            Route::post('/{cashAdvanceRealization}/approve', [AccountingManagerCashAdvanceRealizationController::class, 'approve'])->name('approve');
            Route::post('/{cashAdvanceRealization}/reject', [AccountingManagerCashAdvanceRealizationController::class, 'reject'])->name('reject');
        });
    });

    Route::middleware(['role:accounting-gm'])->prefix('/accounting-gm')->name('accounting-gm.')->group(function () {
        Route::get('/dashboard', [AccountingGMDashboardController::class, 'index']);

        Route::prefix('/supplier-payment')->name('supplier-payment.')->group(function () {
            Route::get('/', [AccountingGMSupplierPaymentController::class, 'index'])->name('index');
            Route::get('/{supplierPayment}', [AccountingGMSupplierPaymentController::class, 'show'])->name('show');
            Route::post('/{supplierPayment}/approve', [AccountingGMSupplierPaymentController::class, 'approve'])->name('approve');
            Route::post('/{supplierPayment}/reject', [AccountingGMSupplierPaymentController::class, 'reject'])->name('reject');
        });

        Route::prefix('/petty-cash')->name('petty-cash.')->group(function () {
            Route::get('/', [AccountingGMPettyCashController::class, 'index'])->name('index');
            Route::get('/{pettyCash}', [AccountingGMPettyCashController::class, 'show'])->name('show');
            Route::post('/{pettyCash}/approve', [AccountingGMPettyCashController::class, 'approve'])->name('approve');
            Route::post('/{pettyCash}/reject', [AccountingGMPettyCashController::class, 'reject'])->name('reject');
        });

        Route::prefix('/international-trip')->name('international-trip.')->group(function () {
            Route::get('/', [AccountingGMInternationalTripController::class, 'index'])->name('index');
            Route::get('/{internationalTrip}', [AccountingGMInternationalTripController::class, 'show'])->name('show');
            Route::post('/{internationalTrip}/approve', [AccountingGMInternationalTripController::class, 'approve'])->name('approve');
            Route::post('/{internationalTrip}/reject', [AccountingGMInternationalTripController::class, 'reject'])->name('reject');
        });

        Route::prefix('/cash-advance-draw')->name('cash-advance-draw.')->group(function () {
            Route::get('/', [AccountingGMCashAdvanceDrawController::class, 'index'])->name('index');
            Route::get('/{cashAdvanceDraw}', [AccountingGMCashAdvanceDrawController::class, 'show'])->name('show');
            Route::post('/{cashAdvanceDraw}/approve', [AccountingGMCashAdvanceDrawController::class, 'approve'])->name('approve');
            Route::post('/{cashAdvanceDraw}/reject', [AccountingGMCashAdvanceDrawController::class, 'reject'])->name('reject');
        });

        Route::prefix('/cash-advance-realization')->name('cash-advance-realization.')->group(function () {
            Route::get('/', [AccountingGMCashAdvanceRealizationController::class, 'index'])->name('index');
            Route::get('/{cashAdvanceRealization}', [AccountingGMCashAdvanceRealizationController::class, 'show'])->name('show');
            Route::post('/{cashAdvanceRealization}/approve', [AccountingGMCashAdvanceRealizationController::class, 'approve'])->name('approve');
            Route::post('/{cashAdvanceRealization}/reject', [AccountingGMCashAdvanceRealizationController::class, 'reject'])->name('reject');
        });
    });

    Route::middleware(['role:user'])->prefix('/user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index']);

        Route::prefix('/supplier-payment')->name('supplier-payment.')->group(function () {
            Route::get('/', [UserSupplierPaymentController::class, 'index'])->name('index');
            Route::get('/create', [UserSupplierPaymentController::class, 'create'])->name('create');
            Route::post('/', [UserSupplierPaymentController::class, 'store'])->name('store');
            Route::get('/{supplierPayment}', [UserSupplierPaymentController::class, 'show'])->name('show');
            Route::get('/{supplierPayment}/edit', [UserSupplierPaymentController::class, 'edit'])->name('edit');
            Route::put('/{supplierPayment}', [UserSupplierPaymentController::class, 'update'])->name('update');
            Route::post('/{supplierPayment}/{revision}/submit-revision', [UserSupplierPaymentController::class, 'submitRevision'])->name('submit-revision');
        });

        Route::prefix('/petty-cash')->name('petty-cash.')->group(function () {
            Route::get('/', [UserPettyCashController::class, 'index'])->name('index');
            Route::get('/create', [UserPettyCashController::class, 'create'])->name('create');
            Route::post('/', [UserPettyCashController::class, 'store'])->name('store');
            Route::get('/{pettyCash}', [UserPettyCashController::class, 'show'])->name('show');
            Route::get('/{pettyCash}/edit', [UserPettyCashController::class, 'edit'])->name('edit');
            Route::put('/{pettyCash}', [UserPettyCashController::class, 'update'])->name('update');
            Route::post('/{pettyCash}/{revision}/submit-revision', [UserPettyCashController::class, 'submitRevision'])->name('submit-revision');
        });

        Route::prefix('/international-trip')->name('international-trip.')->group(function () {
            Route::get('/', [UserInternationalTripController::class, 'index'])->name('index');
            Route::get('/create', [UserInternationalTripController::class, 'create'])->name('create');
            Route::post('/', [UserInternationalTripController::class, 'store'])->name('store');
            Route::get('/{internationalTrip}', [UserInternationalTripController::class, 'show'])->name('show');
            Route::get('/{internationalTrip}/edit', [UserInternationalTripController::class, 'edit'])->name('edit');
            Route::put('/{internationalTrip}', [UserInternationalTripController::class, 'update'])->name('update');
            Route::post('/{internationalTrip}/{revision}/submit-revision', [UserInternationalTripController::class, 'submitRevision'])->name('submit-revision');
        });

        Route::prefix('/cash-advance-draw')->name('cash-advance-draw.')->group(function () {
            Route::get('/', [UserCashAdvanceDrawController::class, 'index'])->name('index');
            Route::get('/create', [UserCashAdvanceDrawController::class, 'create'])->name('create');
            Route::post('/', [UserCashAdvanceDrawController::class, 'store'])->name('store');
            Route::get('/{cashAdvanceDraw}', [UserCashAdvanceDrawController::class, 'show'])->name('show');
            Route::get('/{cashAdvanceDraw}/edit', [UserCashAdvanceDrawController::class, 'edit'])->name('edit');
            Route::put('/{cashAdvanceDraw}', [UserCashAdvanceDrawController::class, 'update'])->name('update');
            Route::post('/{cashAdvanceDraw}/{revision}/submit-revision', [UserCashAdvanceDrawController::class, 'submitRevision'])->name('submit-revision');
        });

        Route::prefix('/cash-advance-realization')->name('cash-advance-realization.')->group(function () {
            Route::get('/', [UserCashAdvanceRealizationController::class, 'index'])->name('index');
            Route::get('/create', [UserCashAdvanceRealizationController::class, 'create'])->name('create');
            Route::post('/', [UserCashAdvanceRealizationController::class, 'store'])->name('store');
            Route::get('/{cashAdvanceRealization}', [UserCashAdvanceRealizationController::class, 'show'])->name('show');
            Route::get('/{cashAdvanceRealization}/edit', [UserCashAdvanceRealizationController::class, 'edit'])->name('edit');
            Route::put('/{cashAdvanceRealization}', [UserCashAdvanceRealizationController::class, 'update'])->name('update');
            Route::post('/{cashAdvanceRealization}/{revision}/submit-revision', [UserCashAdvanceRealizationController::class, 'submitRevision'])->name('submit-revision');
        });
    });

    // Legacy route - redirected to user.supplier-payment for backward compatibility
    // Route::resource('/supplier-payment', SupplierPaymentController::class);
});
