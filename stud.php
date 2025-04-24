<?php
session_start();
// Database configuration
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'kd_academy');

// Check authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'Student') {
    header('Location: login.php');
    exit();
}

$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get student data
$student_id = $_SESSION['user_id'];
$student = $db->query("SELECT * FROM users WHERE id = $student_id")->fetch_assoc();

// Get attendance stats
$attendance = $db->query("SELECT 
    SUM(status = 'Present') as present,
    SUM(status = 'Absent') as absent
    FROM attendance 
    WHERE student_id = $student_id")->fetch_assoc();

// Get registered courses
$courses = $db->query("SELECT 
    c.course_code, 
    c.course_name, 
    c.semester,
    r.registration_date
    FROM registrations r
    JOIN courses c ON r.course_id = c.id
    WHERE r.user_id = $student_id")->fetch_all(MYSQLI_ASSOC);

// Get grades
$grades = $db->query("SELECT 
    c.course_name,
    sg.test_score,
    sg.quiz_score,
    sg.final_grade
    FROM student_grades sg
    JOIN courses c ON sg.course_id = c.id
    WHERE sg.user_id = $student_id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #7f8c8d;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: var(--light-gray);
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }

        .welcome-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }

        .stat-icon {
            font-size: 2rem;
            margin-right: 20px;
            width: 60px;
            text-align: center;
            color: var(--primary-color);
        }

        .stat-content h3 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
        }

        .stat-content p {
            margin: 5px 0 0;
            font-size: 1.8rem;
            color: var(--secondary-color);
            font-weight: bold;
        }

        .content-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        .grade-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
        }

        .grade-A { background-color: #2ecc71; color: white; }
        .grade-B { background-color: #3498db; color: white; }
        .grade-C { background-color: #f1c40f; color: white; }
        .grade-D { background-color: #e67e22; color: white; }
        .grade-F { background-color: #e74c3c; color: white; }

        .chart-container {
            padding: 15px;
            background: white;
            border-radius: 8px;
            margin: 20px 0;
        }

        .nav-menu {
            background-color: var(--primary-color);
            padding: 1rem;
            margin-bottom: 30px;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            margin-right: 1.5rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .nav-menu a:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="nav-menu">
        <a href="account.php"><i class="fas fa-user-cog"></i> Account</a>
        <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    </div>

    <div class="container">
        <div class="welcome-section">
            <h1>Welcome, <?= htmlspecialchars($student['name']) ?></h1>
            <p>Programme: <?= $student['programme'] ?> | Group: <?= $student['user_group'] ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div class="stat-content">
                    <h3>Registered Courses</h3>
                    <p><?= count($courses) ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-attendance"></i>
                </div>
                <div class="stat-content">
                    <h3>Attendance</h3>
                    <p><?= $attendance['present'] ?? 0 ?> / <?= ($attendance['present'] ?? 0) + ($attendance['absent'] ?? 0) ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="stat-content">
                    <h3>Current GPA</h3>
                    <p>3.45</p> <!-- You would need to implement GPA calculation -->
                </div>
            </div>
        </div>

        <div class="content-section">
            <h2>Attendance Overview</h2>
            <div class="chart-container">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <div class="content-section">
            <h2>Registered Courses</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Semester</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($courses as $course): ?>
                    <tr>
                        <td><?= $course['course_code'] ?></td>
                        <td><?= $course['course_name'] ?></td>
                        <td>Semester <?= $course['semester'] ?></td>
                        <td><?= date('M d, Y', strtotime($course['registration_date'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="content-section">
            <h2>Academic Performance</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Test Score</th>
                        <th>Quiz Score</th>
                        <th>Final Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($grades as $grade): ?>
                    <tr>
                        <td><?= $grade['course_name'] ?></td>
                        <td><?= $grade['test_score'] ?></td>
                        <td><?= $grade['quiz_score'] ?></td>
                        <td>
                            <span class="grade-badge grade-<?= $grade['final_grade'][0] ?>">
                                <?= $grade['final_grade'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Attendance Chart
        new Chart(document.getElementById('attendanceChart'), {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent'],
                datasets: [{
                    data: [<?= $attendance['present'] ?? 0 ?>, <?= $attendance['absent'] ?? 0 ?>],
                    backgroundColor: ['#2ecc71', '#e74c3c'],
                    hoverOffset: 4
                }]
            }
        });
    </script>
</body>
</html>