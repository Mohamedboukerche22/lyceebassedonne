<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Special admin login
    if ($email === 'admin12345@gmail.com' && $password === 'adda28011968') {
        $_SESSION['user_id'] = 0; // Special ID for admin
        $_SESSION['role'] = 'admin';
        $_SESSION['email'] = $email;
        redirect('admin.php');
    }
    
    // Regular user login
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        
        if ($user['role'] === 'student') {
            redirect('student.php');
        } elseif ($user['role'] === 'teacher') {
            redirect('teacher.php');
        } elseif ($user['role'] === 'admin') {
            redirect('admin.php');
        }
    } else {
        $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة!';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة المدرسة</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-mode">
    <div class="container">
        <div class="form-container">
            <h1>تسجيل الدخول</h1>
            
            <?php if ($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">البريد الإلكتروني:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">كلمة المرور:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                
                <button type="submit" class="btn">تسجيل الدخول</button>
            </form>
            
            <p>ليس لديك حساب؟ <a href="register.php">سجل هنا</a></p>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>