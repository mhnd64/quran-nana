<?php
// بدء الجلسة
session_start();

// تفريغ وتدمير الجلسة تماماً
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// التوجيه لصفحة تسجيل الدخول مع معلمة تسجيل الخروج بنجاح
header("Location: login.php?logout=1");
exit;
?>
