<?php
require 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'جميع الحقول مطلوبة!';
    } elseif ($password !== $confirm_password) {
        $error = 'كلمات المرور غير متطابقة!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'صيغة البريد الإلكتروني غير صالحة!';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'البريد الإلكتروني موجود مسبقاً!';
        } else {
            if ($role === 'teacher' && $_POST['teacher_password'] !== 'teacher123') {
                $error = 'كلمة مرور الأستاذ غير صحيحة!';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$email, $hashed_password, $role]);
                $user_id = $pdo->lastInsertId();
                
                if ($role === 'student') {
                    $first_name = $_POST['student_first_name'];
                    $last_name = $_POST['student_last_name'];
                    $class = $_POST['class'];
                    
                    $stmt = $pdo->prepare("INSERT INTO students (user_id, first_name, last_name, class) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $first_name, $last_name, $class]);
                } elseif ($role === 'teacher') {
                    $first_name = $_POST['teacher_first_name'];
                    $last_name = $_POST['teacher_last_name'];
                    $subject = $_POST['subject'];
                    
                    $stmt = $pdo->prepare("INSERT INTO teachers (user_id, first_name, last_name, subject) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $first_name, $last_name, $subject]);
                }
                
                $success = 'تم التسجيل بنجاح! يمكنك الآن تسجيل الدخول.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل - نظام إدارة المدرسة</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-mode">
    <div class="container">
        <div class="form-container">
            <h1>تسجيل حساب جديد</h1>
            
            <?php if ($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" id="registerForm">
                <div class="form-group">
                    <label for="role">أنا:</label>
                    <select name="role" id="role" required>
                        <option value="">اختر الدور</option>
                        <option value="student">طالب</option>
                        <option value="teacher">أستاذ</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="email">البريد الإلكتروني:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">كلمة المرور:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">تأكيد كلمة المرور:</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
                
                <!-- Student fields -->
                <div id="studentFields" class="role-fields">
                    <div class="form-group">
                        <label for="student_first_name">اللقب :</label>
                        <input type="text" name="student_first_name" id="student_first_name">
                    </div>
                    
                    <div class="form-group">
                        <label for="student_last_name">الاسم :</label>
                        <input type="text" name="student_last_name" id="student_last_name">
                    </div>
                    
                    <div class="form-group">
                        <label for="class">الصف:</label>
                        <input type="text" name="class" id="class">
                    </div>
                </div>
                
                <!-- Teacher fields -->
                <div id="teacherFields" class="role-fields" style="display: none;">
                    <div class="form-group">
                        <label for="teacher_first_name">اللقب  :</label>
                        <input type="text" name="teacher_first_name" id="teacher_first_name">
                    </div>
                    
                    <div class="form-group">
                        <label for="teacher_last_name">الاسم :</label>
                        <input type="text" name="teacher_last_name" id="teacher_last_name">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">المادة:</label>
                        <input type="text" name="subject" id="subject">
                    </div>
                    
                    <div class="form-group">
                        <label for="teacher_password">كلمة مرور الأستاذ:</label>
                        <input type="password" name="teacher_password" id="teacher_password">
                        <small>اسأل المدير عن كلمة مرور الأستاذ</small>
                    </div>
                </div>
                
                <button type="submit" class="btn">تسجيل</button>
            </form>
            
            <p>هل لديك حساب بالفعل؟ <a href="login.php">سجل الدخول هنا</a></p>
        </div>
    </div>
    
    <script>
        document.getElementById('role').addEventListener('change', function() {
            const role = this.value;
            document.getElementById('studentFields').style.display = role === 'student' ? 'block' : 'none';
            document.getElementById('teacherFields').style.display = role === 'teacher' ? 'block' : 'none';
        });
    </script>
</body>
</html>