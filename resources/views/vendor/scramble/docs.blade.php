<!doctype html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="dark light">
    <title>{{ $config->get('ui.title') ?? config('app.name') . ' - API Documentation' }}</title>

    <!-- Google Fonts & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://unpkg.com/@stoplight/elements@8.4.2/web-components.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements@8.4.2/styles.min.css">

    @include('scramble::dev-tools', ['renderer' => 'elements'])

    <script>
        const originalFetch = window.fetch;
        window.fetch = (url, options) => {
            const CSRF_TOKEN_COOKIE_KEY = "XSRF-TOKEN";
            const CSRF_TOKEN_HEADER_KEY = "X-XSRF-TOKEN";
            const getCookieValue = (key) => {
                const cookie = document.cookie.split(';').find((cookie) => cookie.trim().startsWith(key));
                return cookie?.split("=")[1];
            };

            const updateFetchHeaders = (headers, headerKey, headerValue) => {
                if (headers instanceof Headers) {
                    headers.set(headerKey, headerValue);
                } else if (Array.isArray(headers)) {
                    headers.push([headerKey, headerValue]);
                } else if (headers) {
                    headers[headerKey] = headerValue;
                }
            };
            const csrfToken = getCookieValue(CSRF_TOKEN_COOKIE_KEY);
            if (csrfToken) {
                const { headers = new Headers() } = options || {};
                updateFetchHeaders(headers, CSRF_TOKEN_HEADER_KEY, decodeURIComponent(csrfToken));
                return originalFetch(url, { ...options, headers });
            }

            return originalFetch(url, options);
        };
    </script>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        html, body { height: 100%; width: 100%; overflow: hidden; }

        /* Top Header Navbar */
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

        /* --- DARK THEME OVERRIDES (Admin Black Theme) --- */
        html[data-theme="dark"] body {
            background-color: #000000;
            color: #ffffff;
        }

        html[data-theme="dark"] .scramble-nav {
            background-color: #050507;
            border-bottom: 1px solid #27272a;
        }

        html[data-theme="dark"] .scramble-brand {
            color: #ffffff;
        }

        html[data-theme="dark"] .btn-dashboard {
            background: #18181b;
            color: #ffffff;
            border: 1px solid #3f3f46;
        }

        html[data-theme="dark"] .btn-dashboard:hover {
            background: #ffffff;
            color: #000000;
        }

        html[data-theme="dark"] .btn-theme-toggle {
            background: #18181b;
            color: #ffffff;
            border: 1px solid #3f3f46;
        }

        html[data-theme="dark"] .btn-theme-toggle:hover {
            background: #27272a;
        }

        html[data-theme="dark"] {
            --color-canvas: #000000 !important;
            --color-canvas-subtle: #050507 !important;
            --color-canvas-pure: #0c0c0e !important;
            --color-canvas-accent: #18181b !important;
            --color-border: #27272a !important;
            --color-text: #ffffff !important;
            --color-text-subtle: #a1a1aa !important;
            --color-text-muted: #71717a !important;
            --color-primary: #ffffff !important;
        }

        /* Stoplight Dark Code Highlighting Fixes */
        [data-theme="dark"] .token.property { color: #80cbc4 !important; }
        [data-theme="dark"] .token.operator { color: #ff7b72 !important; }
        [data-theme="dark"] .token.number { color: #f78c6c !important; }
        [data-theme="dark"] .token.string { color: #a5d6ff !important; }
        [data-theme="dark"] .token.boolean { color: #79c0ff !important; }
        [data-theme="dark"] .token.punctuation { color: #dbdbdb !important; }

        /* --- LIGHT THEME OVERRIDES --- */
        html[data-theme="light"] body {
            background-color: #ffffff;
            color: #09090b;
        }

        html[data-theme="light"] .scramble-nav {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        html[data-theme="light"] .scramble-brand {
            color: #09090b;
        }

        html[data-theme="light"] .btn-dashboard {
            background: #09090b;
            color: #ffffff;
            border: 1px solid #09090b;
        }

        html[data-theme="light"] .btn-dashboard:hover {
            background: #27272a;
        }

        html[data-theme="light"] .btn-theme-toggle {
            background: #ffffff;
            color: #09090b;
            border: 1px solid #cbd5e1;
        }

        html[data-theme="light"] .btn-theme-toggle:hover {
            background: #f1f5f9;
        }

        html[data-theme="light"] {
            --color-canvas: #ffffff !important;
            --color-canvas-subtle: #f8fafc !important;
            --color-canvas-pure: #ffffff !important;
            --color-canvas-accent: #f1f5f9 !important;
            --color-border: #e2e8f0 !important;
            --color-text: #09090b !important;
            --color-text-subtle: #475569 !important;
            --color-text-muted: #64748b !important;
            --color-primary: #09090b !important;
        }

        /* Container sizing */
        .api-container {
            height: calc(100vh - 60px);
            width: 100%;
        }
    </style>
</head>
<body>

    <!-- Scramble Navbar Header -->
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

    <!-- Stoplight Elements API Renderer -->
    <div class="api-container">
        <elements-api
            id="docs"
            router="hash"
            layout="responsive"
            hideTryIt="false"
            hideSchemas="false"
        />
    </div>

    <script>
        (async () => {
            const docs = document.getElementById('docs');
            docs.apiDescriptionDocument = @json($spec);
        })();

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.style.colorScheme = theme;
            
            const docsElement = document.getElementById('docs');
            if (docsElement) {
                docsElement.setAttribute('theme', theme);
            }

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

        // Initialize Theme from localStorage or default to dark
        const savedTheme = localStorage.getItem('scramble_theme') || 'dark';
        applyTheme(savedTheme);
    </script>
</body>
</html>
