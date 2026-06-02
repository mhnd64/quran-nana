<?php
session_start();
require_once 'koneksi.php';

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'public';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest';

$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Retrieve scientific miracles and pull corresponding Quranic verse if imported
$query = "SELECT sm.*, qv.text_uthmani, qv.surah_name 
          FROM `scientific_miracles` sm
          LEFT JOIN `quran_verses` qv 
          ON sm.surah_number = qv.surah_number AND sm.ayah_number = qv.ayah_number";

if ($search_query !== '') {
    $search_param = "%" . $search_query . "%";
    $query .= " WHERE sm.title LIKE ? OR sm.description LIKE ? OR sm.scientific_proof LIKE ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sss", $search_param, $search_param, $search_param);
} else {
    $stmt = mysqli_prepare($conn, $query);
}

mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$miracles = [];
while ($row = mysqli_fetch_assoc($res)) {
    $miracles[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scientific Miracles | Quran Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    
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
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        body {
            background: radial-gradient(circle at top right, #1e293b, #0f172a);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Shared Header/Topbar LTR */
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

        .topbar-menu {
            display: flex;
            gap: 10px;
            list-style: none;
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

        /* Layout */
        .layout {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
            z-index: 10;
        }

        .panel {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }

        .section-heading {
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            text-align: left;
        }

        .section-heading h2 {
            font-size: 24px;
            font-weight: 800;
        }

        .section-heading p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .search-row {
            margin-bottom: 32px;
            max-width: 600px;
            text-align: left;
        }

        .search-wrapper {
            position: relative;
            display: flex;
            gap: 12px;
        }

        input[type="text"] {
            width: 100%;
            padding: 14px 20px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            color: var(--text-main);
            font-size: 15px;
            outline: none;
        }

        input[type="text"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .btn-search {
            padding: 14px 28px;
            background: var(--primary);
            border: none;
            border-radius: 14px;
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-search:hover {
            background: var(--primary-hover);
        }

        /* Miracles List */
        .miracles-grid {
            display: flex;
            flex-direction: column;
            gap: 30px;
            text-align: left;
        }

        .miracle-card {
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 32px;
        }

        .miracle-card:hover {
            border-color: rgba(16, 185, 129, 0.2);
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.05);
        }

        .miracle-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .miracle-header h3 {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
        }

        .miracle-ref-badge {
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid rgba(16, 185, 129, 0.2);
            padding: 6px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 700;
        }

        /* Quran Verse block inside miracle */
        .miracle-verse-box {
            background: rgba(255, 255, 255, 0.02);
            border: 1px dashed rgba(16, 185, 129, 0.3);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 24px;
            text-align: center;
        }

        .miracle-verse-text {
            font-family: 'Amiri', serif;
            font-size: 24px;
            line-height: 2.0;
            color: #fff;
            direction: rtl;
        }

        .miracle-verse-meta {
            font-size: 12.5px;
            color: var(--text-muted);
            margin-top: 10px;
            display: block;
        }

        /* Scientific breakdown split */
        .miracle-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 768px) {
            .miracle-details-grid {
                grid-template-columns: 1fr;
            }
        }

        .details-col {
            background: rgba(15, 23, 42, 0.3);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 20px;
        }

        .details-col h4 {
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .details-col h4 svg {
            width: 18px;
            height: 18px;
            color: var(--primary);
        }

        .details-col p {
            font-size: 14px;
            line-height: 1.7;
            color: var(--text-muted);
            text-align: justify;
        }

        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-muted);
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            fill: var(--text-muted);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }
    </style>
</head>
<body>

    <!-- Shared Top Navigation Bar LTR -->
    <header class="topbar">
        <div class="topbar-brand">
            <svg viewBox="0 0 24 24">
                <path d="M12,3L1,9L12,15L21,10.09V17H23V9L12,3M12,5.18L18.82,8.9L12,12.62L5.18,8.9L12,5.18M12,16.5C10.75,16.5 9.54,16.2 8.44,15.65L7,17C8.43,17.9 10.15,18.5 12,18.5C13.85,18.5 15.57,17.9 17,17L15.56,15.65C14.46,16.2 13.25,16.5 12,16.5M12,20C10,20 8.16,19.3 6.66,18.15L5.22,19.56C7.07,21.1 9.43,22 12,22C14.57,22 16.93,21.1 18.78,19.56L17.34,18.15C15.84,19.3 14,20 12,20Z"/>
            </svg>
            <h1>Quran Analytics</h1>
        </div>

        <ul class="topbar-menu">
            <?php if ($user_role !== 'public'): ?>
                <li><a href="dashboard.php"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg><span>Dashboard</span></a></li>
            <?php else: ?>
                <li><a href="index.php"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg><span>Home</span></a></li>
            <?php endif; ?>
            <li><a href="quran.php"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg><span>Holy Quran</span></a></li>
            <li><a href="miracles.php" class="active"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg><span>Scientific Miracles</span></a></li>
            <?php if ($user_role !== 'public'): ?>
                <li><a href="manage_miracles.php"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>Manage Miracles</span></a></li>
                <?php if ($user_role === 'superadmin'): ?>
                    <li><a href="manage_users.php"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg><span>Manage Users</span></a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>

        <div class="user-strip">
            <?php if ($user_role !== 'public'): ?>
                <span class="username">Welcome, <?php echo htmlspecialchars($username); ?></span>
                <span class="role-badge"><?php echo $user_role === 'superadmin' ? 'Superadmin' : 'Contributor'; ?></span>
                <a href="logout.php" class="btn-logout">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn" style="background: rgba(255, 255, 255, 0.05); border: 1px solid var(--border-color); color: #fff; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600;">Sign In</a>
                <a href="register.php" class="btn" style="background: var(--primary); color: #fff; padding: 8px 16px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 700; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); margin-left: 8px;">Become a Contributor</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Layout -->
    <main class="layout">
        <section class="panel">
            <div class="section-heading">
                <div>
                    <h2>Scientific Miracles in the Holy Quran</h2>
                    <p>Explore geological, cosmic, and terrestrial phenomena detailed in the Quran 14 centuries ago.</p>
                </div>
            </div>

            <!-- Search Field -->
            <form method="GET" class="search-row">
                <div class="search-wrapper">
                    <input type="text" name="search" placeholder="Search scientific miracles... (e.g. Iron, Seas)" value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="btn-search">Search</button>
                </div>
            </form>

            <!-- Miracles Grid -->
            <div class="miracles-grid">
                <?php if (count($miracles) > 0): ?>
                    <?php foreach ($miracles as $m): ?>
                        <div class="miracle-card">
                            <div class="miracle-header">
                                <h3><?php echo htmlspecialchars($m['title']); ?></h3>
                                <span class="miracle-ref-badge">
                                    Surah <?php echo htmlspecialchars($m['surah_name'] ?? $m['surah_number']); ?> | Verse <?php echo $m['ayah_number']; ?>
                                </span>
                            </div>

                            <!-- Live Verse Display -->
                            <div class="miracle-verse-box">
                                <?php if ($m['text_uthmani']): ?>
                                    <div class="miracle-verse-text">
                                        « <?php echo htmlspecialchars($m['text_uthmani']); ?> »
                                    </div>
                                    <span class="miracle-verse-meta">
                                        [Surah <?php echo htmlspecialchars($m['surah_name']); ?>, Verse <?php echo $m['ayah_number']; ?>]
                                    </span>
                                <?php else: ?>
                                    <div class="miracle-verse-text" style="font-size: 16px; color: var(--text-muted);">
                                        (Please click "Download Complete Quran" on the Holy Quran page to display the verse here)
                                    </div>
                                    <span class="miracle-verse-meta">
                                        [Surah: <?php echo $m['surah_number']; ?>, Verse: <?php echo $m['ayah_number']; ?>]
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Scientific detail grids -->
                            <div class="miracle-details-grid">
                                <div class="details-col">
                                    <h4>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        Quranic Meaning & Context
                                    </h4>
                                    <p><?php echo nl2br(htmlspecialchars($m['description'])); ?></p>
                                </div>

                                <div class="details-col">
                                    <h4>
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                        </svg>
                                        Modern Scientific Verification
                                    </h4>
                                    <p><?php echo nl2br(htmlspecialchars($m['scientific_proof'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24">
                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,17H13V15H11V17M11,13H13V7H11V13Z"/>
                        </svg>
                        <h3>No miracles found</h3>
                        <p>Try searching using different keywords.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>
