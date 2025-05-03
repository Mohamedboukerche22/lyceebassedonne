<?php
require 'config.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin.php');
    } elseif (isTeacher()) {
        redirect('teacher.php');
    } elseif (isStudent()) {
        redirect('student.php');
    }
}

// Initialize dark mode from cookie or default to light
$darkMode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المدرسة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Light Mode Colors */
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --text-color: #2b2d42;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --border-color: #e9ecef;
            --hero-text: #ffffff;
            --btn-primary: #4361ee;
            --btn-secondary: #f8f9fa;
        }

        .dark-mode {
            /* Dark Mode Colors */
            --primary-color: #4895ef;
            --secondary-color: #3a0ca3;
            --accent-color: #4cc9f0;
            --text-color: #f8f9fa;
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            --border-color: #2d2d2d;
            --hero-text: #ffffff;
            --btn-primary: #4895ef;
            --btn-secondary: #2d2d2d;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            transition: all 0.3s ease;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--hero-text);
            border-radius: 1rem;
            margin-bottom: 3rem;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            transform: rotate(30deg);
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
            position: relative;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
            position: relative;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 1rem;
            border: 2px solid transparent;
        }

        .btn.primary {
            background-color: var(--btn-primary);
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }

        .btn.primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(67, 97, 238, 0.4);
        }

        .btn.secondary {
            background-color: transparent;
            color: white;
            border-color: white;
        }

        .btn.secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .feature-card {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .feature-card .icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            width: fit-content;
        }

        .feature-card h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .feature-card p {
            color: var(--text-color);
            opacity: 0.9;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .feature-link {
            text-decoration: none;
            color: inherit;
        }

        .theme-toggle {
            position: fixed;
            bottom: 2rem;
            left: 2rem;
            background: var(--primary-color);
            color: white;
            border: none;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: scale(1.1) rotate(30deg);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .hero {
                padding: 3rem 1rem;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
            }
            
            .features {
                grid-template-columns: 1fr;
            }
            
            .theme-toggle {
                bottom: 1rem;
                left: 1rem;
                width: 3rem;
                height: 3rem;
                font-size: 1.1rem;
            }
            .theme-toggle {
          animation: pulse 2s infinite;
            }

        @keyframes pulse {
        0% { transform: scale(1); }
       50% { transform: scale(1.1); }
        100% { transform: scale(1); }
        }
        [data-tooltip]::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    margin-left: 10px;
    white-space: nowrap;
    background: var(--primary-color);
    color: white;
    padding: 5px 10px;
    border-radius: 5px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s;
}

[data-tooltip]:hover::after {
    opacity: 1;
}
       }
    </style>
</head>
<body class="<?php echo $darkMode ? 'dark-mode' : ''; ?>">
    <button class="theme-toggle" id="themeToggle" aria-label="تبديل الوضع الليلي">
        <i class="<?php echo $darkMode ? 'far fa-sun' : 'far fa-moon'; ?>"></i>
    </button>

    <div class="container">
        <div class="hero">
            <h1>نظام إدارة المدرسة</h1>
            <p>منصة متكاملة لإدارة العملية التعليمية بكفاءة عالية</p>
            
            <div class="cta-buttons">
                <a href="login.php" class="btn primary">
                    <i class="fas fa-sign-in-alt" style="margin-left: 8px;"></i>
                    تسجيل الدخول
                </a>
                <a href="register.php" class="btn secondary">
                    <i class="fas fa-user-plus" style="margin-left: 8px;"></i>
                    إنشاء حساب
                </a>
            </div>
        </div>
        
        <div class="features">
            <a href='student.php' class="feature-link">
                <div class="feature-card">
                    <div class="icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h2>بوابة الطلاب</h2>
                    <p>مكان واحد لمتابعة جميع جوانبك الأكاديمية بما في ذلك الدرجات، الحضور، الجدول الدراسي، والمواد التعليمية</p>
                </div>
            </a>
            
            <a href='teacher.php' class="feature-link">
                <div class="feature-card">
                    <div class="icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h2>بوابة المعلمين</h2>
                    <p>أدوات متكاملة لإدارة الفصول الدراسية، تسجيل الدرجات، التواصل مع الطلاب، وإعداد الخطط الدراسية</p>
                </div>
            </a>
            
            <a href='admin.php' class="feature-link">
                <div class="feature-card">
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h2>لوحة الإدارة</h2>
                    <p>أدوات قوية لإدارة النظام بالكامل، تنظيم المستخدمين، إنشاء التقارير، ومراقبة أداء المدرسة</p>
                </div>
            </a>
        </div>
    </div>

    <script>
         document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        
        // Set initial state from PHP
        let isDark = body.classList.contains('dark-mode');
        updateIcon();
        
        // Toggle function
        function toggleDarkMode() {
            isDark = !isDark;
            body.classList.toggle('dark-mode', isDark);
            updateIcon();
            setCookie('darkMode', isDark, 365);
        }
        
        // Update toggle icon
        function updateIcon() {
            themeToggle.innerHTML = isDark ? 
                '<i class="far fa-sun"></i>' : 
                '<i class="far fa-moon"></i>';
        }
        
        // Cookie helper
        function setCookie(name, value, days) {
            const date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            document.cookie = `${name}=${value}; expires=${date.toUTCString()}; path=/`;
        }
        
        // Event listener
        themeToggle.addEventListener('click', toggleDarkMode);
        
        // Optional: Watch for system preference changes
        const colorSchemeQuery = window.matchMedia('(prefers-color-scheme: dark)');
        colorSchemeQuery.addListener((e) => {
            if (!document.cookie.includes('darkMode=')) {
                isDark = e.matches;
                body.classList.toggle('dark-mode', isDark);
                updateIcon();
            }
        });
    });
    </script>
</body>
</html>