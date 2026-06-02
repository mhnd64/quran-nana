<?php
session_start();
require_once 'koneksi.php';

// 1. تأمين وتنظيف المدخلات
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($username === '' || $password === '') {
    header("Location: login.php?error=1");
    exit;
}

// 2. استعلام قاعدة البيانات الآمن (Prepared Statements) لحظر هجمات SQL Injection
$query = "SELECT username, password, role FROM user WHERE username = ? LIMIT 1";

if ($stmt = mysqli_prepare($conn, $query)) {
    // ربط المعاملات (Binding parameters)
    mysqli_stmt_bind_param($stmt, "s", $username);
    
    // تنفيذ وجلب النتائج
    mysqli_stmt_execute($stmt);
    
    // ربط النتيجة بمتغيرات داخلية تشمل صلاحية المستخدم (role)
    mysqli_stmt_bind_result($stmt, $db_username, $db_password, $db_role);
    
    // جلب البيانات
    if (mysqli_stmt_fetch($stmt)) {
        // إغلاق الاستعلام المفتوح قبل اتخاذ الإجراءات لتجنب الأخطاء
        mysqli_stmt_close($stmt);

        // 3. مطابقة كلمة المرور المتطابقة أمنياً (سواء عبر النص الصريح أو الدوال التشفيرية)
        $password_matches = false;
        
        if ($password === $db_password) {
            // مطابقة النص الصريح
            $password_matches = true;
        } elseif (password_verify($password, $db_password)) {
            // مطابقة عبر دالة التشفير password_verify
            $password_matches = true;
        }

        if ($password_matches) {
            // إنشاء الجلسة وتحويل المستخدم للوحة التحكم وتخزين الصلاحيات
            $_SESSION['username'] = $db_username;
            $_SESSION['role']     = $db_role;
            
            header("Location: dashboard.php");
            exit;
        }
    } else {
        mysqli_stmt_close($stmt);
    }
}

// في حال الفشل، إعادته لصفحة الـ login مع معامل الخطأ
header("Location: login.php?error=1");
exit;
?>
