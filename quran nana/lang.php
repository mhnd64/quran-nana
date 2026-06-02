<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Default language is English
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Switch language if requested via URL
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar', 'id'])) {
    $_SESSION['lang'] = $_GET['lang'];
    $lang = $_GET['lang'];
    
    // Redirect to the same page without the query parameters for clean URL
    $redirect_url = strtok($_SERVER['REQUEST_URI'], '?');
    header("Location: " . $redirect_url);
    exit;
}

$translations = [
    'en' => [
        'dir' => 'ltr',
        'brand' => 'Quran Analytics',
        'home' => 'Home',
        'dashboard' => 'Dashboard',
        'quran' => 'Holy Quran',
        'miracles' => 'Scientific Miracles',
        'manage_miracles' => 'Manage Miracles',
        'manage_users' => 'Manage Users',
        'welcome' => 'Welcome',
        'guest' => 'Guest',
        'sign_in' => 'Sign In',
        'become_contributor' => 'Become a Contributor',
        'logout' => 'Logout',
        'dakwah_badge' => 'A Dakwah Platform for Quranic Insights',
        'hero_title' => 'Explore the Divine Wonders of the',
        'hero_span' => 'Holy Quran',
        'hero_desc' => 'Welcome to Quran Analytics. A modern educational resource dedicated to sharing scientific miracles of the Holy Quran in fields like astronomy, oceanography, geology, and human biology. Revealed 14 centuries ago, the divine text stands in perfect harmony with modern empirical discoveries.',
        'btn_explore' => 'Explore Scientific Miracles',
        'btn_browse_quran' => 'Browse Holy Quran',
        'stat_verses' => 'Quranic Verses In DB',
        'stat_miracles' => 'Documented Miracles',
        'stat_contributors' => 'Active Contributors',
        'card_seeker_title' => 'For Seekers & Learners',
        'card_seeker_desc' => 'This platform is kept 100% free and open, with absolutely no login required for general browsing. It is designed to be highly accessible and intuitive for friends, students, and seekers of truth.',
        'bullet_seeker_1' => 'Read Quranic verses in authentic Uthmani script.',
        'bullet_seeker_2' => 'Perform instant keyword search across all verses.',
        'bullet_seeker_3' => 'Read compiled scientific consensus maps for every topic.',
        'btn_start_browsing' => 'Start Browsing Directory',
        'card_contributor_title' => 'Join as a Contributor',
        'card_contributor_desc' => 'Are you passionate about research and scientific miracles of the Quran? Register as a contributor to add and update entries, link them to specific verses, and help enrich our database.',
        'bullet_contributor_1' => 'Submit new topics with references & scientific data.',
        'bullet_contributor_2' => 'Secure pending-to-active validation by superadmins.',
        'bullet_contributor_3' => 'Maintain research integrity under absolute security.',
        'btn_register_account' => 'Register Contributor Account',
        'footer_copyright' => 'Quran Analytics. Sharing the miracles of the Quran worldwide.',
        'footer_tag' => 'Developed for Dakwah & Educational purposes.',
        'superadmin_console' => 'Superadmin Console',
        'lang_en' => 'English',
        'lang_ar' => 'العربية',
        'lang_id' => 'Bahasa Indonesia'
    ],
    'ar' => [
        'dir' => 'rtl',
        'brand' => 'تحليلات القرآن',
        'home' => 'الرئيسية',
        'dashboard' => 'لوحة التحكم',
        'quran' => 'القرآن الكريم',
        'miracles' => 'الإعجاز العلمي',
        'manage_miracles' => 'إدارة الإعجاز',
        'manage_users' => 'إدارة المستخدمين',
        'welcome' => 'مرحباً بك',
        'guest' => 'زائر',
        'sign_in' => 'تسجيل الدخول',
        'become_contributor' => 'كن مساهماً معنا',
        'logout' => 'تسجيل الخروج',
        'dakwah_badge' => 'منصة دعوية لبيان معجزات القرآن الكريم',
        'hero_title' => 'اكتشف روائع الإعجاز العلمي في',
        'hero_span' => 'القرآن الكريم',
        'hero_desc' => 'مرحبًا بكم في منصة تحليلات القرآن. مصدر تعليمي ودعوي مخصص لمشاركة معجزات القرآن الكريم العلمية في مجالات الفلك، والبحار، والجيولوجيا، والأحياء. نزل هذا الكتاب منذ 14 قرناً ليتوافق تماماً مع الاكتشافات العلمية الحديثة.',
        'btn_explore' => 'استكشف معجزات القرآن',
        'btn_browse_quran' => 'تصفح المصحف الشريف',
        'stat_verses' => 'الآيات المخزنة بقاعدة البيانات',
        'stat_miracles' => 'مواضيع الإعجاز الموثقة',
        'stat_contributors' => 'المساهمون النشطون',
        'card_seeker_title' => 'للباحثين والمتعلمين',
        'card_seeker_desc' => 'هذه المنصة مجانية ومفتوحة بالكامل، ولا تطلب تسجيل الدخول للتصفح العام، صممت لتكون بسيطة وسهلة الاستخدام لجميع الطلاب والباحثين عن الحقيقة.',
        'bullet_seeker_1' => 'قراءة الآيات الكريمة بالرسم العثماني الأصيل.',
        'bullet_seeker_2' => 'البحث الفوري بالكلمات المفتاحية في جميع الآيات.',
        'bullet_seeker_3' => 'الاطلاع على التوافق العلمي الموثق لكل موضوع.',
        'btn_start_browsing' => 'ابدأ تصفح الدليل الآن',
        'card_contributor_title' => 'انضم كعضو مساهم',
        'card_contributor_desc' => 'هل أنت شغوف بالأبحاث ومعجزات القرآن؟ سجل حساب مساهم لإضافة وتحديث البيانات وربطها بالآيات المناسبة وإثراء الموسوعة العلمية.',
        'bullet_contributor_1' => 'إضافة مواضيع إعجاز جديدة مشفوعة بالقرائن.',
        'bullet_contributor_2' => 'تفعيل الحساب مباشرة بعد مراجعته من المشرف العام.',
        'bullet_contributor_3' => 'الحفاظ على نزاهة الأبحاث تحت أعلى معايير الأمان.',
        'btn_register_account' => 'سجل حساب مساهم جديد',
        'footer_copyright' => 'تحليلات القرآن. نشر إعجاز ومحاسن القرآن الكريم للعالم أجمع.',
        'footer_tag' => 'تطوير لأغراض دعوية وتعليمية.',
        'superadmin_console' => 'لوحة تحكم المشرف العام',
        'lang_en' => 'English',
        'lang_ar' => 'العربية',
        'lang_id' => 'Bahasa Indonesia'
    ],
    'id' => [
        'dir' => 'ltr',
        'brand' => 'Analisis Quran',
        'home' => 'Home',
        'dashboard' => 'Dasbor',
        'quran' => 'Al-Quran',
        'miracles' => 'Mukjizat Ilmiah',
        'manage_miracles' => 'Kelola Mukjizat',
        'manage_users' => 'Kelola Pengguna',
        'welcome' => 'Selamat Datang',
        'guest' => 'Tamu',
        'sign_in' => 'Masuk',
        'become_contributor' => 'Menjadi Kontributor',
        'logout' => 'Keluar',
        'dakwah_badge' => 'Platform Dakwah untuk Wawasan Al-Quran',
        'hero_title' => 'Jelajahi Keajaiban Ilmiah dari',
        'hero_span' => 'Al-Quran Al-Karim',
        'hero_desc' => 'Selamat datang di Quran Analytics. Sebuah sumber edukasi modern yang didedikasikan untuk membagikan mukjizat ilmiah Al-Quran dalam bidang astronomi, oseanografi, geologi, dan biologi manusia. Diturunkan 14 abad yang lalu, teks ilahi ini berdiri dalam harmoni yang sempurna dengan penemuan empiris modern.',
        'btn_explore' => 'Jelajahi Mukjizat Ilmiah',
        'btn_browse_quran' => 'Telusuri Al-Quran',
        'stat_verses' => 'Ayat Al-Quran di DB',
        'stat_miracles' => 'Mukjizat Didokumentasikan',
        'stat_contributors' => 'Kontributor Aktif',
        'card_seeker_title' => 'Untuk Pencari & Pelajar',
        'card_seeker_desc' => 'Platform ini dipertahankan 100% gratis dan terbuka, dengan sama sekali tidak memerlukan login untuk penjelajahan umum. Dirancang sangat mudah diakses dan intuitif untuk teman-teman, siswa, dan pencari kebenaran.',
        'bullet_seeker_1' => 'Baca ayat Al-Quran dalam rasm Utsmani asli.',
        'bullet_seeker_2' => 'Lakukan pencarian kata kunci instan di semua ayat.',
        'bullet_seeker_3' => 'Baca peta konsensus ilmiah yang disusun untuk setiap topik.',
        'btn_start_browsing' => 'Mulai Jelajahi Direktori',
        'card_contributor_title' => 'Bergabung sebagai Kontributor',
        'card_contributor_desc' => 'Apakah Anda bersemangat tentang penelitian dan mukjizat ilmiah Al-Quran? Daftar sebagai kontributor untuk menambah dan memperbarui entri, menghubungkannya ke ayat-ayat tertentu, dan membantu memperkaya basis data kami.',
        'bullet_contributor_1' => 'Kirim topik baru dengan referensi & data ilmiah.',
        'bullet_contributor_2' => 'Validasi tunda-ke-aktif yang aman oleh superadmin.',
        'bullet_contributor_3' => 'Jaga integritas penelitian di bawah keamanan mutlak.',
        'btn_register_account' => 'Daftar Akun Kontributor',
        'footer_copyright' => 'Quran Analytics. Membagikan mukjizat Al-Quran ke seluruh dunia.',
        'footer_tag' => 'Dikembangkan untuk tujuan Dakwah & Pendidikan.',
        'superadmin_console' => 'Konsol Superadmin',
        'lang_en' => 'English',
        'lang_ar' => 'العربية',
        'lang_id' => 'Bahasa Indonesia'
    ]
];

// Helper translation function
function __($key) {
    global $translations, $lang;
    return isset($translations[$lang][$key]) ? $translations[$lang][$key] : $key;
}

// Get current directory direction (RTL or LTR)
function get_dir() {
    global $translations, $lang;
    return isset($translations[$lang]['dir']) ? $translations[$lang]['dir'] : 'ltr';
}
