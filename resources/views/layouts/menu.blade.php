<ul class="sidebar-menu">
    <li class="menu-header">Dashboard</li>

    @if(auth()->user()->hasRole('admin'))
    <li><a href="/admin/dashboard" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
    @if(auth()->user()->hasPermission('view-user'))
    <li class="menu-header">User Management</li>
    <li>
        <a href="{{ route('user.index') }}" class="nav-link"><i class="fas fa-users"></i><span>Manage Users</span></a>
    </li>
    @endif
    <li class="menu-header">Document Management</li>
    <li>
        <a href="{{ route('admin.supplier-payment.index') }}" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>Supplier Payments</span></a>
    </li>
    <li>
        <a href="{{ route('admin.petty-cash.index') }}" class="nav-link"><i class="fas fa-wallet"></i><span>Petty Cash</span></a>
    </li>
    <li>
        <a href="{{ route('admin.cash-advance-draw.index') }}" class="nav-link"><i class="fas fa-hand-holding-usd"></i><span>Cash Advance Draw</span></a>
    </li>
    <li>
        <a href="{{ route('admin.cash-advance-realization.index') }}" class="nav-link"><i class="fas fa-file-contract"></i><span>Cash Advance Realization</span></a>
    </li>
    <li>
        <a href="{{ route('admin.international-trip.index') }}" class="nav-link"><i class="fas fa-plane"></i><span>International Trip</span></a>
    </li>
    <li class="menu-header">Master Data</li>
    <li>
        <a href="{{ route('approval-role.index') }}" class="nav-link"><i class="fas fa-user-tag"></i><span>Approval Roles</span></a>
    </li>
    <li>
        <a href="{{ route('approval-status.index') }}" class="nav-link"><i class="fas fa-check-circle"></i><span>Approval Statuses</span></a>
    </li>
    <li>
        <a href="{{ route('cost-center.index') }}" class="nav-link"><i class="fas fa-building"></i><span>Cost Centers</span></a>
    </li>
    <li>
        <a href="{{ route('department.index') }}" class="nav-link"><i class="fas fa-sitemap"></i><span>Departments</span></a>
    </li>
    <li>
        <a href="{{ route('document-status.index') }}" class="nav-link"><i class="fas fa-info-circle"></i><span>Document Statuses</span></a>
    </li>
    <li>
        <a href="{{ route('document-type.index') }}" class="nav-link"><i class="fas fa-file-alt"></i><span>Document Types</span></a>
    </li>
    <li>
        <a href="{{ route('permission.index') }}" class="nav-link"><i class="fas fa-lock"></i><span>Permissions</span></a>
    </li>
    <li>
        <a href="{{ route('position.index') }}" class="nav-link"><i class="fas fa-briefcase"></i><span>Positions</span></a>
    </li>
    <li>
        <a href="{{ route('revision-status.index') }}" class="nav-link"><i class="fas fa-history"></i><span>Revision Statuses</span></a>
    </li>
    <li>
        <a href="{{ route('role.index') }}" class="nav-link"><i class="fas fa-user-shield"></i><span>Roles</span></a>
    </li>
    <li class="menu-header">Report</li>
    <li><a href="{{ route('admin.report.index') }}" class="nav-link"><i class="fas fa-chart-line"></i><span>Report</span></a></li>
    @endif

    @if(auth()->user()->hasRole('accounting-staff'))
    <li><a href="/accounting-staff/dashboard" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
    <li class="menu-header">Document Approval</li>
    <li>
        <a href="{{ route('accounting-staff.supplier-payment.index') }}" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>Supplier Payments</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-staff.petty-cash.index') }}" class="nav-link"><i class="fas fa-wallet"></i><span>Petty Cash</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-staff.cash-advance-draw.index') }}" class="nav-link"><i class="fas fa-hand-holding-usd"></i><span>Cash Advance Draw</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-staff.cash-advance-realization.index') }}" class="nav-link"><i class="fas fa-file-contract"></i><span>Cash Advance Realization</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-staff.international-trip.index') }}" class="nav-link"><i class="fas fa-plane"></i><span>International Trip</span></a>
    </li>
    <li class="menu-header">Report</li>
    <li><a href="{{ route('accounting-staff.report.index') }}" class="nav-link"><i class="fas fa-chart-line"></i><span>Report</span></a></li>
    @endif

    @if(auth()->user()->hasRole('accounting-manager'))
    <li><a href="/accounting-manager/dashboard" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
    <li class="menu-header">Document Approval</li>
    <li>
        <a href="{{ route('accounting-manager.supplier-payment.index') }}" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>Supplier Payments</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-manager.petty-cash.index') }}" class="nav-link"><i class="fas fa-wallet"></i><span>Petty Cash</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-manager.cash-advance-draw.index') }}" class="nav-link"><i class="fas fa-hand-holding-usd"></i><span>Cash Advance Draw</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-manager.cash-advance-realization.index') }}" class="nav-link"><i class="fas fa-file-contract"></i><span>Cash Advance Realization</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-manager.international-trip.index') }}" class="nav-link"><i class="fas fa-plane"></i><span>International Trip</span></a>
    </li>
    <li class="menu-header">Report</li>
    <li><a href="{{ route('accounting-manager.report.index') }}" class="nav-link"><i class="fas fa-chart-line"></i><span>Report</span></a></li>
    @endif

    @if(auth()->user()->hasRole('accounting-gm'))
    <li><a href="/accounting-gm/dashboard" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
    <li class="menu-header">Document Approval</li>
    <li>
        <a href="{{ route('accounting-gm.supplier-payment.index') }}" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>Supplier Payments</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-gm.petty-cash.index') }}" class="nav-link"><i class="fas fa-wallet"></i><span>Petty Cash</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-gm.cash-advance-draw.index') }}" class="nav-link"><i class="fas fa-hand-holding-usd"></i><span>Cash Advance Draw</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-gm.cash-advance-realization.index') }}" class="nav-link"><i class="fas fa-file-contract"></i><span>Cash Advance Realization</span></a>
    </li>
    <li>
        <a href="{{ route('accounting-gm.international-trip.index') }}" class="nav-link"><i class="fas fa-plane"></i><span>International Trip</span></a>
    </li>
    <li class="menu-header">Report</li>
    <li><a href="{{ route('accounting-gm.report.index') }}" class="nav-link"><i class="fas fa-chart-line"></i><span>Report</span></a></li>
    @endif

    @if(auth()->user()->hasRole('user'))
    <li><a href="/user/dashboard" class="nav-link"><i class="fas fa-fire"></i><span>Dashboard</span></a></li>
    <li class="menu-header">Document Submission</li>
    <li>
        <a href="{{ route('user.supplier-payment.index') }}" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>Supplier Payments</span></a>
    </li>
    <li>
        <a href="{{ route('user.petty-cash.index') }}" class="nav-link"><i class="fas fa-wallet"></i><span>Petty Cash</span></a>
    </li>
    <li>
        <a href="{{ route('user.cash-advance-draw.index') }}" class="nav-link"><i class="fas fa-hand-holding-usd"></i><span>Cash Advance Draw</span></a>
    </li>
    <li>
        <a href="{{ route('user.cash-advance-realization.index') }}" class="nav-link"><i class="fas fa-file-contract"></i><span>Cash Advance Realization</span></a>
    </li>
    <li>
        <a href="{{ route('user.international-trip.index') }}" class="nav-link"><i class="fas fa-plane"></i><span>International Trip</span></a>
    </li>
    <li class="menu-header">Report</li>
    <li><a href="{{ route('user.report.index') }}" class="nav-link"><i class="fas fa-chart-line"></i><span>Report</span></a></li>
    @endif
</ul>