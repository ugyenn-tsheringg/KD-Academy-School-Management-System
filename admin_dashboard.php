<?php
session_start();
// Database configuration
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'kd_academy');

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'Teacher') {
    header('Location: login.php');
    exit();
}

$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get statistics
$users_stats = $db->query("SELECT 
    SUM(user_type = 'Student') as students,
    SUM(user_type = 'Teacher') as teachers,
    programme, 
    COUNT(*) as count 
    FROM users 
    WHERE user_type = 'Student'
    GROUP BY programme")->fetch_all(MYSQLI_ASSOC);

$courses_stats = $db->query("SELECT 
    programme,
    COUNT(DISTINCT course_code) as courses 
    FROM courses 
    GROUP BY programme")->fetch_all(MYSQLI_ASSOC);

$total_courses = $db->query("SELECT COUNT(DISTINCT course_code) as total FROM courses")->fetch_assoc()['total'];
$total_attendance = $db->query("SELECT COUNT(*) as total FROM attendance")->fetch_assoc()['total'];

// Prepare chart data
$programmes = [];
$student_counts = [];
$teacher_counts = [];
$course_counts = [];

foreach($users_stats as $stat) {
    $programmes[] = $stat['programme'];
    $student_counts[] = $stat['count'];
}

foreach($courses_stats as $stat) {
    $course_counts[] = $stat['courses'];
}

$teacher_count = $db->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'Teacher'")->fetch_assoc()['count'];
$student_count = $db->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'Student'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #7f8c8d;
        }

        .container {
            max-width: 1000px;
            margin: 5px auto;
            padding: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 10px;
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

        .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        background: white;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .chart-container {
        padding: 10px;
        background: white;
        border-radius: 6px;
        border: 1px solid #eee;
    }

    .chart-title {
        text-align: center;
        color: var(--primary-color);
        margin-bottom: 8px;
        font-size: 0.9rem;
        font-weight: 600;
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
                    <li class="nav-link active">
                        <a href="admin_dashboard.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="admin_manage_courses.php">
                            <i class='bx bx-book-bookmark icon' ></i>
                            <span class="text nav-text">Manage Courses</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="admin_manage_user.php">
                            <i class='bx bx-user-pin icon' ></i>
                            <span class="text nav-text">Manage Users</span>
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
        <div class="upper-text">Dashboard Sidebar</div>
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
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Students</h3>
                    <p><?= $student_count ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon text-danger">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Teachers</h3>
                    <p><?= $teacher_count ?></p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon text-success">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Courses</h3>
                    <p><?= $total_courses ?></p>
                </div>
            </div>
        </div>
        <div class="charts-grid">
            <div class="chart-container">
                <h3 class="chart-title">User Distribution</h3>
                <canvas id="userChart"></canvas>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">Programme Distribution</h3>
                <canvas id="programmeChart"></canvas>
            </div>

            <div class="chart-container">
                <h3 class="chart-title">Course Distribution</h3>
                <canvas id="courseChart"></canvas>
            </div>
        </div>
    </div>
    </section>

    <script>
        const body = document.querySelector('body'); 
        const sidebar = body.querySelector('nav'); 
        const toggle = body.querySelector(".toggle"); 
        const modeSwitch = body.querySelector(".toggle-switch");
        const modeText = body.querySelector(".mode-text");

        // Initialize sidebar state
        document.addEventListener('DOMContentLoaded', () => {
            // Close sidebar by default
            sidebar.classList.add('close');
            
            // Optional: Uncomment to remember state across page refreshes
            // const isClosed = localStorage.getItem('sidebarClosed') === 'true';
            // sidebar.classList.toggle('close', isClosed);
        });

        toggle.addEventListener("click", () => {
            sidebar.classList.toggle("close");
            
            // Optional: Save state to localStorage
            // localStorage.setItem('sidebarClosed', sidebar.classList.contains('close'));
        });

        modeSwitch.addEventListener("click", () => {
            body.classList.toggle("dark");
            modeText.innerText = body.classList.contains("dark") 
                ? "Light mode" 
                : "Dark mode";
            
            // Optional: Save theme preference
            // localStorage.setItem('darkMode', body.classList.contains("dark"));
        });

        // Optional: Load theme preference on page load
        // document.addEventListener('DOMContentLoaded', () => {
        //     const darkMode = localStorage.getItem('darkMode') === 'true';
        //     body.classList.toggle('dark', darkMode);
        //     modeText.innerText = darkMode ? "Light mode" : "Dark mode";
        // });
        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        }
                // User Distribution Pie Chart
                new Chart(document.getElementById('userChart'), {
            type: 'pie',
            data: {
                labels: ['Students', 'Teachers'],
                datasets: [{
                    data: [<?= $student_count ?>, <?= $teacher_count ?>],
                    backgroundColor: ['#3498db', '#e74c3c'],
                    hoverOffset: 4
                }]
            }
        });

        // Programme Distribution Bar Chart
        new Chart(document.getElementById('programmeChart'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($programmes) ?>,
                datasets: [{
                    label: 'Students per Programme',
                    data: <?= json_encode($student_counts) ?>,
                    backgroundColor: ['#3498db', '#2ecc71', '#9b59b6'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Course Distribution Bar Chart
        new Chart(document.getElementById('courseChart'), {
            type: 'bar',
            data: {
                labels: ['BCS', 'BBA', 'BMC'],
                datasets: [{
                    label: 'Courses per Programme',
                    data: <?= json_encode($course_counts) ?>,
                    backgroundColor: ['#3498db', '#2ecc71', '#9b59b6'],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>