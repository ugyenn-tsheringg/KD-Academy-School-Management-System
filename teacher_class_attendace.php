
<?php
session_start();
// Database configuration
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'kd_academy');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'Teacher') {
    header('Location: login.php');
    exit();
}

// Create connection
$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Initialize message session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Get teacher info
$teacher_id = $_SESSION['user_id'];
$teacher = $db->query("SELECT programme FROM users WHERE id = $teacher_id")->fetch_assoc();
$teacher_programme = $teacher['programme'];

// Process attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attendance_date = date('Y-m-d');
    $selected_group = $_GET['group'] ?? '';
    
    try {
        $db->begin_transaction();
        
        foreach ($_POST['attendance'] as $student_id => $status) {
            $stmt = $db->prepare("INSERT INTO attendance (student_id, attendance_date, status, teacher_id) 
                                VALUES (?, ?, ?, ?) 
                                ON DUPLICATE KEY UPDATE status = VALUES(status), teacher_id = VALUES(teacher_id)");
            $stmt->bind_param('isss', $student_id, $attendance_date, $status, $teacher_id);
            $stmt->execute();
        }
        
        $db->commit();
        $_SESSION['message'] = "Attendance saved successfully!";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        $db->rollback();
        $_SESSION['message'] = "Error saving attendance: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: ".$_SERVER['PHP_SELF']."?group=".urlencode($selected_group));
    exit();
}
// Get students
$selected_group = $_GET['group'] ?? '';
$students = [];
$attendance_date = date('Y-m-d');

if (in_array($selected_group, ['A', 'B'])) {
    $stmt = $db->prepare("SELECT u.id, u.name, a.status 
                         FROM users u 
                         LEFT JOIN attendance a ON u.id = a.student_id AND a.attendance_date = ?
                         WHERE u.user_type = 'Student' 
                         AND u.programme = ? 
                         AND u.user_group = ?");
    $stmt->bind_param('sss', $attendance_date, $teacher_programme, $selected_group);
    $stmt->execute();
    $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">

    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-gray: #ecf0f1;
            --dark-gray: #7f8c8d;
        }

        .container {
            max-width: 900px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .group-selector {
            margin-bottom: 2rem;
            background: var(--light-gray);
            padding: 1rem;
            border-radius: 8px;
        }

        select {
            padding: 0.8rem 1.2rem;
            border: 2px solid var(--primary-color);
            border-radius: 6px;
            background: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        select:hover {
            border-color: var(--secondary-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 2rem 0;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: left;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--light-gray);
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        .status-select {
            padding: 0.5rem;
            border: 2px solid var(--primary-color);
            border-radius: 4px;
            width: 120px;
            background: white;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
        }

        .alert-info {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .fa-users {
            font-size: 1.5em;
        }
        /* Add these new styles */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            position: relative;
        }

        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }

        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }

        .close-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: inherit;
        }

        .fade-out {
            animation: fadeOut 3s forwards;
            animation-delay: 2s;
        }

        @keyframes fadeOut {
            from {opacity: 1;}
            to {opacity: 0;}
        }
    </style>
</head>
<body>
    <nav class="sidebar">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="img/logo.png" alt="logo">
                </span>
                <div class="text logo-text">
                    <span class="name">Academy</span>
                    <span class="profession">Learning Hub</span>
                </div>
            </div>
            <i class='bx bx-chevron-right toggle'></i>
        </header>
        <div class="menu-bar">
            <div class="menu">
                <hr style="position: relative; bottom: 15px;">
                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="teacher_dashboard.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>  
                    <li class="nav-link active">
                        <a href="teacher_class_attendace.php">
                            <i class='bx bx-group icon'></i>
                            <span class="text nav-text">Class Attendance</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="teacher_result.php">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Academic Results</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="teacher_course.php">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Student Courses</span>
                        </a>
                    </li> 
                    <li class="nav-link">
                        <a href="teacher_account.php">
                            <i class='bx bx-user icon' ></i>
                            <span class="text nav-text">Account</span>
                        </a>
                    </li> 
                </ul>
            </div>
            <div class="bottom-content">
                <li class="">
                    <a href="#">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>
                <li class="mode">
                    <div class="sun-moon">
                        <i class='bx bx-moon icon moon'></i>
                        <i class='bx bx-sun icon sun'></i>
                    </div>
                    <span class="mode-text text">Dark mode</span>
                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
                </li>
            </div>
        </div>  
    </nav>
    <section class="right-section home">
        <div class="upper-text">Class Attendance</div>
        <div class="search-account">
            <div class="search">
                <div>
                    <span style="font-size: 24px; color: #707070; position: relative; top: 3px; left: 5px;">
                        <i class='bx bx-search search-icon'></i>
                    </span>
                </div>
                <div>
                    <input type="search" class="search-text" placeholder="Search">
                </div>
            </div>
            <div class="message-box">
                <span style="font-size: 30px; color: #707070; cursor: pointer;">
                    <i class='bx bx-envelope search-icon'></i>
                </span>
            </div>
            <div>   
                <span style="font-size: 35px; color: #707070; cursor: pointer;">
                    <i class='bx bx-user-circle search-icon'></i>
                </span>
            </div>
        </div>
    </section>
    <section class="main-section">
        <div class="container">
            <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $message_type ?> fade-out">
                <?= $message ?>
                <span class="close-btn" onclick="this.parentElement.style.display='none'">&times;</span>
            </div>
            <?php endif; ?>
            <div class="header">
                <h1><i class="fas fa-users"></i> KD Academy Attendance System</h1>
            </div>
            
            <div class="content">
                <div class="group-selector">
                    <form method="GET">
                        <label for="group">Select Group:</label>
                        <select name="group" id="group" onchange="this.form.submit()">
                            <option value="">Choose Group</option>
                            <option value="A" <?= $selected_group === 'A' ? 'selected' : '' ?>>Group A</option>
                            <option value="B" <?= $selected_group === 'B' ? 'selected' : '' ?>>Group B</option>
                        </select>
                    </form>
                </div>
    
                <?php if (!empty($students)): ?>
                <form method="POST">
                    <table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= htmlspecialchars($student['name']) ?></td>
                                <td>
                                    <select class="status-select" name="attendance[<?= $student['id'] ?>]">
                                        <option value="Present" <?= ($student['status'] ?? '') === 'Present' ? 'selected' : '' ?>>
                                            <i class="fas fa-check"></i> Present
                                        </option>
                                        <option value="Absent" <?= ($student['status'] ?? '') === 'Absent' ? 'selected' : '' ?>>
                                            <i class="fas fa-times"></i> Absent
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Attendance
                    </button>
                </form>
                <?php elseif ($selected_group): ?>
                <div class="alert alert-info">
                    No students found in <?= $teacher_programme ?> Group <?= $selected_group ?>
                </div>
                <?php endif; ?>
            </div>  
        </div>     
    </section>
    <script>
        // Auto-hide success messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.fade-out');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 5000);
            });
        });
    </script>
</body>
</html>