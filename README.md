# quran-nana
# Quran Analytics & Scientific Miracles Project (PHP & MySQL)
### مشروع التحليلات القرآنية والإعجاز العلمي (PHP & MySQL)

Welcome to the **Quran Analytics & Scientific Miracles Project**. This file is bilingual (English & Arabic). Scroll down for the Arabic version.
أهلاً بك في **مشروع التحليلات القرآنية والإعجاز العلمي**. هذا الملف متوفر بلغتين (الإنجليزية والعربية). يرجى التمرير لأسفل للوصول إلى النسخة العربية.

---

## 🇺🇸 English Version

An interactive web application showcasing scientific miracles in the Holy Quran and its verses, complete with an integrated dashboard for user management and managing scientific miracles. This project is built using **PHP** and **MySQL** to run locally under a **XAMPP** environment.

### 🚀 Prerequisites
* **XAMPP** (includes Apache server and MySQL database server).
* **PHP** Version 7.4 or newer.

### 🛠️ Quick Start Steps (Locally)
1. **Project Directory Location:**
   Ensure the project folder is inside the `htdocs` directory of your XAMPP installation. Your current project path is:
   `e:\universityprogram\xampppp\htdocs\quran nana\quran nana`

2. **Start XAMPP Servers:**
   * Open the **XAMPP Control Panel**.
   * Start the **Apache** server by clicking **Start**.
   * Start the **MySQL** server by clicking **Start** (essential for database connection).

3. **Open the Website in your Browser:**
   Open any web browser and go to the following link:
   ```text
   http://localhost/quran nana/quran nana/login.php
   ```

### 💾 Automatic Database Initialization (Auto-Seeding)
The database connection file `koneksi.php` is pre-programmed to perform the following operations automatically on your first visit to the website:
1. Create a database named `quran_nana` with Arabic character support (`utf8mb4`).
2. Create the required tables (`user`, `scientific_miracles`, and `quran_verses`).
3. Seed the tables with demo user accounts, famous Quranic verses, and scientific miracles stories automatically without needing to import any external SQL file!

### 🔑 Default Demo Accounts
Two default accounts have been automatically created in the database to easily experience all the features of the site:

| Account Type | Username | Password | Role | Privileges |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | `admin` | `admin123` | `admin` | Full CRUD privileges (Add, Edit, Delete miracles and users) |
| **Student** | `demo` | `demo123` | `student` | Read-only privileges (Browse verses and scientific miracles) |

### 📂 Project Architecture
* `koneksi.php`: Database connection file responsible for initializing tables and auto-seeding data.
* `login.php` & `proses_login.php`: Secure authentication system using Prepared Statements and Password Hashing.
* `dashboard.php`: Interactive dashboard for both administrators and students.
* `manage_miracles.php` & `manage_users.php`: CRUD management interfaces for miracles and users (accessible to admins only).
* `quran.php` & `miracles.php`: Interactive pages for viewing and searching the Holy Quran and miracles.
* `script.js` & `style.css`: Clean, beautiful frontend design and dynamic client-side live search.

---

## 🇸🇦 النسخة العربية

تطبيق ويب تفاعلي يعرض الإعجاز العلمي في القرآن الكريم وآياته الكريمة مع لوحة تحكم متكاملة لإدارة المستخدمين وإدارة المعجزات العلمية. تم بناء المشروع باستخدام **PHP** و **MySQL** ليعمل محلياً تحت بيئة **XAMPP**.

### 🚀 متطلبات التشغيل
* **XAMPP** (يحتوي على خادم Apache وخادم قاعدة بيانات MySQL).
* **PHP** الإصدار 7.4 أو أحدث.

### 🛠️ خطوات التشغيل السريع (محلياً)
1. **نقل مجلد المشروع:**
   تأكد من وجود مجلد المشروع داخل مجلد `htdocs` الخاص بـ XAMPP. المسار الحالي لديك هو:
   `e:\universityprogram\xampppp\htdocs\quran nana\quran nana`

2. **تشغيل خوادم XAMPP:**
   * افتح **XAMPP Control Panel**.
   * قم بتشغيل خادم **Apache** بالضغط على **Start**.
   * قم بتشغيل خادم **MySQL** بالضغط على **Start** (ضروري للاتصال بقاعدة البيانات).

3. **فتح الموقع في المتصفح:**
   افتح المتصفح واذهب إلى الرابط التالي:
   ```text
   http://localhost/quran nana/quran nana/login.php
   ```

### 💾 تهيئة قاعدة البيانات تلقائياً (Auto-Seeding)
لقد تم تصميم ملف الاتصال `koneksi.php` ليقوم بجميع العمليات التالية تلقائياً عند أول زيارة للموقع:
1. إنشاء قاعدة بيانات باسم `quran_nana` ودعم الترميز العربي `utf8mb4`.
2. إنشاء الجداول المطلوبة (`user` و `scientific_miracles` و `quran_verses`).
3. إدخال البيانات التجريبية والآيات القرآنية الشهيرة وقصص الإعجاز العلمي تلقائياً دون الحاجة لاستيراد أي ملف SQL خارجي!

### 🔑 الحسابات الجاهزة للتجربة
تم إنشاء حسابين افتراضيين تلقائياً لتسهيل تجربة جميع مميزات الموقع:

| نوع الحساب | اسم المستخدم (Username) | كلمة المرور (Password) | الصلاحيات (Role) | المميزات |
| :--- | :--- | :--- | :--- | :--- |
| **المدير** | `admin` | `admin123` | `admin` | صلاحيات كاملة لإضافة وتعديل وحذف المعجزات والمستخدمين |
| **الطالب** | `demo` | `demo123` | `student` | تصفح وقراءة الآيات والمعجزات (للقراءة فقط) |

### 📂 الهيكل البرمجي للمشروع
* `koneksi.php`: ملف الاتصال بقاعدة البيانات والمسؤول عن تهيئة الجداول وتلقيم البيانات تلقائياً.
* `login.php` & `proses_login.php`: نظام تسجيل دخول آمن باستخدام استعلامات مُهيأة (Prepared Statements) وتشفير كلمات المرور.
* `dashboard.php`: لوحة التحكم التفاعلية للمستخدمين والمدراء.
* `manage_miracles.php` & `manage_users.php`: صفحات الإضافة والتعديل والحذف للمعجزات والمستخدمين (متاحة للمدراء فقط).
* `quran.php` & `miracles.php`: صفحات تصفح المحتوى والبحث فيه.
* `script.js` & `style.css`: ملفات التصميم الجمالي والتفاعلي والبحث اللحظي السريع.
