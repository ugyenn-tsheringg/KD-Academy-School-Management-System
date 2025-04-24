<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Teacher') {
    header("Location: login.html");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'kd_academy');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Initialize success message
$_SESSION['success'] = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, programme, user_type, user_group) VALUES (?, ?, ?, ?, ?, ?)");
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt->bind_param("ssssss", $_POST['name'], $_POST['email'], $password, $_POST['programme'], $_POST['user_type'], $_POST['user_group']);
        if($stmt->execute()) {
            $_SESSION['success'] = "User added successfully!";
        }
    }
    elseif (isset($_POST['save_changes'])) {
        foreach ($_POST['id'] as $index => $id) {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, programme=?, user_type=?, user_group=? WHERE id=?");
            $stmt->bind_param("sssssi", 
                $_POST['name'][$index],
                $_POST['email'][$index],
                $_POST['programme'][$index],
                $_POST['user_type'][$index],
                $_POST['user_group'][$index],
                $id
            );
            $stmt->execute();
        }
        $_SESSION['success'] = "Changes saved successfully!";
    }
}

if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $_GET['delete']);
    if($stmt->execute()) {
        $_SESSION['success'] = "User deleted successfully!";
    }
}

// Get filter parameters
$filter_type = $_GET['type'] ?? '';
$filter_programme = $_GET['programme'] ?? '';
$filter_group = $_GET['group'] ?? '';

// Build query
$query = "SELECT * FROM users WHERE 1=1";
if($filter_type) $query .= " AND user_type='$filter_type'";
if($filter_programme) $query .= " AND programme='$filter_programme'";
if($filter_group) $query .= " AND user_group='$filter_group'";
$query .= " ORDER BY user_type, name";

$result = $conn->query($query);
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
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: #f8f9fa;
        }

        .container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .alert {
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            background: var(--success);
            color: white;
            display: <?= isset($_SESSION['success']) ? 'block' : 'none' ?>;
        }

        h1 {
            color: var(--primary);
            text-align: center;
            margin-bottom: 40px;
            font-weight: 600;
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
            background: var(--light);
            padding: 20px;
            border-radius: 8px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        select, input {
            padding: 10px 15px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        select:focus, input:focus {
            border-color: var(--secondary);
            outline: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        tr:hover {
            background: #f1f4f7;
        }

        .action-btns {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--secondary);
            color: white;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .add-user-form {
            margin-bottom: 30px;
            background: var(--light);
            padding: 25px;
            border-radius: 8px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
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
                        <a href="admin_dashboard.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-link">
                        <a href="admin_manage_courses.php">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Manage Courses</span>
                        </a>
                    </li>  
                    <li class="nav-link active">
                        <a href="admin_manage_user.php">
                            <i class='bx bx-user-pin icon' ></i>
                            <span class="text nav-text">Manage Users</span>
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
        <div class="upper-text">Welcome, Admin</div>
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
            <?php if(isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
                <div class="alert"><?= $_SESSION['success'] ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <h1>User Management Dashboard</h1>

            <!-- Filters -->
            <form method="GET" class="filters">
                <div class="filter-group">
                    <label>User Type:</label>
                    <select name="type" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="Student" <?= $filter_type === 'Student' ? 'selected' : '' ?>>Student</option>
                        <option value="Teacher" <?= $filter_type === 'Teacher' ? 'selected' : '' ?>>Teacher</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Programme:</label>
                    <select name="programme" onchange="this.form.submit()">
                        <option value="">All Programmes</option>
                        <option value="BCS" <?= $filter_programme === 'BCS' ? 'selected' : '' ?>>BCS</option>
                        <option value="BBA" <?= $filter_programme === 'BBA' ? 'selected' : '' ?>>BBA</option>
                        <option value="BMC" <?= $filter_programme === 'BMC' ? 'selected' : '' ?>>BMC</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Group:</label>
                    <select name="group" onchange="this.form.submit()">
                        <option value="">All Groups</option>
                        <option value="A" <?= $filter_group === 'A' ? 'selected' : '' ?>>Group A</option>
                        <option value="B" <?= $filter_group === 'B' ? 'selected' : '' ?>>Group B</option>
                    </select>
                </div>
            </form>

            <!-- Add User Form -->
            <div class="add-user-form">
                <h2>➕ Add New User</h2>
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Full Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email Address" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <select name="programme" required>
                                <option value="BCS">BCS</option>
                                <option value="BBA">BBA</option>
                                <option value="BMC">BMC</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="user_type" required>
                                <option value="Student">Student</option>
                                <option value="Teacher">Teacher</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select name="user_group">
                                <option value="">Select Group</option>
                                <option value="A">Group A</option>
                                <option value="B">Group B</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <form method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Programme</th>
                            <th>Type</th>
                            <th>Group</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <input type="hidden" name="id[]" value="<?= $user['id'] ?>">
                                <input type="text" name="name[]" value="<?= htmlspecialchars($user['name']) ?>">
                            </td>
                            <td><input type="email" name="email[]" value="<?= htmlspecialchars($user['email']) ?>"></td>
                            <td>
                                <select name="programme[]">
                                    <option value="BCS" <?= $user['programme'] === 'BCS' ? 'selected' : '' ?>>BCS</option>
                                    <option value="BBA" <?= $user['programme'] === 'BBA' ? 'selected' : '' ?>>BBA</option>
                                    <option value="BMC" <?= $user['programme'] === 'BMC' ? 'selected' : '' ?>>BMC</option>
                                </select>
                            </td>
                            <td>
                                <select name="user_type[]">
                                    <option value="Student" <?= $user['user_type'] === 'Student' ? 'selected' : '' ?>>Student</option>
                                    <option value="Teacher" <?= $user['user_type'] === 'Teacher' ? 'selected' : '' ?>>Teacher</option>
                                </select>
                            </td>
                            <td>
                                <select name="user_group[]">
                                    <option value="">N/A</option>
                                    <option value="A" <?= $user['user_group'] === 'A' ? 'selected' : '' ?>>A</option>
                                    <option value="B" <?= $user['user_group'] === 'B' ? 'selected' : '' ?>>B</option>
                                </select>
                            </td>
                            <td class="action-btns">
                                <a href="?delete=<?= $user['id'] ?>" class="btn btn-danger" 
                                onclick="return confirm('Are you sure you want to delete this user?')">🗑️ Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div style="margin-top: 25px; text-align: right;">
                    <button type="submit" name="save_changes" class="btn btn-success">💾 Save All Changes</button>
                </div>
            </form>
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
    </script>
</body>
</html>