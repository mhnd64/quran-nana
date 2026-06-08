<?php
// Secure session check gateway
require_once 'auth_check.php';
// Translation system core
require_once 'lang.php';
// Database connector
require_once 'koneksi.php';

// Fetch scientific miracles statistics from database
$miracles_count = 0;
$res = mysqli_query($conn, "SELECT COUNT(*) as total FROM `scientific_miracles`");
if ($res) {
    $row = mysqli_fetch_assoc($res);
    $miracles_count = $row['total'];
}

// Fetch total Quranic verses currently loaded
$verses_count = 0;
$res_v = mysqli_query($conn, "SELECT COUNT(*) as total FROM `quran_verses`");
if ($res_v) {
    $row_v = mysqli_fetch_assoc($res_v);
    $verses_count = $row_v['total'];
}

// Fetch user profile from session context
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'contributor';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo get_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('dash_title'); ?></title>
    <!-- Google Fonts: Inter & Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2">
    
    <style>
        :root {
            --primary: #10b981;
            --primary-hover: #059669;
            --primary-light: rgba(16, 185, 129, 0.1);
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.7);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.08);
            --error-color: #ef4444;
            --success-color: #10b981;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: <?php echo $lang === 'ar' ? "'Cairo', sans-serif" : "'Inter', sans-serif"; ?>;
            transition: all 0.3s ease;
        }

        body {
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Decorative background elements */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
            top: -100px;
            left: -100px;
            z-index: 0;
        }

        /* Shared Header/Topbar */
        .topbar {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-brand svg {
            width: 36px;
            height: 36px;
            fill: var(--primary);
        }

        .topbar-brand h1 {
            font-size: 22px;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Topbar Navigation Menu */
        .topbar-menu {
            display: flex;
            gap: 10px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .topbar-menu a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-menu a:hover, .topbar-menu a.active {
            color: var(--text-main);
            background: var(--primary-light);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .topbar-menu a svg {
            width: 18px;
            height: 18px;
        }

        .user-strip {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .username {
            font-weight: 700;
            color: var(--text-main);
        }

        .role-badge {
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid rgba(16, 185, 129, 0.3);
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--error-color);
            padding: 8px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .btn-logout:hover {
            background: var(--error-color);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        /* Layout Grid */
        .layout {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 3fr 1fr;
            gap: 32px;
            z-index: 10;
            position: relative;
        }

        @media (max-width: 992px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .section-heading {
            margin-bottom: 32px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
            text-align: start;
        }

        .section-heading h2 {
            font-size: 24px;
            font-weight: 800;
        }

        .section-heading p {
            color: var(--text-muted);
            font-size: 15px;
            margin-top: 6px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 36px;
        }

        .stat-card {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
            text-align: start;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.1);
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon svg {
            width: 28px;
            height: 28px;
        }

        .stat-info h3 {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .stat-info p {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
            margin-top: 4px;
        }

        /* Features Dashboard List */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 24px;
            text-align: start;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .feature-card:hover {
            border-color: rgba(16, 185, 129, 0.3);
            background: rgba(255, 255, 255, 0.04);
        }

        .feature-card h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .feature-card p {
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-muted);
            flex-grow: 1;
        }

        .feature-card .btn {
            background: var(--primary);
            color: white;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
        }

        .feature-card .btn:hover {
            background: var(--primary-hover);
        }

        /* Sidebar info */
        .sidebar-card {
            display: flex;
            flex-direction: column;
            gap: 24px;
            text-align: start;
        }

        .sidebar-box {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 24px;
        }

        .sidebar-box h3 {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 16px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }

        .sidebar-box ul {
            list-style: none;
        }

        .sidebar-box ul li {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-box ul li svg {
            width: 18px;
            height: 18px;
            color: var(--primary);
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <!-- Shared Top Navigation Bar -->
    <header class="topbar">
        <div class="topbar-brand">
            <svg viewBox="0 0 24 24">
                <path d="M12,3L1,9L12,15L21,10.09V17H23V9L12,3M12,5.18L18.82,8.9L12,12.62L5.18,8.9L12,5.18M12,16.5C10.75,16.5 9.54,16.2 8.44,15.65L7,17C8.43,17.9 10.15,18.5 12,18.5C13.85,18.5 15.57,17.9 17,17L15.56,15.65C14.46,16.2 13.25,16.5 12,16.5M12,20C10,20 8.16,19.3 6.66,18.15L5.22,19.56C7.07,21.1 9.43,22 12,22C14.57,22 16.93,21.1 18.78,19.56L17.34,18.15C15.84,19.3 14,20 12,20Z"/>
            </svg>
            <h1><?php echo __('brand'); ?></h1>
        </div>

        <ul class="topbar-menu">
            <li>
                <a href="dashboard.php" class="active">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span><?php echo __('dashboard'); ?></span>
                </a>
            </li>
            <li>
                <a href="quran.php">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span><?php echo __('quran'); ?></span>
                </a>
            </li>
            <li>
                <a href="miracles.php">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <span><?php echo __('miracles'); ?></span>
                </a>
            </li>
            <li>
                <a href="manage_miracles.php">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span><?php echo __('manage_miracles'); ?></span>
                </a>
            </li>
            <?php if ($user_role === 'superadmin'): ?>
            <li>
                <a href="manage_users.php">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span><?php echo __('manage_users'); ?></span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="user-strip">
            <!-- Language Switcher in topbar -->
            <div class="lang-switcher" style="display: flex; gap: 8px; align-items: center; background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 8px; border: 1px solid var(--border-color);">
                <a href="?lang=en" style="color: <?php echo $lang === 'en' ? 'var(--primary)' : 'var(--text-muted)'; ?>; text-decoration: none; font-weight: 700; font-size: 13px;">EN</a>
                <span style="color: var(--border-color); font-size: 11px;">|</span>
                <a href="?lang=ar" style="color: <?php echo $lang === 'ar' ? 'var(--primary)' : 'var(--text-muted)'; ?>; text-decoration: none; font-weight: 700; font-size: 13px; font-family: 'Cairo', sans-serif;">AR</a>
                <span style="color: var(--border-color); font-size: 11px;">|</span>
                <a href="?lang=id" style="color: <?php echo $lang === 'id' ? 'var(--primary)' : 'var(--text-muted)'; ?>; text-decoration: none; font-weight: 700; font-size: 13px;">ID</a>
            </div>

            <span class="username"><?php echo __('welcome'); ?>, <?php echo htmlspecialchars($username); ?></span>
            <span class="role-badge"><?php echo $user_role === 'superadmin' ? __('role_superadmin') : __('role_contributor'); ?></span>
            <a href="logout.php" class="btn-logout"><?php echo __('logout'); ?></a>
        </div>
    </header>

    <!-- Main Content Layout -->
    <main class="layout">
        <section class="panel">
            <div class="section-heading">
                <h2><?php echo __('dash_heading'); ?></h2>
                <p><?php echo __('dash_subheading'); ?></p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo __('scientific_pointers'); ?></h3>
                        <p><?php echo $miracles_count; ?> <?php echo __('topics'); ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo __('imported_verses'); ?></h3>
                        <p><?php echo number_format($verses_count); ?> <?php echo __('verses'); ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo __('session_privilege'); ?></h3>
                        <p><?php echo $user_role === 'superadmin' ? __('superadmin_full') : __('contributor_add_edit'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Features Navigation Section -->
            <h2><?php echo __('available_modules'); ?></h2>
            <div class="features-grid">
                <div class="feature-card">
                    <h3><?php echo __('sql_quran_browser'); ?></h3>
                    <p><?php echo __('quran_browser_desc'); ?></p>
                    <a href="quran.php" class="btn"><?php echo __('browse_verses'); ?></a>
                </div>

                <div class="feature-card">
                    <h3><?php echo __('miracles'); ?></h3>
                    <p><?php echo __('miracles_dir_desc'); ?></p>
                    <a href="miracles.php" class="btn"><?php echo __('explore_dir'); ?></a>
                </div>

                <div class="feature-card">
                    <h3><?php echo __('manage_miracles'); ?></h3>
                    <p><?php echo __('manage_miracles_desc'); ?></p>
                    <a href="manage_miracles.php" class="btn" style="background: linear-gradient(135deg, #10b981, #059669)"><?php echo __('manage_miracles'); ?></a>
                </div>

                <?php if ($user_role === 'superadmin'): ?>
                <div class="feature-card">
                    <h3><?php echo __('manage_users'); ?></h3>
                    <p><?php echo __('superadmin_control_desc'); ?></p>
                    <a href="manage_users.php" class="btn" style="background: linear-gradient(135deg, #f59e0b, #d97706)"><?php echo __('manage_users'); ?></a>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Sidebar / Meta Details -->
        <aside class="sidebar-card">
            <div class="sidebar-box">
                <h3><?php echo __('about_project'); ?></h3>
                <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.7;">
                    <?php echo __('about_desc'); ?>
                </p>
            </div>

            <div class="sidebar-box">
                <h3><?php echo __('platform_highlights'); ?></h3>
                <ul>
                    <li>
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><?php echo __('protected_sessions'); ?></span>
                    </li>
                    <li>
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><?php echo __('prepared_statements'); ?></span>
                    </li>
                    <li>
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span><?php echo __('sanitized_inputs'); ?></span>
                    </li>
                </ul>
            </div>
        </aside>
    </main>

    <script src="script.js?v=2"></script>
</body>
</html>
