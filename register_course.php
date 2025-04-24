<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_connection.php';

// Check student login
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Student') {
    header("Location: login.html");
    exit();
}

// Database setup verification
function verify_tables($conn) {
    $tables = ['courses', 'registrations', 'users'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result->num_rows == 0) {
            die("Error: Missing table '$table' in database");
        }
    }
}

try {
    verify_tables($conn);
    
    // Get student info
    $stmt = $conn->prepare("SELECT programme FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    
    if (!$student) {
        throw new Exception("Student record not found!");
    }
    
    $programme = $student['programme'];
    $semester = isset($_GET['semester']) ? (int)$_GET['semester'] : 1;

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['courses']) && isset($_POST['semester'])) {
            $successCount = 0;
            $errors = [];
            
            foreach ($_POST['courses'] as $course_id) {
                // Validate course exists for programme
                $checkStmt = $conn->prepare("SELECT id FROM courses WHERE id = ? AND programme = ?");
                $checkStmt->bind_param("is", $course_id, $programme);
                $checkStmt->execute();
                
                if (!$checkStmt->get_result()->num_rows) {
                    $errors[] = "Invalid course selection";
                    continue;
                }
                
                // Insert registration
                $insertStmt = $conn->prepare("INSERT INTO registrations (user_id, course_id, semester) VALUES (?, ?, ?)");
                $insertStmt->bind_param("iii", $_SESSION['user_id'], $course_id, $semester);
                
                if ($insertStmt->execute()) {
                    $successCount++;
                } else {
                    // Handle duplicate entry gracefully
                    if ($conn->errno == 1062) {
                        $errors[] = "Already registered for some courses";
                    } else {
                        $errors[] = "Database error: " . $conn->error;
                    }
                }
            }
            
            if ($successCount > 0) {
                $_SESSION['success'] = "Successfully registered for $successCount courses!";
            }
            if (!empty($errors)) {
                $_SESSION['error'] = implode("<br>", array_unique($errors));
            }
            header("Location: register_course.php?semester=$semester");
            exit();
        }
    }

    // Get available courses
    $stmt = $conn->prepare("SELECT * FROM courses WHERE programme = ? AND semester = ?");
    $stmt->bind_param("si", $programme, $semester);
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Get registered courses
    $registeredStmt = $conn->prepare("SELECT course_id FROM registrations WHERE user_id = ?");
    $registeredStmt->bind_param("i", $_SESSION['user_id']);
    $registeredStmt->execute();
    $registered = array_column($registeredStmt->get_result()->fetch_all(MYSQLI_ASSOC), 'course_id');

} catch (Exception $e) {
    die("System error: " . $e->getMessage());
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
            --primary: #2c3e50;
            --secondary: #695CFE;
            --success: #27ae60;
            --light: #ecf0f1;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            background: var(--success);
            color: white;
        }

        h1 {
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
        }

        .semester-selector {
            text-align: center;
            margin-bottom: 30px;
        }

        .semester-btn {
            padding: 10px 20px;
            margin: 0 10px;
            border: 2px solid var(--secondary);
            border-radius: 5px;
            background: white;
            color: var(--secondary);
            cursor: pointer;
            transition: all 0.3s;
        }

        .semester-btn.active {
            background: var(--secondary);
            color: white;
        }

        .course-list {
            display: grid;
            gap: 15px;
        }

        .course-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: white;
        }

        .course-item input {
            margin-right: 15px;
            transform: scale(1.2);
        }

        .course-info {
            flex-grow: 1;
        }

        .course-code {
            font-weight: bold;
            color: var(--primary);
        }

        .course-credits {
            color: #666;
            font-size: 0.9em;
        }

        button[type="submit"] {
            display: block;
            width: 100%;
            padding: 15px;
            margin-top: 20px;
            background: var(--success);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
        }
                /* Add this to your CSS */
                .semester-btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .semester-btn {
            padding: 10px 20px;
            border: 2px solid #695CFE;
            background: none;
            color: #695CFE;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .semester-btn.active {
            background: #695CFE;
            color: white;
        }
        
        .semester-btn:hover {
            background: #695CFE;
            color: white;
        }
                /* Add these new styles */
                .course-item.registered {
            background: #e8f5e9;
            border-color: #4CAF50;
        }
        
        .course-item.selected {
            background: #f0f8ff;
            border-color: #2196F3;
        }
        
        .checkbox-wrapper {
            display: none;
        }
        
        .course-item {
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .course-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        /* Modify existing styles */
        .semester-btn-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .semester-btn {
            padding: 10px 20px;
            border: 2px solid #695CFE;
            background: none;
            color: #695CFE;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .semester-btn.active {
            background: #695CFE;
            color: white;
        }

        .semester-btn:hover {
            background: #695CFE;
            color: white;
        }
        .registered-badge {
            color: #4CAF50;
            font-weight: bold;
            margin-top: 5px;
            font-size: 0.9em;
        }
        .summary-section {
            border: 1px solid #ddd;
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .btn-pdf {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            transition: background 0.3s;
        }

        .btn-pdf:hover {
            background: #c0392b;
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
                    <li class="nav-link active">
                        <a href="register_course.php">
                            <i class='bx bx-edit icon'></i>
                            <span class="text nav-text">Course Registration</span>
                        </a>
                    </li>
                    <li class="nav-link">
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
        <div class="upper-text">Course Registration</div>
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
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert success"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert error"><?= $_SESSION['error'] ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
    
            <h1>📚 Course Registration - <?= $programme ?> Semester <?= $semester ?></h1>
    
            <div class="semester-btn-group">
                <a href="register_course.php?semester=1" 
                   class="semester-btn <?= $semester == 1 ? 'active' : '' ?>">
                    Semester 1
                </a>    
                <a href="register_course.php?semester=2" 
                   class="semester-btn <?= $semester == 2 ? 'active' : '' ?>">
                    Semester 2
                </a>
            </div>
            <form method="POST">
                <input type="hidden" name="semester" value="<?= $semester ?>">
                <div class="course-list">
                <?php foreach ($courses as $course): 
                    $isRegistered = in_array($course['id'], $registered);
                    ?>
                    <label class="course-item <?= $isRegistered ? 'registered' : '' ?>">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="courses[]" value="<?= $course['id'] ?>" 
                                <?= $isRegistered ? 'disabled' : '' ?>>
                        </div>
                        <div class="course-info">
                            <div class="course-code"><?= $course['course_code'] ?></div>
                            <div class="course-name"><?= $course['course_name'] ?></div>
                            <div class="course-credits"><?= $course['credits'] ?> Credits</div>
                            <?php if($isRegistered): ?>
                                <div class="registered-badge">✓ Registered</div>
                            <?php endif; ?>
                        </div>
                    </label>
                <?php endforeach; ?>
                </div>
                
                <button type="submit">Register Selected Courses</button>
            </form>
            <div class="summary-section" style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3>Registration Summary</h3>
            <?php
            // Get registration count for current semester
            $summaryStmt = $conn->prepare("SELECT COUNT(*) AS total FROM registrations 
                                        WHERE user_id = ? AND semester = ?");
            $summaryStmt->bind_param("ii", $_SESSION['user_id'], $semester);
            $summaryStmt->execute();
            $summary = $summaryStmt->get_result()->fetch_assoc();
            ?>
            <p>Total Courses Registered: <strong><?= $summary['total'] ?></strong></p>
            <a href="generate_pdf.php?semester=<?= $semester ?>" 
            class="btn-pdf" 
            style="background: #e74c3c; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
                Download PDF Summary
            </a>
        </div>
        </div>
        </div>    
    </section>
    <script>
        // Add click handling for course items
        document.querySelectorAll('.course-item').forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            
            if(!checkbox.disabled) {
                item.addEventListener('click', (e) => {
                    if(!checkbox.checked) {
                        item.classList.add('selected');
                        checkbox.checked = true;
                    } else {
                        item.classList.remove('selected');
                        checkbox.checked = false;
                    }
                });
            }
        });
        document.querySelector('form').addEventListener('submit', (e) => {
            const btn = e.target.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = 'Processing...';
        });
    </script>

</body>
</html>