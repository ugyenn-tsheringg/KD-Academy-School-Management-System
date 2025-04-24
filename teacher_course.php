<?php
session_start();

// Verify user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Teacher') {
    header("Location: login.html");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'kd_academy');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get teacher's program from database
$stmt = $conn->prepare("SELECT programme FROM users WHERE id = ? AND user_type = 'Teacher'");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Teacher not found in database
    header("Location: login.html");
    exit();
}

$teacher = $result->fetch_assoc();
$teacher_programme = $teacher['programme'];

// Verify valid program
$allowed_programs = ['BCS', 'BBA', 'BMC'];
if (!in_array($teacher_programme, $allowed_programs)) {
    die("Invalid program configuration for teacher");
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'kd_academy');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle student removal
if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM registrations WHERE id = ?");
    $stmt->bind_param("i", $_GET['delete']);
    $stmt->execute();
}

// Get selected course details
$selected_course = null;
$students = [];
$total_students = 0;

if (isset($_GET['course_id'])) {
    $course_id = $conn->real_escape_string($_GET['course_id']);
    
    // Get course details
    $course_query = $conn->prepare("SELECT * FROM courses WHERE id = ? AND programme = ?");
    $course_query->bind_param("is", $course_id, $teacher_programme);
    $course_query->execute();
    $selected_course = $course_query->get_result()->fetch_assoc();

    // Get registered students
    if ($selected_course) {
        $stmt = $conn->prepare("
            SELECT users.id, users.name, users.email, registrations.registration_date, registrations.id as reg_id 
            FROM registrations 
            JOIN users ON registrations.user_id = users.id 
            WHERE course_id = ? AND users.programme = ?
            ORDER BY registrations.registration_date DESC
        ");
        $stmt->bind_param("is", $course_id, $teacher_programme);
        $stmt->execute();
        $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $total_students = count($students);
    }
}

// Get all courses for the teacher's programme
$courses_result = $conn->query("
    SELECT * FROM courses 
    WHERE programme = '$teacher_programme' 
    ORDER BY semester, course_code
");
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
        .container {
            max-width: 900px;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .course-selector {
            margin-bottom: 30px;
            padding: 20px;
            background: #ecf0f1;
            border-radius: 8px;
        }

        select, button {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .delete-btn {
            color: #e74c3c;
            text-decoration: none;
            font-weight: bold;
        }

        .delete-btn:hover {
            text-decoration: underline;
        }

        .total-count {
            margin-top: 20px;
            padding: 10px;
            background: #2ecc71;
            color: white;
            border-radius: 4px;
            display: inline-block;
        }

        .course-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
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
                    <li class="nav-link">
                        <a href="teacher_class_attendace.php">
                            <i class='bx bx-group icon'></i>
                            <span class="text nav-text">Class Attendance</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="teacher_result.html">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Academic Results</span>
                        </a>
                    </li>
                    <li class="nav-link active">
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
                <li class="mode">
                    <button onclick="confirmLogout()" class="logout-button" style="height: 50px; width: 100%; display: flex; align-items: center; font-size: 17px; font-weight: 500;">
                        <i class='bx bx-log-out icon'></i>
                        <span class="text nav-text">Logout</span>
                    </button>
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
        <div class="upper-text">Welcome, Teacher</div>
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
            <h1><?= $teacher_programme ?> Course Management</h1>
            
            <div class="course-selector">
                <form method="GET">
                    <select name="course_id" required>
                        <option value="">Select a Course</option>
                        <?php while ($course = $courses_result->fetch_assoc()): ?>
                            <option value="<?= $course['id'] ?>" 
                                <?= isset($_GET['course_id']) && $_GET['course_id'] == $course['id'] ? 'selected' : '' ?>>
                                <?= $course['course_code'] ?> - <?= $course['course_name'] ?> (Semester <?= $course['semester'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit">Show Students</button>
                </form>
            </div>

            <?php if ($selected_course): ?>
                <div class="course-info">
                    <h2><?= $selected_course['course_code'] ?> - <?= $selected_course['course_name'] ?></h2>
                    <p>Semester <?= $selected_course['semester'] ?> | Credits: <?= $selected_course['credits'] ?></p>
                </div>

                <?php if ($total_students > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registration Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= $student['id'] ?></td>
                                    <td><?= htmlspecialchars($student['name']) ?></td>
                                    <td><?= htmlspecialchars($student['email']) ?></td>
                                    <td><?= date('M d, Y H:i', strtotime($student['registration_date'])) ?></td>
                                    <td>
                                        <a href="?course_id=<?= $_GET['course_id'] ?>&delete=<?= $student['reg_id'] ?>" 
                                        class="delete-btn"
                                        onclick="return confirm('Are you sure you want to remove this student?')">
                                            Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="total-count">
                        Total Students Registered: <?= $total_students ?>
                    </div>
                <?php else: ?>
                    <p>No students registered for this course yet.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <script>
        const body = document.querySelector('body'); 
        sidebar = body.querySelector('nav'); 
        toggle = body.querySelector(".toggle"); 
        searchBtn = body.querySelector(".search-box"),
        modeSwitch = body.querySelector(".toggle-switch"),
        modeText = body.querySelector(".mode-text");

        toggle.addEventListener("click" , () =>{
            sidebar.classList.toggle("close");
        })

        searchBtn.addEventListener("click" , () =>{
            sidebar.classList.remove("close");
        })

        modeSwitch.addEventListener("click" , () =>{
        body.classList.toggle("dark");

        if(body.classList.contains("dark")){
        modeText.innerText = "Light mode";
        }else{
        modeText.innerText = "Dark mode";
        }
        });

        function confirmLogout() {
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php";
            }
        }
    </script>
</body>
</html>