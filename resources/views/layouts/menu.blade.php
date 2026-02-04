<ul class="sidebar-menu">
    <li class="menu-header">Dashboard</li>
    <li class="nav-item dropdown">
        <a href="#" class="nav-link has-dropdown"><i class="fas fa-fire"></i><span>Dashboard</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="index-0.html">General Dashboard</a></li>
            <li><a class="nav-link" href="index.html">Ecommerce Dashboard</a></li>
        </ul>
    </li>
    @if(auth()->user()->hasRole('admin'))
    <li><a href="/admin/dashboard">Admin Dashboard</a></li>
    @if(auth()->user()->hasPermission('view-user'))
    <li class="menu-header">User Management</li>
    <li>
        <a href="{{ route('user.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Manage Users</span></a>
    </li>
    @endif
    <li class="menu-header">Master Data</li>
    <li>
        <a href="{{ route('approval.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Approvals</span></a>
    </li>
    <li>
        <a href="{{ route('cost-center.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Cost Centers</span></a>
    </li>
    <li>
        <a href="{{ route('department.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Departments</span></a>
    </li>
    <li>
        <a href="{{ route('document-type.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Document Types</span></a>
    </li>
    <li>
        <a href="{{ route('permission.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Permissions</span></a>
    </li>
    <li>
        <a href="{{ route('position.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Positions</span></a>
    </li>
    <li>
        <a href="{{ route('role.index') }}" class="nav-link"><i class="fas fa-fire"></i><span>Roles</span></a>
    </li>
    @endif

    @if(auth()->user()->hasRole('accounting'))
    <li><a href="/accounting/dashboard">Accounting Dashboard</a></li>
    <li><a href="/accounting/report">Report</a></li>
    @endif

    @if(auth()->user()->hasRole('user'))
    <li><a href="/dashboard">User Dashboard</a></li>
    @endif
</ul>