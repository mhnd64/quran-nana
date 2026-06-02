<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "quran_nana";

// الاتصال بخادم قاعدة البيانات MySQL
$conn = mysqli_connect($host, $user, $pass);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// إنشاء قاعدة البيانات إذا لم تكن موجودة مسبقاً وتعيين ترميز اللغة العربية UTF-8
mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
mysqli_select_db($conn, $db);

// 1. إنشاء جدول المستخدمين 'user' مع دعم الصلاحيات وحالة الحساب
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `user` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(20) NOT NULL DEFAULT 'contributor',
    `status` VARCHAR(20) NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// التحقق من وجود عمود الحالة status وتحديث الحقول للبيانات القديمة إن وجدت
$check_status_col = mysqli_query($conn, "SHOW COLUMNS FROM `user` LIKE 'status'");
if (mysqli_num_rows($check_status_col) === 0) {
    mysqli_query($conn, "ALTER TABLE `user` ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'pending' AFTER `role`");
    mysqli_query($conn, "UPDATE `user` SET `status` = 'active'");
}

// تحديث الأدوار القديمة لتتوافق مع المسميات الجديدة
mysqli_query($conn, "UPDATE `user` SET `role` = 'superadmin' WHERE `role` = 'admin'");
mysqli_query($conn, "UPDATE `user` SET `role` = 'contributor' WHERE `role` = 'student'");

// 2. إنشاء جدول الإعجاز العلمي 'scientific_miracles'
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `scientific_miracles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `surah_number` INT NOT NULL,
    `ayah_number` INT NOT NULL,
    `scientific_proof` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

// 3. إنشاء جدول الآيات القرآنية 'quran_verses' مع ضمان عدم تكرار الآية (فهرس فريد)
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `quran_verses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `surah_number` INT NOT NULL,
    `surah_name` VARCHAR(100) NOT NULL,
    `ayah_number` INT NOT NULL,
    `text_uthmani` TEXT NOT NULL,
    `text_simple` TEXT NOT NULL,
    UNIQUE KEY `surah_ayah` (`surah_number`, `ayah_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

// إدخال الحسابات التجريبية الافتراضية إذا كان الجدول فارغاً (تلقيم البيانات)
$check_users = mysqli_query($conn, "SELECT * FROM `user` LIMIT 1");
if (mysqli_num_rows($check_users) === 0) {
    // حساب المدير العام (صلاحيات كاملة للتحكم في الحسابات والبيانات)
    $admin_pw = password_hash('admin123', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO `user` (username, password, role, status) VALUES ('admin', '$admin_pw', 'superadmin', 'active')");

    // حساب المساهم التجريبي (إضافة وتعديل بيانات الإعجاز بعد التفعيل)
    $student_pw = password_hash('demo123', PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO `user` (username, password, role, status) VALUES ('demo', '$student_pw', 'contributor', 'active')");
}

// إدخال مواضيع الإعجاز العلمي الافتراضية إذا كان الجدول فارغاً
$check_miracles = mysqli_query($conn, "SELECT * FROM `scientific_miracles` LIMIT 1");
if (mysqli_num_rows($check_miracles) === 0) {
    mysqli_query($conn, "INSERT INTO `scientific_miracles` (title, description, surah_number, ayah_number, scientific_proof) VALUES 
    (
        'Iron Sent Down From Heaven', 
        'The Quran states that iron was \"sent down\" rather than created locally on Earth: \"And We sent down iron, wherein is great might and benefits for mankind.\" (Surah Al-Hadid, 57:25).', 
        57, 
        25, 
        'Modern astrophysics has confirmed that the extreme temperature and pressure required to produce iron atoms do not exist inside Earth or even within the solar system. Iron can only be synthesized in massive stars undergoing supernova explosions. Therefore, all iron on Earth was physically sent down from outer space via meteorites billions of years ago.'
    ),
    (
        'The Barrier Between Two Seas', 
        'The Quran describes the meeting of two bodies of water, salt and fresh, and the existence of an invisible barrier preventing them from blending completely: \"He released the two seas, meeting [side by side]; Between them is a barrier [so] neither of them transgresses.\" (Surah Ar-Rahman, 55:19-20).', 
        55, 
        19, 
        'Oceanographic discoveries show that where different bodies of water meet (like the Atlantic and the Mediterranean), there is a water barrier with distinct differences in salinity, temperature, and density. This gradient forms a physical barrier that prevents the rapid mixing of the two seas, preserving their unique ecological characteristics.'
    ),
    (
        'The Expanding Universe', 
        'The Quran refers to the creation and expansion of the cosmos: \"And the heaven We constructed with strength, and indeed, We are [its] expander.\" (Surah Adh-Dhariyat, 51:47).', 
        51, 
        84, 
        'In 1929, Edwin Hubble observed that galaxies are moving away from each other at speeds proportional to their distance. This established the foundational theory of the expanding universe. The Arabic term \"Moosi\'oon\" in the verse translates to expander, precisely describing this ongoing cosmological expansion discovered only in the 20th century.'
    )");
}

// إدخال وتلقيم الآيات القرآنية الافتراضية المرتبطة بالإعجاز وسورة الفاتحة
$check_verses = mysqli_query($conn, "SELECT * FROM `quran_verses` LIMIT 1");
if (mysqli_num_rows($check_verses) === 0) {
    mysqli_query($conn, "INSERT IGNORE INTO `quran_verses` (surah_number, surah_name, ayah_number, text_uthmani, text_simple) VALUES 
    (1, 'Al-Fatiha', 1, 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ', 'بسم الله الرحمن الرحيم'),
    (1, 'Al-Fatiha', 2, 'الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ', 'الحمد لله رب العالمين'),
    (1, 'Al-Fatiha', 3, 'الرَّحْمَٰنِ الرَّحِيمِ', 'الرحمن الرحيم'),
    (1, 'Al-Fatiha', 4, 'مَالِكِ يَوْمِ الدِّينِ', 'مالك يوم الدين'),
    (1, 'Al-Fatiha', 5, 'إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ', 'إياك نعبد وإياك نستعين'),
    (1, 'Al-Fatiha', 6, 'اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ', 'اهدنا الصراط المستقيم'),
    (1, 'Al-Fatiha', 7, 'صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ', 'صراط الذين أنعمت عليهم غير المغضوب عليهم ولا الضالين'),
    (57, 'Al-Hadid', 25, 'لَقَدْ أَرْسَلْنَا رُسُلَنَا بِالْبَيِّنَاتِ وَأَنْزَلْنَا مَعَهُمُ الْكِتَابَ وَالْمِيزَانَ لِيَقُومَ النَّاسُ بِالْقِسْطِ ۖ وَأَنْزَلْنَا الْحَدِيدَ فِيهِ بَأْسٌ شَدِيدٌ وَمَنَافِعُ لِلنَّاسِ وَلِيَعْلَمَ اللَّهُ مَنْ يَنْصُرُهُ وَرُسُلَهُ بِالْغَيْبِ ۚ إِنَّ اللَّهَ قَوِيٌّ عَزِيزٌ', 'لقد أرسلنا رسلنا بالبينات وأنزلنا معهم الكتاب والميزان ليقوم الناس بالقسط وأنزلنا الحديد فيه بأس شديد ومنافع للناس وليعلم الله من ينصره ورسله بالغيب إن الله قوي عزيز'),
    (55, 'Ar-Rahman', 19, 'مَرَجَ الْبَحْرَيْنِ يَلْتَقِيَانِ', 'مرج البحرين يلتقيان'),
    (55, 'Ar-Rahman', 20, 'بَيْنَهُمَا بَرْزَخٌ لَا يَبْغِيَانِ', 'بينهما برزخ لا يبغيان'),
    (51, 'Adh-Dhariyat', 47, 'وَالسَّمَاءَ بَنَيْنَاهَا بِأَيْدٍ وَإِنَّا لَمُوسِعُونَ', 'والسماء بنيناها بأيد وإنا لموسعون')");
}
?>
