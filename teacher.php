<?php
require 'config.php';

if (!isLoggedIn() || !isTeacher()) {
    redirect('login.php');
}

// Get teacher info
$stmt = $pdo->prepare("
    SELECT t.* 
    FROM teachers t
    JOIN users u ON t.user_id = u.id
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all students for dropdown
$students_stmt = $pdo->query("SELECT id, first_name, last_name, class FROM students ORDER BY class, last_name");
$students = $students_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get grades given by this teacher
$grades_stmt = $pdo->prepare("
    SELECT g.*, s.first_name AS student_first_name, s.last_name AS student_last_name, s.class
    FROM grades g
    JOIN students s ON g.student_id = s.id
    WHERE g.teacher_id = ?
    ORDER BY g.semester, s.class, s.last_name
");
$grades_stmt->execute([$teacher['id']]);
$given_grades = $grades_stmt->fetchAll(PDO::FETCH_ASSOC);

// Process form submission for adding grades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_grade'])) {
    $student_id = $_POST['student_id'];
    $subject = $_POST['subject'];
    $semester = $_POST['semester'];
    $grade = $_POST['grade'];
    $notes = $_POST['notes'];
    
    // Validate inputs
    if (!empty($student_id) && !empty($subject) && !empty($semester) && !empty($grade)) {
        $stmt = $pdo->prepare("
            INSERT INTO grades (student_id, teacher_id, subject, semester, grade, notes)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $student_id,
            $teacher['id'],
            $subject,
            $semester,
            $grade,
            $notes
        ]);
        
        // Refresh the page to show the new grade
        redirect('teacher.php');
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة المعلم - نظام إدارة المدرسة</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-mode">
    <div class="container">
        <header>
            <h1>مرحباً، الأستاذ <?php echo htmlspecialchars($teacher['last_name']); ?>!</h1>
            <a href="logout.php" class="btn logout">تسجيل الخروج</a>
        </header>
        
        <div class="dashboard">
            <div class="profile-card">
                <h2>ملفك الشخصي</h2>
                <p><strong>الاسم:</strong> <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></p>
                <p><strong>المادة:</strong> <?php echo htmlspecialchars($teacher['subject']); ?></p>
                <p><strong>البريد الإلكتروني:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
            
            <div class="add-grade-section">
                <h2>إضافة درجة</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="student_id">الطالب:</label>
                        <select name="student_id" id="student_id" required>
                            <option value="">اختر الطالب</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name'] . ' (' . $student['class'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">المادة:</label>
                        <input type="text" name="subject" id="subject" value="<?php echo htmlspecialchars($teacher['subject']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="semester">الفصل الدراسي:</label>
                        <select name="semester" id="semester" required>
                            <option value="الفصل الأول">الفصل الأول</option>
                            <option value="الفصل الثاني">الفصل الثاني</option>
                            <option value="الفصل الثالث">الفصل الثالث</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="grade">الدرجة:</label>
                        <input type="number" name="grade" id="grade" min="0" max="20" step="0.25" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">ملاحظات:</label>
                        <textarea name="notes" id="notes" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="add_grade" class="btn">إضافة درجة</button>
                </form>
            </div>
            
            <div class="grades-section">
                <h2>الدرجات التي قمت بإعطائها</h2>
                
                <?php if (empty($given_grades)): ?>
                    <p>لا توجد درجات مسجلة بعد</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>الطالب</th>
                                <th>الصف</th>
                                <th>المادة</th>
                                <th>الفصل</th>
                                <th>الدرجة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($given_grades as $grade): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grade['student_first_name'] . ' ' . $grade['student_last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['class']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['semester']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['notes'] ?? 'لا يوجد'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>