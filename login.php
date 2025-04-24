<?php
session_start(); // Start the session

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kd_academy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['name'])) {
        // Handle sign-up (same as before)
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $programme = $_POST['programme'] ?? null;
        $dob = $_POST['dob'] ?? null;
        $group = $_POST['group'] ?? null;
        $user_type = $_POST['user_type'];

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, programme, dob, user_group, user_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $name, $email, $password, $programme, $dob, $group, $user_type);

        if ($stmt->execute()) {
            echo "<script>
                alert('User registered successfully!');
                window.location.href = 'login.html';
            </script>";
        } else {
            echo "<script>
                alert('Error: " . addslashes($stmt->error) . "');
                window.location.href = 'login.html';
            </script>";
        }

        $stmt->close();
    } else {
        // Handle sign-in
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Hardcoded admin credentials check
        if ($email === 'admin@gmail.com' && $password === 'HelloWorld') {
            // Set admin session and redirect
            $_SESSION['user_id'] = 'admin';
            $_SESSION['user_type'] = 'Teacher';
            echo "<script>
                alert('Admin sign-in successful!');
                window.location.href = 'admin_dashboard.php';
            </script>";
            exit();
        }

        // Fetch user data including user_type
        $stmt = $conn->prepare("SELECT id, password, user_type FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $hashed_password, $user_type);

        if ($stmt->fetch() && password_verify($password, $hashed_password)) {
            // Store user data in session
            $_SESSION['user_id'] = $id;
            $_SESSION['user_type'] = $user_type;

            // Redirect based on user type
            if ($user_type === 'Teacher') {
                echo "<script>
                    alert('Sign-in successful!');
                    window.location.href = 'teacher_dashboard.php';
                </script>";
            } else {
                echo "<script>
                    alert('Sign-in successful!');
                    window.location.href = 'dashboard.php';
                </script>";
            }
        } else {
            echo "<script>
                alert('Invalid email or password.');
                window.location.href = 'login.html';
            </script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>