<?php
require 'config.php';

if (!isLoggedIn() || !isStudent()) {
    redirect('login.php');
}

// Get student info
$stmt = $pdo->prepare("
    SELECT s.* 
    FROM students s
    JOIN users u ON s.user_id = u.id
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Get grades
$stmt = $pdo->prepare("
    SELECT g.*, t.first_name AS teacher_first_name, t.last_name AS teacher_last_name
    FROM grades g
    JOIN teachers t ON g.teacher_id = t.id
    WHERE g.student_id = ?
    ORDER BY g.semester, g.subject
");
$stmt->execute([$student['id']]);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize grades by semester
$semesters = [];
foreach ($grades as $grade) {
    $semester = $grade['semester'];
    if (!isset($semesters[$semester])) {
        $semesters[$semester] = [];
    }
    $semesters[$semester][] = $grade;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - School Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-mode">
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($student['first_name']); ?>!</h1>
            <a href="logout.php" class="btn logout">Logout</a>
        </header>
        
        <div class="dashboard">
            <div class="profile-card">
                <h2>Your Profile</h2>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
                <p><strong>Class:</strong> <?php echo htmlspecialchars($student['class']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
            
            <div class="grades-section">
                <h2>Your Grades</h2>
                
                <?php if (empty($semesters)): ?>
                    <p>No grades recorded yet.</p>
                <?php else: ?>
                    <?php foreach ($semesters as $semester => $semester_grades): ?>
                        <div class="semester">
                            <h3>Semester: <?php echo htmlspecialchars($semester); ?></h3>
                            
                            <table>
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Grade</th>
                                        <th>Teacher</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($semester_grades as $grade): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                                            <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                                            <td><?php echo htmlspecialchars($grade['teacher_first_name'] . ' ' . $grade['teacher_last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($grade['notes'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>