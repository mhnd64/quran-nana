<?php
require_once 'auth_check.php';
require_once 'koneksi.php';

// 1. Strict access control (only superadministrators)
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: dashboard.php");
    exit;
}

$user_role = $_SESSION['role'];
$username = $_SESSION['username'];

$message = "";
$message_type = "";

// 2. Handle Add User (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    if ($action === 'add') {
        $new_username = trim($_POST['username']);
        $new_password = $_POST['password'];
        $new_role = $_POST['role'];
        
        if ($new_username !== '' && $new_password !== '') {
            // Check if username already exists
            $check_stmt = mysqli_prepare($conn, "SELECT id FROM `user` WHERE username = ? LIMIT 1");
            mysqli_stmt_bind_param($check_stmt, "s", $new_username);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            
            if (mysqli_stmt_num_rows($check_stmt) > 0) {
                $message = "Username is already taken. Please choose another one.";
                $message_type = "error";
            } else {
                // Insert new user with hashed password and active status
                $hashed_pw = password_hash($new_password, PASSWORD_DEFAULT);
                $insert_stmt = mysqli_prepare($conn, "INSERT INTO `user` (username, password, role, status) VALUES (?, ?, ?, 'active')");
                mysqli_stmt_bind_param($insert_stmt, "sss", $new_username, $hashed_pw, $new_role);
                
                if (mysqli_stmt_execute($insert_stmt)) {
                    $message = "User successfully registered!";
                    $message_type = "success";
                } else {
                    $message = "Failed to insert user into database.";
                    $message_type = "error";
                }
                mysqli_stmt_close($insert_stmt);
            }
            mysqli_stmt_close($check_stmt);
        } else {
            $message = "Please fill in all fields.";
            $message_type = "error";
        }
    }
}

// 3. Handle Delete User (GET)
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    
    if ($delete_id > 0) {
        // Retrieve username to prevent self-deletion
        $user_query = mysqli_query($conn, "SELECT username FROM `user` WHERE id = $delete_id LIMIT 1");
        $user_data = mysqli_fetch_assoc($user_query);
        
        if ($user_data) {
            if ($user_data['username'] === $_SESSION['username']) {
                $message = "Security Alert: You cannot delete your own logged-in admin account!";
                $message_type = "error";
            } else {
                $stmt = mysqli_prepare($conn, "DELETE FROM `user` WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "i", $delete_id);
                if (mysqli_stmt_execute($stmt)) {
                    $message = "User successfully deleted.";
                    $message_type = "success";
                } else {
                    $message = "An error occurred while deleting user.";
                    $message_type = "error";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// 3.5 Handle Contributor Approval (GET)
if (isset($_GET['approve'])) {
    $approve_id = (int)$_GET['approve'];
    if ($approve_id > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE `user` SET `status` = 'active' WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $approve_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Contributor account approved and activated successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to approve contributor.";
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    }
}

// 3.6 Handle Contributor Suspension (GET)
if (isset($_GET['suspend'])) {
    $suspend_id = (int)$_GET['suspend'];
    if ($suspend_id > 0) {
        // Prevent self-suspension
        $user_query = mysqli_query($conn, "SELECT username FROM `user` WHERE id = $suspend_id LIMIT 1");
        $user_data = mysqli_fetch_assoc($user_query);
        if ($user_data && $user_data['username'] === $_SESSION['username']) {
            $message = "Security Alert: You cannot suspend your own logged-in admin account!";
            $message_type = "error";
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE `user` SET `status` = 'suspended' WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $suspend_id);
            if (mysqli_stmt_execute($stmt)) {
                $message = "User account successfully suspended.";
                $message_type = "success";
            } else {
                $message = "Failed to suspend account.";
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// 4. Retrieve all users for the list
$users_res = mysqli_query($conn, "SELECT id, username, role, status, created_at FROM `user` ORDER BY id DESC");
$users = [];
while ($row = mysqli_fetch_assoc($users_res)) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Quran Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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

        /* Layout Grid */
        .layout {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 2fr 3fr;
            gap: 32px;
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
            padding: 32px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .section-heading {
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 16px;
            text-align: left;
        }

        .section-heading h2 {
            font-size: 22px;
            font-weight: 800;
        }

        .section-heading p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 600;
            text-align: left;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--success-color);
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--error-color);
        }

        /* Forms styling LTR */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 15px;
            outline: none;
        }

        input[type="text"]:focus, input[type="password"]:focus, select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .btn-submit {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
        }

        /* Database Table */
        .table-wrap {
            overflow-x: auto;
            margin-top: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: rgba(15, 23, 42, 0.6);
            padding: 14px 16px;
            color: var(--text-muted);
            font-weight: 700;
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .role-tag {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .role-superadmin {
            background: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .role-contributor {
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-tag {
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-suspended {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .action-links {
            display: flex;
            gap: 12px;
        }

        .action-link-delete {
            color: var(--error-color);
            text-decoration: none;
            font-weight: 700;
        }

        .action-link-delete:hover {
            text-decoration: underline;
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
            <li><a href="dashboard.php"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg><span>Dashboard</span></a></li>
            <li><a href="quran.php"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg><span>Holy Quran</span></a></li>
            <li><a href="miracles.php"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg><span>Scientific Miracles</span></a></li>
            <li><a href="manage_miracles.php"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>Manage Miracles</span></a></li>
            <?php if ($user_role === 'superadmin'): ?>
            <li><a href="manage_users.php" class="active"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg><span>Manage Users</span></a></li>
            <?php endif; ?>
        </ul>

        <div class="user-strip">
            <span class="username">Welcome, <?php echo htmlspecialchars($username); ?></span>
            <span class="role-badge"><?php echo $user_role === 'superadmin' ? 'Superadmin' : 'Contributor'; ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>

    <!-- Main Layout Grid -->
    <main class="layout">
        
        <!-- Column 1: Add User Form -->
        <section class="panel">
            <div class="section-heading">
                <h2>Add New User</h2>
                <p>Register a new system user with standard contributor or administrative privileges.</p>
            </div>

            <!-- Alerts -->
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="manage_users.php">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="e.g. fatimah99">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password (min. 6 characters)">
                </div>

                <div class="form-group">
                    <label for="role">User Role</label>
                    <select id="role" name="role">
                        <option value="contributor">Contributor (Add & Edit Miracles)</option>
                        <option value="superadmin">Superadmin (Full Access)</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">Register User Account</button>
            </form>
        </section>

        <!-- Column 2: Existing Users List -->
        <section class="panel">
            <div class="section-heading">
                <h2>System User Directory</h2>
                <p>Browse through registered users, verify contributors, manage roles, and delete accounts.</p>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Access Level</th>
                            <th>Status</th>
                            <th>Registration Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($u['username']); ?></td>
                                    <td>
                                        <span class="role-tag role-<?php echo htmlspecialchars($u['role']); ?>">
                                            <?php echo htmlspecialchars($u['role'] === 'superadmin' ? 'Superadmin' : 'Contributor'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-tag status-<?php echo htmlspecialchars($u['status']); ?>">
                                            <?php echo htmlspecialchars($u['status'] ?? 'pending'); ?>
                                        </span>
                                    </td>
                                    <td style="color: var(--text-muted); font-size: 13px;">
                                        <?php echo date("M d, Y", strtotime($u['created_at'])); ?>
                                    </td>
                                    <td>
                                        <div class="action-links">
                                            <?php if ($u['username'] === $_SESSION['username']): ?>
                                                <span style="color: var(--text-muted); font-size: 12px; font-style: italic;">Current Session</span>
                                            <?php else: ?>
                                                <?php if ($u['role'] === 'contributor'): ?>
                                                    <?php if ($u['status'] !== 'active'): ?>
                                                        <a href="manage_users.php?approve=<?php echo $u['id']; ?>" style="color: #10b981; font-weight: 700; text-decoration: none;">Approve</a>
                                                    <?php else: ?>
                                                        <a href="manage_users.php?suspend=<?php echo $u['id']; ?>" style="color: #f59e0b; font-weight: 700; text-decoration: none;">Suspend</a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <a href="manage_users.php?delete=<?php echo $u['id']; ?>" class="action-link-delete" onclick="return confirm('Are you sure you want to delete this user permanently?')">Delete</a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                              <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 30px;">
                                    No records found in database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <script src="script.js"></script>
</body>
</html>
