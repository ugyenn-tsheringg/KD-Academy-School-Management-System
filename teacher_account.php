<?php
session_start();
// Database configuration
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'kd_academy');

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Initialize messages
$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Get current user data
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $programme = $_POST['programme'];
    $dob = $_POST['dob'];
    $user_group = $_POST['user_group'];
    $user_type = $_POST['user_type'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $update_fields = [];
    $params = [];
    $types = '';

    // Basic info updates
    if ($name != $user['name']) {
        $update_fields[] = "name = ?";
        $params[] = $name;
        $types .= 's';
    }

    if ($email != $user['email']) {
        $update_fields[] = "email = ?";
        $params[] = $email;
        $types .= 's';
    }

    if ($programme != $user['programme']) {
        $update_fields[] = "programme = ?";
        $params[] = $programme;
        $types .= 's';
    }

    if ($dob != $user['dob']) {
        $update_fields[] = "dob = ?";
        $params[] = $dob;
        $types .= 's';
    }

    if ($user_group != $user['user_group']) {
        $update_fields[] = "user_group = ?";
        $params[] = $user_group;
        $types .= 's';
    }

    if ($user_type != $user['user_type']) {
        $update_fields[] = "user_type = ?";
        $params[] = $user_type;
        $types .= 's';
    }

    // Handle password change
    if (!empty($new_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $_SESSION['message'] = "Current password is incorrect";
            $_SESSION['message_type'] = 'error';
            header("Location: account.php");
            exit();
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['message'] = "New passwords do not match";
            $_SESSION['message_type'] = 'error';
            header("Location: account.php");
            exit();
        }

        $update_fields[] = "password = ?";
        $params[] = password_hash($new_password, PASSWORD_DEFAULT);
        $types .= 's';
    }

    // Build update query
    if (!empty($update_fields)) {
        $query = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = ?";
        $params[] = $user_id;
        $types .= 'i';

        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Profile updated successfully!";
            $_SESSION['message_type'] = 'success';
            
            // Update session data if user type changed
            if ($user_type != $user['user_type']) {
                $_SESSION['user_type'] = $user_type;
            }
        } else {
            $_SESSION['message'] = "Error updating profile: " . $db->error;
            $_SESSION['message_type'] = 'error';
        }
        
        header("Location: account.php");
        exit();
    }
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
            max-width: 800px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .form-group {
            margin-bottom: 1.5rem;
            margin-top: 2rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus {
            border-color: var(--secondary-color);
            outline: none;
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
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            position: relative;
        }

        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .alert-error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }

        .nav-menu {
            background-color: var(--primary-color);
            padding: 1rem;
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

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
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
                    <li class="nav-link">
                        <a href="attendance.php">
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
                    <li class="nav-link active">
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
                <span style="font-size: 35px; color: #707070; cursor: pointer;" onclick="togglePopup()">
                    <i class='bx bx-user-circle search-icon'></i>
                </span>
            </div>
        </div>
        <div class="user-popup" id="userPopup">
            <div class="user-info">
              <i class='bx bx-user-circle' style="font-size: 50px; color: #707070;"></i>
              <div>
                <h4>Easin Arafat</h4>
                <p>easin@example.com</p>
              </div>
            </div>
            <div class="menu-options">
              <button>View Profile</button>
              <button>Account Settings</button>
              <button>Notifications</button>
              <button>Switch Account</button>
              <button>Logout</button>
            </div>
          </div>
    </section>
    <section class="main-section">
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-edit"></i> Account Settings</h1>
        </div>

        <div class="content">
            <?php if ($message): ?>
            <div class="alert alert-<?= $message_type ?>">
                <?= $message ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="two-column">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                </div>

                <div class="two-column">
                    <div class="form-group">
                        <label>Programme</label>
                        <select name="programme">
                            <option value="BCS" <?= $user['programme'] == 'BCS' ? 'selected' : '' ?>>BCS</option>
                            <option value="BBA" <?= $user['programme'] == 'BBA' ? 'selected' : '' ?>>BBA</option>
                            <option value="BMC" <?= $user['programme'] == 'BMC' ? 'selected' : '' ?>>BMC</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>User Type</label>
                        <select name="user_type">
                            <option value="Teacher" <?= $user['user_type'] == 'Teacher' ? 'selected' : '' ?>>Teacher</option>
                            <option value="Student" <?= $user['user_type'] == 'Student' ? 'selected' : '' ?>>Student</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <h3>Change Password</h3>
                    <br>
                    <div class="two-column">
                        <div>
                            <label>Current Password</label>
                            <input type="password" name="current_password">
                        </div>
                        <div>
                            <label>New Password</label>
                            <input type="password" name="new_password">
                        </div>
                        <div>
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password">
                        </div>
                    </div>
                    <small>Leave blank to keep current password</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
    </section>

    <script>
        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.display = 'none';
            });
        }, 5000);
    </script>

</body>
</html>