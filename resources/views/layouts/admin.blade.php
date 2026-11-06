<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Laravel Mongo Store</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CDN for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- DARK THEME (Default Black Theme) --- */
        html[data-theme="dark"] {
            --bg-dark: #000000;
            --bg-card: #0c0c0e;
            --bg-sidebar: #050507;
            --bg-header: #050507;
            --text-main: #ffffff;
            --text-muted: #a1a1aa;
            --border-color: #27272a;
            --nav-hover-bg: #18181b;
            --nav-active-bg: #18181b;
            --nav-active-border: #ffffff;
            --btn-bg: #18181b;
            --btn-text: #ffffff;
            --btn-border: #3f3f46;
            --btn-hover-bg: #ffffff;
            --btn-hover-text: #000000;
            --avatar-bg: #ffffff;
            --avatar-text: #000000;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            --icon-bg: #18181b;
            --icon-border: #27272a;
            --badge-info-bg: #18181b;
            --badge-info-text: #ffffff;
            --badge-info-border: #3f3f46;
        }

        /* --- LIGHT THEME (Clean Light Theme) --- */
        html[data-theme="light"] {
            --bg-dark: #f8fafc;
            --bg-card: #ffffff;
            --bg-sidebar: #ffffff;
            --bg-header: #ffffff;
            --text-main: #09090b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --nav-hover-bg: #f1f5f9;
            --nav-active-bg: #f1f5f9;
            --nav-active-border: #09090b;
            --btn-bg: #f1f5f9;
            --btn-text: #09090b;
            --btn-border: #cbd5e1;
            --btn-hover-bg: #09090b;
            --btn-hover-text: #ffffff;
            --avatar-bg: #09090b;
            --avatar-text: #ffffff;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --icon-bg: #f1f5f9;
            --icon-border: #cbd5e1;
            --badge-info-bg: #f1f5f9;
            --badge-info-text: #09090b;
            --badge-info-border: #cbd5e1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
            position: fixed;
            height: 100vh;
            z-index: 100;
            transition: background-color 0.25s ease, border-color 0.25s ease;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 2rem;
            letter-spacing: 0.02em;
        }

        .brand i {
            color: var(--text-main);
        }

        .nav-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.85rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--nav-hover-bg);
            color: var(--text-main);
            border-left: 3px solid var(--nav-active-border);
        }

        .nav-link i {
            font-size: 1.1rem;
            width: 24px;
        }

        /* Main Content Container */
        .main-wrapper {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: var(--bg-dark);
            transition: background-color 0.25s ease;
        }

        /* Header */
        header {
            height: 70px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            background: var(--bg-header);
            position: sticky;
            top: 0;
            z-index: 90;
            transition: background-color 0.25s ease, border-color 0.25s ease;
        }

        .header-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .btn-doc, .btn-theme-toggle {
            background: var(--btn-bg);
            color: var(--btn-text);
            border: 1px solid var(--btn-border);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-doc:hover, .btn-theme-toggle:hover {
            background: var(--btn-hover-bg);
            color: var(--btn-hover-text);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-main);
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--avatar-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--avatar-text);
        }

        .logout-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            font-size: 1.1rem;
            transition: color 0.2s ease;
        }

        .logout-btn:hover {
            color: #ef4444;
        }

        /* Content Body */
        .content {
            padding: 2rem;
            flex: 1;
            background-color: var(--bg-dark);
            transition: background-color 0.25s ease;
        }

        /* KPI Cards Grid */
        .grid-kpi {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card-kpi {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.2s ease, border-color 0.2s ease, background-color 0.25s ease;
        }

        .card-kpi:hover {
            transform: translateY(-3px);
            border-color: var(--btn-border);
        }

        .kpi-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text-muted);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: var(--icon-bg);
            color: var(--text-main);
            border: 1px solid var(--icon-border);
        }

        .icon-blue, .icon-purple, .icon-green, .icon-amber {
            background: var(--icon-bg);
            color: var(--text-main);
            border: 1px solid var(--icon-border);
        }

        .kpi-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--text-main);
        }

        /* Tables & Content Cards */
        .card-table {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            transition: background-color 0.25s ease, border-color 0.25s ease;
        }

        .table-head-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .table-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-main);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 0.85rem 1rem;
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
            color: var(--text-main);
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge-success { background: rgba(34, 197, 94, 0.15); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3); }
        .badge-warning { background: rgba(234, 179, 8, 0.15); color: #ca8a04; border: 1px solid rgba(234, 179, 8, 0.3); }
        .badge-danger { background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); }
        .badge-info { background: var(--badge-info-bg); color: var(--badge-info-text); border: 1px solid var(--badge-info-border); }

        /* Action Buttons */
        .btn-add {
            background: var(--btn-bg);
            color: var(--btn-text);
            border: 1px solid var(--btn-border);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-add:hover {
            background: var(--btn-hover-bg);
            color: var(--btn-hover-text);
        }

        .btn-edit {
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
            padding: 0.35rem 0.7rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s ease;
        }

        .btn-edit:hover {
            background: #3b82f6;
            color: #ffffff;
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 0.35rem 0.7rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s ease;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: #ffffff;
        }

        .btn-cancel {
            background: transparent;
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
        }

        /* Modal Overlay & Dialog */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            width: 100%;
            max-width: 520px;
            padding: 1.75rem;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
            color: var(--text-main);
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .modal-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-main);
        }

        .modal-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.2rem;
            cursor: pointer;
        }

        .modal-body {
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            padding: 0.7rem 0.9rem;
            background: var(--bg-dark);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-size: 0.9rem;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--btn-border);
        }

        .modal-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* Flash Alerts */
        .alert-flash {
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .alert-flash-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #16a34a;
        }

        .alert-flash-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="brand">
            <i class="fa-solid fa-leaf"></i>
            <span>MongoAdmin</span>
        </div>
        <ul class="nav-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.products') }}" class="nav-link {{ request()->routeIs('admin.products') ? 'active' : '' }}">
                    <i class="fa-solid fa-box"></i>
                    <span>Products</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Categories</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.coupons') }}" class="nav-link {{ request()->routeIs('admin.coupons') ? 'active' : '' }}">
                    <i class="fa-solid fa-ticket"></i>
                    <span>Coupons</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reviews') }}" class="nav-link {{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                    <i class="fa-solid fa-star"></i>
                    <span>Reviews</span>
                </a>
            </li>
            <li style="margin-top: auto;">
                <a href="/docs" target="_blank" class="nav-link">
                    <i class="fa-solid fa-book-open"></i>
                    <span>API Docs (/docs)</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content Area -->
    <div class="main-wrapper">
        <header>
            <div class="header-title">
                @yield('title', 'Admin Panel')
            </div>
            <div class="header-actions">
                <!-- Theme Toggle Button -->
                <button id="admin-theme-toggle" type="button" class="btn-theme-toggle" onclick="toggleAdminTheme()">
                    <i id="admin-theme-icon" class="fa-solid fa-moon"></i>
                    <span id="admin-theme-text">Dark Mode</span>
                </button>

                <a href="/docs" target="_blank" class="btn-doc">
                    <i class="fa-solid fa-code"></i>
                    Interactive OpenAPI Docs
                </a>
                <div class="user-profile">
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div style="font-size: 0.9rem; font-weight: 500;">
                        {{ auth()->user()->name ?? 'Admin User' }}
                    </div>
                    <form action="{{ route('admin.logout') }}" method="POST" style="margin-left: 0.5rem;">
                        @csrf
                        <button type="submit" class="logout-btn" title="Logout">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert-flash alert-flash-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-flash alert-flash-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function applyAdminTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            const icon = document.getElementById('admin-theme-icon');
            const text = document.getElementById('admin-theme-text');
            if (icon && text) {
                if (theme === 'dark') {
                    icon.className = 'fa-solid fa-moon';
                    text.textContent = 'Dark Mode';
                } else {
                    icon.className = 'fa-solid fa-sun';
                    text.textContent = 'Light Mode';
                }
            }
            localStorage.setItem('admin_theme', theme);
            localStorage.setItem('scramble_theme', theme);
        }

        function toggleAdminTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyAdminTheme(newTheme);
        }

        const savedAdminTheme = localStorage.getItem('admin_theme') || 'dark';
        applyAdminTheme(savedAdminTheme);
    </script>
</body>
</html>
