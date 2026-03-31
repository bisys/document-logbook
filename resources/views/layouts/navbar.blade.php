<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
            <li><a href="#" data-toggle="search" class="nav-link nav-link-lg d-sm-none"><i class="fas fa-search"></i></a></li>
        </ul>

    </form>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown dropdown-list-toggle"><a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg {{ Auth::user()->unreadNotifications->count() > 0 ? 'beep' : '' }}"><i class="far fa-bell"></i></a>
            <div class="dropdown-menu dropdown-list dropdown-menu-right">
                <div class="dropdown-header">Notifications
                    <div class="float-right">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('delete-all-notifications-form').submit();">Delete All</a>
                        <form id="delete-all-notifications-form" action="{{ route('notifications.delete-all') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
                <div class="dropdown-list-content dropdown-list-icons">
                    @forelse(Auth::user()->notifications as $notification)
                        <a href="{{ route('notifications.mark-as-read', $notification->id) }}" class="dropdown-item {{ $notification->read_at === null ? 'dropdown-item-unread' : '' }}">
                            <div class="dropdown-item-icon {{ $notification->data['icon_bg'] ?? 'bg-primary' }} text-white">
                                <i class="{{ $notification->data['icon'] ?? 'fas fa-bell' }}"></i>
                            </div>
                            <div class="dropdown-item-desc">
                                {{ $notification->data['message'] ?? 'New notification' }}
                                <div class="time {{ $notification->read_at === null ? 'text-primary' : '' }}">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="dropdown-item text-center">
                            <p class="text-muted mb-0 mt-3">No notifications found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </li>
        <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                <div class="d-sm-none d-lg-inline-block">Hi, {{ Auth::user()->name }}</div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('change-password') }}" class="dropdown-item has-icon">
                    <i class="fas fa-key"></i> Change Password
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </form>
            </div>
        </li>
    </ul>
</nav>