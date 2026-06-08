// نظام التحليلات القرآنية - التفاعل البرمجي للموقع ومؤثرات الجاذبية
document.addEventListener("DOMContentLoaded", () => {
    console.log("Quran Analytics Portal Initialized.");

    // --- 1. انتقالات الصفحة السلسة (Page Transitions) ---
    const overlay = document.createElement("div");
    overlay.className = "page-transition-overlay";
    document.body.prepend(overlay);
    
    // تفعيل تلاشي الطبقة الانتقالية فور تحميل الصفحة
    requestAnimationFrame(() => {
        overlay.classList.add("loaded");
    });

    // اعتراض الضغط على الروابط لتشغيل الانتقال التدريجي
    document.addEventListener("click", (e) => {
        const link = e.target.closest("a");
        if (link) {
            const href = link.getAttribute("href");
            const target = link.getAttribute("target");
            
            // التأكد من أن الرابط داخلي وليس زر إجراء أو صفحة خارجية
            if (href && !href.startsWith("#") && !href.startsWith("javascript:") && href !== "" && target !== "_blank") {
                e.preventDefault();
                overlay.classList.remove("loaded");
                setTimeout(() => {
                    window.location.href = href;
                }, 400);
            }
        }
    });

    // --- 2. مؤشر الماوس المخصص والمتفاعل (Custom Hover Cursor) ---
    const cursorDot = document.createElement("div");
    const cursorTrail = document.createElement("div");
    cursorDot.className = "custom-cursor-dot";
    cursorTrail.className = "custom-cursor-trail";
    document.body.appendChild(cursorDot);
    document.body.appendChild(cursorTrail);

    let mouseX = 0, mouseY = 0;
    let trailX = 0, trailY = 0;
    let isMoving = false;

    document.addEventListener("mousemove", (e) => {
        mouseX = e.clientX;
        mouseY = e.clientY;
        
        if (!isMoving) {
            cursorDot.style.opacity = "1";
            cursorTrail.style.opacity = "1";
            document.body.classList.add("custom-cursor-active");
            isMoving = true;
        }

        cursorDot.style.left = mouseX + "px";
        cursorDot.style.top = mouseY + "px";
    });

    document.addEventListener("mouseleave", () => {
        cursorDot.style.opacity = "0";
        cursorTrail.style.opacity = "0";
        isMoving = false;
        document.body.classList.remove("custom-cursor-active");
    });

    // حلقة الحركة لتنعيم انتقال مؤشر التتبع الخارجي (Easing/Lerp Loop)
    function animateTrail() {
        const ease = 0.15; // عامل التنعيم والتباطؤ المريح
        trailX += (mouseX - trailX) * ease;
        trailY += (mouseY - trailY) * ease;
        
        cursorTrail.style.left = trailX + "px";
        cursorTrail.style.top = trailY + "px";
        
        requestAnimationFrame(animateTrail);
    }
    animateTrail();

    // تفعيل تأثير التوسيع واللمعان عند مرور الفأرة فوق العناصر التفاعلية
    const hoverSelectors = "a, button, select, input, textarea, .lang-switcher, .topbar-brand, th, td, label, svg";
    function addHoverListeners() {
        const interactiveElements = document.querySelectorAll(hoverSelectors);
        interactiveElements.forEach(elem => {
            // تجنب مضاعفة مستمعي الأحداث
            if (!elem.dataset.hoverBound) {
                elem.dataset.hoverBound = "true";
                elem.addEventListener("mouseenter", () => {
                    cursorDot.classList.add("hovered");
                    cursorTrail.classList.add("hovered");
                });
                elem.addEventListener("mouseleave", () => {
                    cursorDot.classList.remove("hovered");
                    cursorTrail.classList.remove("hovered");
                });
            }
        });
    }
    addHoverListeners();

    // مراقبة التغييرات في الصفحة لإعادة ربط التأثيرات بالعناصر الديناميكية الجديدة
    const observer = new MutationObserver(addHoverListeners);
    observer.observe(document.body, { childList: true, subtree: true });

    // --- 3. نظام جزيئات الجاذبية المضادة التفاعلي (Antigravity Particles Background) ---
    const canvas = document.createElement("canvas");
    canvas.id = "antigravity-canvas";
    document.body.prepend(canvas);
    const ctx = canvas.getContext("2d");

    let width = window.innerWidth;
    let height = window.innerHeight;
    canvas.width = width;
    canvas.height = height;

    window.addEventListener("resize", () => {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    });

    const particles = [];
    const maxParticles = 90;

    class Particle {
        constructor() {
            this.reset();
        }

        reset() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            this.vx = (Math.random() - 0.5) * 0.7;
            this.vy = (Math.random() - 0.5) * 0.7;
            this.radius = Math.random() * 2.5 + 1.2;
            // استخدام تدرج ألوان أخضر وأزرق متناغم مع الهوية البصرية للموقع
            this.color = Math.random() > 0.4 ? "rgba(16, 185, 129, 0.5)" : "rgba(59, 130, 246, 0.4)";
            this.originalVx = this.vx;
            this.originalVy = this.vy;
        }

        update() {
            // جزيئات الجاذبية التفاعلية مع الفأرة
            if (isMoving) {
                const dx = mouseX - this.x;
                const dy = mouseY - this.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                const gravityRadius = 180; // نطاق تأثير الجاذبية

                if (dist < gravityRadius) {
                    const force = (gravityRadius - dist) / gravityRadius;
                    // تأثير التنافر اللطيف (الجاذبية المضادة) عند اقتراب الفأرة
                    this.vx -= (dx / dist) * force * 0.04;
                    this.vy -= (dy / dist) * force * 0.04;
                } else {
                    // العودة التدريجية للسرعة الطبيعية عند ابتعاد الفأرة
                    this.vx += (this.originalVx - this.vx) * 0.02;
                    this.vy += (this.originalVy - this.vy) * 0.02;
                }
            }

            this.x += this.vx;
            this.y += this.vy;

            // الارتداد عند الاصطدام بحدود الشاشة
            if (this.x < 0 || this.x > width) this.vx *= -1;
            if (this.y < 0 || this.y > height) this.vy *= -1;
        }

        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = this.color;
            ctx.fill();
        }
    }

    // توليد الجزيئات
    for (let i = 0; i < maxParticles; i++) {
        particles.push(new Particle());
    }

    // حلقة الأنيميشن الرئيسية للجزيئات ورسم الروابط
    function animateParticles() {
        ctx.clearRect(0, 0, width, height);

        for (let i = 0; i < particles.length; i++) {
            const p = particles[i];
            p.update();
            p.draw();

            // ربط الجزيئات القريبة ببعضها (تأثير الكوكبات الفلكية)
            for (let j = i + 1; j < particles.length; j++) {
                const p2 = particles[j];
                const dx = p.x - p2.x;
                const dy = p.y - p2.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                const connectionRadius = 110;

                if (dist < connectionRadius) {
                    const alpha = ((connectionRadius - dist) / connectionRadius) * 0.15;
                    ctx.strokeStyle = `rgba(16, 185, 129, ${alpha})`;
                    ctx.lineWidth = 0.8;
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.stroke();
                }
            }

            // ربط الجزيئات بمؤشر الفأرة عند اقترابها (تأثير شبكة الطاقة)
            if (isMoving) {
                const dx = mouseX - p.x;
                const dy = mouseY - p.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                const mouseConnectionRadius = 130;

                if (dist < mouseConnectionRadius) {
                    const alpha = ((mouseConnectionRadius - dist) / mouseConnectionRadius) * 0.2;
                    ctx.strokeStyle = `rgba(59, 130, 246, ${alpha})`;
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(mouseX, mouseY);
                    ctx.stroke();
                }
            }
        }

        requestAnimationFrame(animateParticles);
    }
    animateParticles();

    // --- 4. الوظائف السابقة المتواجدة بالموقع (Existing Features) ---

    // تلاشي التنبيهات وإزالتها تلقائياً بعد 5 ثوانٍ
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-10px)";
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // تظليل الرابط النشط بشريط القائمة العلوي
    const currentPath = window.location.pathname.split("/").pop();
    const menuLinks = document.querySelectorAll(".topbar-menu a");
    menuLinks.forEach(link => {
        const linkPath = link.getAttribute("href");
        if (currentPath === linkPath) {
            menuLinks.forEach(l => l.classList.remove("active"));
            link.classList.add("active");
        }
    });

    // الفلترة والبحث الفوري للبطاقات أثناء الكتابة
    const searchInput = document.getElementById("search");
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            const query = e.target.value.toLowerCase().trim();
            
            const verseCards = document.querySelectorAll(".verse-card");
            verseCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(query)) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });

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
});
