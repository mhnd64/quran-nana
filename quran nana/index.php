<?php
require_once 'lang.php';
require_once 'koneksi.php';

// Fetch stats for live display
$verses_count = 0;
$res_v = mysqli_query($conn, "SELECT COUNT(*) as total FROM `quran_verses`");
if ($res_v) {
    $row = mysqli_fetch_assoc($res_v);
    $verses_count = $row['total'];
}

$miracles_count = 0;
$res_m = mysqli_query($conn, "SELECT COUNT(*) as total FROM `scientific_miracles`");
if ($res_m) {
    $row = mysqli_fetch_assoc($res_m);
    $miracles_count = $row['total'];
}

$contributors_count = 0;
$res_c = mysqli_query($conn, "SELECT COUNT(*) as total FROM `user` WHERE role='contributor' AND status='active'");
if ($res_c) {
    $row = mysqli_fetch_assoc($res_c);
    $contributors_count = $row['total'];
}

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'public';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo get_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('brand'); ?> | <?php echo __('dakwah_badge'); ?></title>
    <!-- Google Fonts: Inter & Amiri for beautiful Arabic script -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Amiri:ital,wght@0,400;0,700;1,400;1,700&family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2">
    
    <style>
        :root {
            --primary: #10b981;
            --primary-hover: #059669;
            --primary-light: rgba(16, 185, 129, 0.1);
            --bg-dark: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.65);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.08);
            --emerald-glow: rgba(16, 185, 129, 0.15);
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

        /* Ambient Glow Elements */
        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
            top: -200px;
            right: -100px;
            z-index: 0;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.05) 0%, transparent 70%);
            bottom: 100px;
            left: -100px;
            z-index: 0;
            pointer-events: none;
        }

        /* Topbar Header styling */
        .topbar {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
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
            font-weight: 900;
            background: linear-gradient(135deg, #ffffff, #10b981);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .topbar-menu {
            display: flex;
            gap: 10px;
            list-style: none;
        }

        .topbar-menu a {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 14.5px;
            padding: 8px 18px;
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
            gap: 12px;
        }

        /* Language Switcher styling */
        .lang-switcher {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            padding: 6px 12px;
            border-radius: 10px;
            margin-right: 10px;
            margin-left: 10px;
        }

        .lang-link {
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            padding: 2px 6px;
            border-radius: 4px;
        }

        .lang-link.active {
            color: var(--primary);
            background: var(--primary-light);
        }

        .lang-link:hover {
            color: #fff;
        }

        /* Hero Section */
        .hero {
            max-width: 1200px;
            margin: 60px auto 40px auto;
            padding: 0 24px;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .hero-badge {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: var(--primary);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            margin-bottom: 24px;
        }

        .hero h2 {
            font-size: 48px;
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero h2 span {
            background: linear-gradient(135deg, #10b981, #3b82f6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 18px;
            color: var(--text-muted);
            max-width: 760px;
            margin: 0 auto 36px auto;
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn-primary-glow {
            background: var(--primary);
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary-glow:hover {
            background: var(--primary-hover);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.5);
        }

        .btn-secondary-outline {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-secondary-outline:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        /* Stats Section */
        .stats-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
            z-index: 10;
            position: relative;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .stat-card:hover {
            border-color: var(--primary);
            box-shadow: 0 12px 36px rgba(16, 185, 129, 0.15);
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: var(--primary-light);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .stat-icon svg {
            width: 32px;
            height: 32px;
        }

        .stat-info h3 {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-info p {
            font-size: 32px;
            font-weight: 800;
            color: #fff;
            margin-top: 4px;
        }

        /* Features/Info Sections */
        .sections-container {
            max-width: 1200px;
            margin: 60px auto 80px auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            z-index: 10;
            position: relative;
        }

        @media (max-width: 992px) {
            .sections-container {
                grid-template-columns: 1fr;
            }
        }

        .info-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: inherit;
        }

        .info-card h3 {
            font-size: 24px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .info-card h3 svg {
            width: 28px;
            height: 28px;
            color: var(--primary);
        }

        .info-card p {
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .bullet-list {
            list-style: none;
            margin-bottom: 28px;
        }

        .bullet-list li {
            font-size: 14.5px;
            color: var(--text-main);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .bullet-list li svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
            flex-shrink: 0;
        }

        .info-card .btn-card {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--primary-light);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--primary);
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14.5px;
        }

        .info-card .btn-card:hover {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--border-color);
            background: rgba(15, 23, 42, 0.8);
            padding: 40px 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 14px;
            z-index: 10;
            position: relative;
        }

        footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        footer a:hover {
            text-decoration: underline;
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
                <a href="index.php" class="active">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span><?php echo __('home'); ?></span>
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
            <?php if ($user_role !== 'public'): ?>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span><?php echo __('manage_users'); ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>

        <!-- Language Selector -->
        <div class="lang-switcher">
            <a href="?lang=en" class="lang-link <?php echo $lang === 'en' ? 'active' : ''; ?>">EN</a>
            <span style="color: var(--border-color); font-size: 12px;">|</span>
            <a href="?lang=ar" class="lang-link <?php echo $lang === 'ar' ? 'active' : ''; ?>">AR</a>
            <span style="color: var(--border-color); font-size: 12px;">|</span>
            <a href="?lang=id" class="lang-link <?php echo $lang === 'id' ? 'active' : ''; ?>">ID</a>
        </div>

        <div class="user-strip">
            <?php if ($user_role !== 'public'): ?>
                <span class="username" style="font-weight: 700;"><?php echo __('welcome'); ?>, <?php echo htmlspecialchars($username); ?></span>
                <span class="role-badge" style="background: var(--primary-light); color: var(--primary); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; text-transform: uppercase;"><?php echo $user_role === 'superadmin' ? 'Superadmin' : 'Contributor'; ?></span>
                <a href="dashboard.php" class="btn" style="background: var(--primary-light); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--primary); padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 700;"><?php echo __('dashboard'); ?></a>
                <a href="logout.php" class="btn-logout"><?php echo __('logout'); ?></a>
            <?php else: ?>
                <a href="login.php" class="btn" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: #fff; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600;"><?php echo __('sign_in'); ?></a>
                <a href="register.php" class="btn" style="background: var(--primary); color: #fff; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 700; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); margin-left: 8px;"><?php echo __('become_contributor'); ?></a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Hero Area -->
    <section class="hero">
        <span class="hero-badge"><?php echo __('dakwah_badge'); ?></span>
        <h2><?php echo __('hero_title'); ?> <br><span><?php echo __('hero_span'); ?></span></h2>
        <p><?php echo __('hero_desc'); ?></p>
        <div class="hero-actions">
            <a href="miracles.php" class="btn-primary-glow">
                <span><?php echo __('btn_explore'); ?></span>
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
            <a href="quran.php" class="btn-secondary-outline">
                <span><?php echo __('btn_browse_quran'); ?></span>
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </a>
        </div>
    </section>

    <!-- Live Database Stats -->
    <section class="stats-wrapper">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3><?php echo __('stat_verses'); ?></h3>
                    <p><?php echo number_format($verses_count); ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3><?php echo __('stat_miracles'); ?></h3>
                    <p><?php echo $miracles_count; ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3><?php echo __('stat_contributors'); ?></h3>
                    <p><?php echo $contributors_count; ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Information Splitting Blocks -->
    <main class="sections-container">
        <!-- Card 1: For the Public -->
        <article class="info-card" style="border-left: 4px solid var(--primary);">
            <h3>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span><?php echo __('card_seeker_title'); ?></span>
            </h3>
            <p><?php echo __('card_seeker_desc'); ?></p>
            <ul class="bullet-list">
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><?php echo __('bullet_seeker_1'); ?></span>
                </li>
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><?php echo __('bullet_seeker_2'); ?></span>
                </li>
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><?php echo __('bullet_seeker_3'); ?></span>
                </li>
            </ul>
            <a href="miracles.php" class="btn-card"><?php echo __('btn_start_browsing'); ?></a>
        </article>

        <!-- Card 2: For Contributors -->
        <article class="info-card" style="border-left: 4px solid #3b82f6;">
            <h3>
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span><?php echo __('card_contributor_title'); ?></span>
            </h3>
            <p><?php echo __('card_contributor_desc'); ?></p>
            <ul class="bullet-list">
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><?php echo __('bullet_contributor_1'); ?></span>
                </li>
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><?php echo __('bullet_contributor_2'); ?></span>
                </li>
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span><?php echo __('bullet_contributor_3'); ?></span>
                </li>
            </ul>
            <a href="register.php" class="btn-card" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2);"><?php echo __('btn_register_account'); ?></a>
        </article>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo __('footer_copyright'); ?></p>
        <p style="margin-top: 10px; font-size: 13px;"><?php echo __('footer_tag'); ?> <a href="login.php"><?php echo __('superadmin_console'); ?></a></p>
    </footer>

    <script src="script.js?v=2"></script>
</body>
</html>
