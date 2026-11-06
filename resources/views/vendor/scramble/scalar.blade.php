<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ $config->get('ui.title') ?? config('app.name') . ' - API Docs' }}</title>
    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @include('scramble::dev-tools', ['renderer' => 'scalar'])

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        html, body { height: 100%; width: 100%; overflow: hidden; }

        .scramble-nav {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: background-color 0.25s ease, border-color 0.25s ease;
        }

        .scramble-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.15rem;
            font-weight: 800;
            text-decoration: none;
        }

        .scramble-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-theme-toggle, .btn-dashboard {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        html[data-theme="dark"] body { background-color: #000000; color: #ffffff; }
        html[data-theme="dark"] .scramble-nav { background-color: #050507; border-bottom: 1px solid #27272a; }
        html[data-theme="dark"] .scramble-brand { color: #ffffff; }
        html[data-theme="dark"] .btn-dashboard { background: #18181b; color: #ffffff; border: 1px solid #3f3f46; }
        html[data-theme="dark"] .btn-dashboard:hover { background: #ffffff; color: #000000; }
        html[data-theme="dark"] .btn-theme-toggle { background: #18181b; color: #ffffff; border: 1px solid #3f3f46; }

        html[data-theme="light"] body { background-color: #ffffff; color: #09090b; }
        html[data-theme="light"] .scramble-nav { background-color: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        html[data-theme="light"] .scramble-brand { color: #09090b; }
        html[data-theme="light"] .btn-dashboard { background: #09090b; color: #ffffff; border: 1px solid #09090b; }
        html[data-theme="light"] .btn-theme-toggle { background: #ffffff; color: #09090b; border: 1px solid #cbd5e1; }

        .api-container {
            height: calc(100vh - 60px);
            width: 100%;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <nav class="scramble-nav">
        <a href="{{ route('admin.dashboard') }}" class="scramble-brand">
            <i class="fa-solid fa-leaf"></i>
            <span>MongoAdmin API Docs</span>
        </a>
        <div class="scramble-actions">
            <a href="{{ route('admin.dashboard') }}" class="btn-dashboard">
                <i class="fa-solid fa-chart-pie"></i>
                Admin Dashboard
            </a>
            <button id="theme-toggle-btn" type="button" class="btn-theme-toggle" onclick="toggleTheme()">
                <i id="theme-icon" class="fa-solid fa-moon"></i>
                <span id="theme-text">Dark Mode</span>
            </button>
        </div>
    </nav>
    
    <div id="app" class="api-container"></div>
    
    <script src="{{ $config->renderer()->get('cdn', 'https://cdn.jsdelivr.net/npm/@scalar/api-reference') }}"></script>
    <script>
        const CSRF_TOKEN_COOKIE_KEY = "XSRF-TOKEN";
        const CSRF_TOKEN_HEADER_KEY = "X-XSRF-TOKEN";
        const getCookieValue = (key) => {
            const cookie = document.cookie.split(';').find((cookie) => cookie.trim().startsWith(key));
            return cookie?.split("=")[1];
        };

        const reference = Scalar.createApiReference('#app', {
            content: @json($spec),
            ...@json($config->renderer()->all(except: ['cdn', 'credentials'])),
            onBeforeRequest: ({ requestBuilder }) => {
                requestBuilder.headers.set(CSRF_TOKEN_HEADER_KEY, decodeURIComponent(getCookieValue(CSRF_TOKEN_COOKIE_KEY)))
            },
            customFetch: (input, init) => {
                return window.fetch(input, { ...init, credentials: @json($config->renderer()->get('credentials', 'include')) })
            }
        });

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            const icon = document.getElementById('theme-icon');
            const text = document.getElementById('theme-text');
            if (theme === 'dark') {
                icon.className = 'fa-solid fa-moon';
                text.textContent = 'Dark Mode';
            } else {
                icon.className = 'fa-solid fa-sun';
                text.textContent = 'Light Mode';
            }
            localStorage.setItem('scramble_theme', theme);
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        }

        const savedTheme = localStorage.getItem('scramble_theme') || 'dark';
        applyTheme(savedTheme);
    </script>
</body>
</html>
