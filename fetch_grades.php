<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
require_once 'db_connection.php';

// Get the logged-in user's programme from database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT programme FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$programme = $user['programme'];

function fetchStudentGrades($conn, $programme) {
    $gradesData = [
        'name' => 'Student Performance',
        'grades' => []
    ];

    // Fetch semesters
    $semesterQuery = "SELECT DISTINCT semester_name FROM semesters";
    $semesterResult = $conn->query($semesterQuery);

    if (!$semesterResult) {
        die("Semester query failed: " . $conn->error);
    }

    while ($semester = $semesterResult->fetch_assoc()) {
        $semesterName = $semester['semester_name'];
        $semesterGrades = [
            'semester' => $semesterName,
            'subjects' => []
        ];

        // Modified SQL query with proper aliases
        $gradesQuery = "
            SELECT s.subject_name, 
                   CONCAT(sg.test_score, '/20') AS test, 
                   CONCAT(sg.quiz_score, '/20') AS quiz, 
                   sg.final_grade
            FROM student_grades sg
            JOIN subjects s ON sg.subject_id = s.subject_id
            JOIN semesters sem ON sg.semester_id = sem.semester_id
            JOIN courses c ON sg.course_id = c.course_id
            WHERE sem.semester_name = ? AND c.programme = ?
        ";

        $stmt = $conn->prepare($gradesQuery);
        
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ss", $semesterName, $programme);
        
        if (!$stmt->execute()) {
            die("Execution failed: " . $stmt->error);
        }

        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $semesterGrades['subjects'][] = $row;
        }

        // Add GPA and CGPA
        $semesterGrades['subjects'][] = [
            'subject_name' => 'GPA', 
            'final_grade' => '3.50'
        ];
        $semesterGrades['subjects'][] = [
            'subject_name' => 'CGPA', 
            'final_grade' => '3.75'
        ];

        $gradesData['grades'][] = $semesterGrades;
    }

    return json_encode($gradesData);
}

echo fetchStudentGrades($conn, $programme);
$conn->close();
?>