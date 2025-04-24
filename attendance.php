<?php
session_start();
// Database configuration
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'kd_academy');

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'Student') {
    header('Location: login.html');
    exit();
}

// Create connection
$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get student information
$student_id = $_SESSION['user_id'];
$student = $db->query("SELECT name, programme, user_group FROM users WHERE id = $student_id")->fetch_assoc();

// Get attendance records
$attendance = [];
$summary = [
    'total' => 0,
    'present' => 0,
    'absent' => 0,
    'percentage' => 0
];

$stmt = $db->prepare("SELECT a.attendance_date, a.status, u.name AS teacher_name 
                     FROM attendance a 
                     JOIN users u ON a.teacher_id = u.id 
                     WHERE a.student_id = ?
                     ORDER BY a.attendance_date DESC");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$attendance = $result->fetch_all(MYSQLI_ASSOC);

// Calculate summary
if (!empty($attendance)) {
    $summary['total'] = count($attendance);
    $present = array_filter($attendance, fn($record) => $record['status'] === 'Present');
    $summary['present'] = count($present);
    $summary['absent'] = $summary['total'] - $summary['present'];
    $summary['percentage'] = $summary['total'] > 0 
        ? round(($summary['present'] / $summary['total']) * 100, 2)
        : 0;
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
            border-radius: px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .student-info {
            padding: 2rem;
            background: var(--light-gray);
            margin: 1rem;
            border-radius: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .info-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
        }

        .info-card h3 {
            margin: 0 0 0.5rem;
            color: var(--dark-gray);
            font-size: 1rem;
        }

        .info-card .value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }

        .attendance-table {
            margin: 2rem;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        .status-present {
            color: #2ecc71;
            font-weight: bold;
        }

        .status-absent {
            color: #e74c3c;
            font-weight: bold;
        }

        .percentage-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: conic-gradient(
                #2ecc71 <?= $summary['percentage'] * 3.6 ?>deg,
                #e74c3c <?= $summary['percentage'] * 3.6 ?>deg 360deg
            );
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .percentage-text {
            background: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
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
                        <a href="dashboard.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="register_course.php">
                            <i class='bx bx-edit icon'></i>
                            <span class="text nav-text">Course Registration</span>
                        </a>
                    </li>   
                    <li class="nav-link active">
                        <a href="attendance.php">
                            <i class='bx bx-group icon'></i>
                            <span class="text nav-text">Class Attendance</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="result.php">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Academic Results</span>
                        </a>
                    </li>   
                    <li class="nav-link">
                        <a href="account.php">
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
            <div class="header">
                <h1><i class="fas fa-user-graduate"></i> KD Academy Student Portal</h1>
            </div>

            <div class="student-info">
                <h2>Welcome, <?= htmlspecialchars($student['name']) ?></h2>
                <div class="info-grid">
                    <div class="info-card">
                        <h3>Programme</h3>
                        <div class="value"><?= htmlspecialchars($student['programme']) ?></div>
                    </div>
                    <div class="info-card">
                        <h3>Group</h3>
                        <div class="value"><?= htmlspecialchars($student['user_group']) ?></div>
                    </div>
                    <div class="info-card">
                        <h3>Attendance Percentage</h3>
                        <div class="percentage-circle">
                            <div class="percentage-text">
                                <?= $summary['percentage'] ?>%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-card">
                        <h3>Total Days</h3>
                        <div class="value"><?= $summary['total'] ?></div>
                    </div>
                    <div class="info-card">
                        <h3>Present Days</h3>
                        <div class="value status-present"><?= $summary['present'] ?></div>
                    </div>
                    <div class="info-card">
                        <h3>Absent Days</h3>
                        <div class="value status-absent"><?= $summary['absent'] ?></div>
                    </div>
                </div>
            </div>

            <div class="attendance-table">
                <?php if (!empty($attendance)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance as $record): ?>
                            <tr>
                                <td><?= date('F j, Y', strtotime($record['attendance_date'])) ?></td>
                                <td>
                                    <span class="status-<?= strtolower($record['status']) ?>">
                                        <?= $record['status'] ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($record['teacher_name']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="info-card" style="text-align: center; margin: 2rem;">
                        No attendance records found
                    </div>
                <?php endif; ?>
            </div>
        </div>
 
    </section>
    <script src="main.js"></script>
</body>
</html>