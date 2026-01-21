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
    <li><a href="/admin/users">Manage Users</a></li>
    @endif

    @if(auth()->user()->hasRole('accounting'))
    <li><a href="/accounting/dashboard">Accounting Dashboard</a></li>
    <li><a href="/accounting/report">Report</a></li>
    @endif

    @if(auth()->user()->hasRole('user'))
    <li><a href="/dashboard">User Dashboard</a></li>
    @endif
</ul>