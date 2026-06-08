<?php
require_once 'lang.php';
require_once 'koneksi.php';

// Prevent duplicate login: if already logged in, redirect to dashboard.php
if (!empty($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

$error_msg = "";
$success_msg = "";

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($username === "" || $password === "" || $confirm_password === "") {
        $error_msg = __('all_fields_required');
    } elseif ($password !== $confirm_password) {
        $error_msg = __('passwords_dont_match');
    } elseif (strlen($password) < 6) {
        $error_msg = __('password_too_short');
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM `user` WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error_msg = __('username_taken');
        } else {
            $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
            $role = 'contributor';
            $status = 'pending';
            
            $insert_stmt = mysqli_prepare($conn, "INSERT INTO `user` (username, password, role, status) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "ssss", $username, $hashed_pw, $role, $status);
            
            if (mysqli_stmt_execute($insert_stmt)) {
                $success_msg = __('reg_success');
                header("refresh:4;url=login.php");
            } else {
                $error_msg = __('reg_error');
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo get_dir(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('register_title'); ?></title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
            bottom: -150px;
            left: -150px;
            z-index: 0;
        }

        .container {
            width: 100%;
            max-width: 480px;
            padding: 24px;
            z-index: 10;
        }

        .auth-card {
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .brand-logo {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), #6366f1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
        }

        .brand-logo svg {
            width: 36px;
            height: 36px;
            fill: white;
        }

        h2 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ffffff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 24px;
        }

        .alert {
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: start;
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: var(--error-color);
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--success-color);
        }

        .form-group {
            margin-bottom: 18px;
            text-align: start;
        }

        label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        /* Support RTL/LTR for icon in input */
        [dir="rtl"] .input-wrapper svg {
            position: absolute;
            right: 16px;
            left: auto;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: var(--text-muted);
        }

        [dir="ltr"] .input-wrapper svg {
            position: absolute;
            left: 16px;
            right: auto;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: var(--text-muted);
        }

        [dir="rtl"] input {
            width: 100%;
            padding: 13px 48px 13px 16px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            color: var(--text-main);
            font-size: 15px;
            outline: none;
        }

        [dir="ltr"] input {
            width: 100%;
            padding: 13px 16px 13px 48px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            color: var(--text-main);
            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            background: rgba(15, 23, 42, 0.8);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            border: none;
            border-radius: 14px;
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .signup-link {
            margin-top: 20px;
            font-size: 13.5px;
            color: var(--text-muted);
        }

        .signup-link a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .footer-note {
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Floating Lang Switcher */
        .floating-lang {
            position: absolute;
            top: 24px;
            z-index: 1000;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            padding: 8px 16px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        [dir="rtl"] .floating-lang {
            left: 24px;
            right: auto;
        }
        
        [dir="ltr"] .floating-lang {
            right: 24px;
            left: auto;
        }

        .floating-lang a {
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
        }
    </style>
</head>
<body>

    <!-- Floating Language Switcher -->
    <div class="floating-lang">
        <a href="?lang=en" style="color: <?php echo $lang === 'en' ? 'var(--primary)' : 'var(--text-muted)'; ?>;">EN</a>
        <span style="color: var(--border-color); font-size: 12px;">|</span>
        <a href="?lang=ar" style="color: <?php echo $lang === 'ar' ? 'var(--primary)' : 'var(--text-muted)'; ?>; font-family: 'Cairo', sans-serif;">AR</a>
        <span style="color: var(--border-color); font-size: 12px;">|</span>
        <a href="?lang=id" style="color: <?php echo $lang === 'id' ? 'var(--primary)' : 'var(--text-muted)'; ?>;">ID</a>
    </div>

    <div class="container">
        <div class="auth-card">
            <!-- Brand Logo -->
            <div class="brand-logo">
                <svg viewBox="0 0 24 24">
                    <path d="M12,3L1,9L12,15L21,10.09V17H23V9L12,3M12,5.18L18.82,8.9L12,12.62L5.18,8.9L12,5.18M12,16.5C10.75,16.5 9.54,16.2 8.44,15.65L7,17C8.43,17.9 10.15,18.5 12,18.5C13.85,18.5 15.57,17.9 17,17L15.56,15.65C14.46,16.2(13.25,16.5)12,16.5M12,20C10,20 8.16,19.3 6.66,18.15L5.22,19.56C7.07,21.1 9.43,22 12,22C14.57,22 16.93,21.1 18.78,19.56L17.34,18.15C15.84,19.3 14,20 12,20Z"/>
                </svg>
            </div>

            <h2><?php echo __('create_account'); ?></h2>
            <p class="subtitle"><?php echo __('join_portal'); ?></p>

            <!-- Success Alert -->
            <?php if ($success_msg): ?>
                <div class="alert alert-success">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span><?php echo $success_msg; ?></span>
                </div>
            <?php endif; ?>

            <!-- Error Alert -->
            <?php if ($error_msg): ?>
                <div class="alert alert-error">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink:0;">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span><?php echo $error_msg; ?></span>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username"><?php echo __('username'); ?></label>
                    <div class="input-wrapper">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <input type="text" id="username" name="username" placeholder="<?php echo __('choose_username'); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password"><?php echo __('password'); ?></label>
                    <div class="input-wrapper">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input type="password" id="password" name="password" placeholder="<?php echo __('at_least_6'); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><?php echo __('confirm_password'); ?></label>
                    <div class="input-wrapper">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="<?php echo __('repeat_password'); ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit"><?php echo __('register_button'); ?></button>
            </form>

            <div class="signup-link">
                <?php echo __('already_have'); ?> <a href="login.php"><?php echo __('sign_in_here'); ?></a>
            </div>

            <p class="footer-note">&copy; 2026 <?php echo __('brand'); ?>. <?php echo __('copyright'); ?></p>
        </div>
    </div>

    <script src="script.js?v=2"></script>
</body>
</html>
