<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Laravel MongoDB Portal</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        .btn-theme-toggle-login {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            padding: 0.5rem 0.9rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            border-radius: 20px;
            padding: 2.5rem;
            transition: background-color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            font-size: 1.5rem;
        }

        .login-title {
            font-size: 1.6rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .login-subtitle {
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #71717a;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            border-radius: 10px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s ease, background-color 0.25s ease;
        }

        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s ease, transform 0.1s ease;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }

        .demo-credentials {
            margin-top: 1.5rem;
            padding: 1rem;
            border-radius: 10px;
            font-size: 0.8rem;
        }

        .demo-credentials code {
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            font-weight: 600;
        }

        /* --- DARK MODE STYLING --- */
        html[data-theme="dark"] body { background: #000000; color: #ffffff; }
        html[data-theme="dark"] .btn-theme-toggle-login { background: #18181b; color: #ffffff; border: 1px solid #3f3f46; }
        html[data-theme="dark"] .btn-theme-toggle-login:hover { background: #ffffff; color: #000000; }
        html[data-theme="dark"] .login-card { background: #0c0c0e; border: 1px solid #27272a; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.9); }
        html[data-theme="dark"] .brand-logo { background: #ffffff; color: #000000; box-shadow: 0 10px 25px rgba(255, 255, 255, 0.15); }
        html[data-theme="dark"] .login-title { color: #ffffff; }
        html[data-theme="dark"] .login-subtitle { color: #a1a1aa; }
        html[data-theme="dark"] .form-label { color: #ffffff; }
        html[data-theme="dark"] .form-control { background: #18181b; border: 1px solid #27272a; color: #ffffff; }
        html[data-theme="dark"] .form-control:focus { border-color: #ffffff; box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.15); }
        html[data-theme="dark"] .btn-submit { background: #ffffff; color: #000000; }
        html[data-theme="dark"] .demo-credentials { background: #18181b; border: 1px dashed #3f3f46; color: #a1a1aa; }
        html[data-theme="dark"] .demo-credentials code { background: #27272a; color: #ffffff; }

        /* --- LIGHT MODE STYLING --- */
        html[data-theme="light"] body { background: #f8fafc; color: #09090b; }
        html[data-theme="light"] .btn-theme-toggle-login { background: #ffffff; color: #09090b; border: 1px solid #cbd5e1; }
        html[data-theme="light"] .btn-theme-toggle-login:hover { background: #09090b; color: #ffffff; }
        html[data-theme="light"] .login-card { background: #ffffff; border: 1px solid #e2e8f0; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08); }
        html[data-theme="light"] .brand-logo { background: #09090b; color: #ffffff; box-shadow: 0 10px 25px rgba(9, 9, 11, 0.15); }
        html[data-theme="light"] .login-title { color: #09090b; }
        html[data-theme="light"] .login-subtitle { color: #64748b; }
        html[data-theme="light"] .form-label { color: #09090b; }
        html[data-theme="light"] .form-control { background: #f1f5f9; border: 1px solid #cbd5e1; color: #09090b; }
        html[data-theme="light"] .form-control:focus { border-color: #09090b; box-shadow: 0 0 0 3px rgba(9, 9, 11, 0.1); }
        html[data-theme="light"] .btn-submit { background: #09090b; color: #ffffff; }
        html[data-theme="light"] .demo-credentials { background: #f1f5f9; border: 1px dashed #cbd5e1; color: #64748b; }
        html[data-theme="light"] .demo-credentials code { background: #e2e8f0; color: #09090b; }
    </style>
</head>
<body>

    <!-- Theme Toggle Button -->
    <button id="admin-theme-toggle" type="button" class="btn-theme-toggle-login" onclick="toggleAdminTheme()" title="Toggle Light/Dark Theme">
        <i id="admin-theme-icon" class="fa-solid fa-moon"></i>
        <span id="admin-theme-text">Dark Mode</span>
    </button>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">
                <i class="fa-solid fa-leaf"></i>
            </div>
            <h1 class="login-title">Admin Dashboard</h1>
            <p class="login-subtitle">Laravel MVC with MongoDB Database</p>
        </div>

        @if($errors->any())
            <div class="alert-error">
                <i class="fa-solid fa-triangle-exclamation" style="margin-right: 0.4rem;"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Admin Email Address</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" name="email" class="form-control" value="admin@example.com" required placeholder="admin@example.com">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" class="form-control" value="password123" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Sign In to Dashboard
            </button>
        </form>

        <div class="demo-credentials">
            <div style="font-weight: 600; margin-bottom: 0.25rem;">Default Admin Credentials:</div>
            <div>Email: <code>admin@example.com</code></div>
            <div>Password: <code>password123</code></div>
        </div>
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
