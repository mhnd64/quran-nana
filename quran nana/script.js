// نظام التحليلات القرآنية - التفاعل البرمجي للموقع

// دالة تهيئة الموقع فور اكتمال تحميل عناصر الصفحة (DOM)
document.addEventListener("DOMContentLoaded", () => {
    console.log("Quran Analytics Portal Initialized.");

    // دالة إخفاء وإزالة التنبيهات (Alerts) تلقائياً وتدريجياً بعد 5 ثوانٍ
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach(alert => {
        // المؤقت الزمني لبدء الفلاش التدريجي
        setTimeout(() => {
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-10px)";
            // إزالة العنصر تماماً من الصفحة بعد اكتمال تأثير الاختفاء
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // دالة تظليل رابط الصفحة الحالية في شريط التنقل العلوي
    const currentPath = window.location.pathname.split("/").pop();
    const menuLinks = document.querySelectorAll(".topbar-menu a");
    menuLinks.forEach(link => {
        const linkPath = link.getAttribute("href");
        if (currentPath === linkPath) {
            menuLinks.forEach(l => l.classList.remove("active"));
            link.classList.add("active");
        }
    });

    // دالة البحث والفلترة اللحظية للبطاقات (الآيات والمعجزات) أثناء كتابة المستخدم
    const searchInput = document.getElementById("search");
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            const query = e.target.value.toLowerCase().trim();
            
            // فلترة بطاقات الآيات القرآنية بناءً على نص البحث
            const verseCards = document.querySelectorAll(".verse-card");
            verseCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(query)) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });

            // فلترة بطاقات الإعجاز العلمي بناءً على العنوان والمحتوى
            const miracleCards = document.querySelectorAll(".miracle-card");
            miracleCards.forEach(card => {
                const title = card.querySelector("h3") ? card.querySelector("h3").textContent.toLowerCase() : "";
                const content = card.textContent.toLowerCase();
                if (title.includes(query) || content.includes(query)) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        });
    }

    // دالة تأكيد الحذف الأمني للمستخدم قبل مسح السجلات من قاعدة البيانات
    const deleteButtons = document.querySelectorAll(".action-link-delete");
    deleteButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            const confirmed = confirm("Are you sure you want to permanently delete this entry from the database?");
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
});

