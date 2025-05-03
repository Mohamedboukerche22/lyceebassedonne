<?php
require 'config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('login.php');
}

// Get all users
$users = $pdo->query("SELECT * FROM users ORDER BY role, email")->fetchAll(PDO::FETCH_ASSOC);

// Get all students with their user info
$students = $pdo->query("
    SELECT s.*, u.email, u.created_at 
    FROM students s
    JOIN users u ON s.user_id = u.id
    ORDER BY s.class, s.last_name
")->fetchAll(PDO::FETCH_ASSOC);

// Get all teachers with their user info
$teachers = $pdo->query("
    SELECT t.*, u.email, u.created_at 
    FROM teachers t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.subject, t.last_name
")->fetchAll(PDO::FETCH_ASSOC);

// Get all grades
$grades = $pdo->query("
    SELECT g.*, 
           s.first_name AS student_first_name, s.last_name AS student_last_name, s.class,
           t.first_name AS teacher_first_name, t.last_name AS teacher_last_name, t.subject AS teacher_subject
    FROM grades g
    JOIN students s ON g.student_id = s.id
    JOIN teachers t ON g.teacher_id = t.id
    ORDER BY g.semester, s.class, s.last_name
")->fetchAll(PDO::FETCH_ASSOC);

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);
        redirect('admin.php');
    } elseif (isset($_POST['reset_password'])) {
        $user_id = $_POST['user_id'];
        $new_password = password_hash('default123', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$new_password, $user_id]);
        redirect('admin.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="dark-mode">
    <div class="container">
        <header>
            <h1>Admin Dashboard</h1>
            <a href="logout.php" class="btn logout">Logout</a>
        </header>
        
        <div class="dashboard">
            <div class="admin-sections">
                <div class="admin-section">
                    <h2>All Users</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo ucfirst($user['role']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" name="reset_password" class="btn small">Reset Password</button>
                                        </form>
                                        <?php if ($user['email'] !== 'admin12345@gmail.com'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="delete_user" class="btn small danger">Delete</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="admin-section">
                    <h2>All Students</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Class</th>
                                <th>Email</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo $student['id']; ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['class']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($student['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="admin-section">
                    <h2>All Teachers</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Subject</th>
                                <th>Email</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teachers as $teacher): ?>
                                <tr>
                                    <td><?php echo $teacher['id']; ?></td>
                                    <td><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($teacher['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="admin-section">
                    <h2>All Grades</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Semester</th>
                                <th>Grade</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grades as $grade): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grade['student_first_name'] . ' ' . $grade['student_last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['class']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['subject']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['semester']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['teacher_first_name'] . ' ' . $grade['teacher_last_name']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
</body>
</html>