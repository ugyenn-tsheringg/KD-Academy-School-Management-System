<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Grades Table</title>
    <style>
        /* Your existing CSS styles */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span id="studentName"></span></h1>
            <p>Select a semester to view your grades:</p>
        </div>
        <div class="dropdown-container">
            <select id="semesterDropdown" onchange="displayOptions()">
                <option value="">Select a semester</option>
            </select>
        </div>
        <div class="options-container" id="optionsContainer">
            <button onclick="showPerformance('test')">Assignment</button>
            <button onclick="showPerformance('quiz')">Project</button>
            <button onclick="showPerformance('finalGrade')">Final Grade</button>
        </div>
        <div class="grades-section" id="gradesSection">
            <h3 id="semesterTitle"></h3>
            <table id="gradesTable">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Grades will be dynamically inserted here -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let studentData = null;

        // Fetch student data from server on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('fetch_grades.php')
                .then(response => response.json())
                .then(data => {
                    studentData = data;
                    
                    // Display student name
                    document.getElementById("studentName").textContent = studentData.name;

                    // Populate dropdown menu
                    const dropdown = document.getElementById("semesterDropdown");
                    dropdown.innerHTML = '<option value="">Select a semester</option>'; // Clear previous options
                    studentData.grades.forEach(semester => {
                        const option = document.createElement("option");
                        option.value = semester.semester;
                        option.textContent = semester.semester;
                        dropdown.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching student data:', error);
                    alert('Failed to load student data');
                });
        });

        // Display options (Test, Quiz, Final Grade) after selecting a semester
        function displayOptions() {
            const dropdown = document.getElementById("semesterDropdown");
            const selectedSemester = dropdown.value;
            const optionsContainer = document.getElementById("optionsContainer");

            if (selectedSemester) {
                optionsContainer.style.display = "block";
            } else {
                optionsContainer.style.display = "none";
                document.getElementById("gradesSection").style.display = "none";
            }
        }

        // Show performance based on the selected option (Test, Quiz, Final Grade)
        function showPerformance(type) {
            const dropdown = document.getElementById("semesterDropdown");
            const selectedSemester = dropdown.value;
            const semesterData = studentData.grades.find(s => s.semester === selectedSemester);

            if (semesterData) {
                const gradesSection = document.getElementById("gradesSection");
                const gradesTableBody = document.querySelector("#gradesTable tbody");
                document.getElementById("semesterTitle").textContent = `${semesterData.semester} - ${type}`;
                gradesTableBody.innerHTML = "";

                semesterData.subjects.forEach(subject => {
                    // Skip GPA and CGPA for Test and Quiz
                    if ((type === 'test' || type === 'quiz') && (subject.name === 'GPA' || subject.name === 'CGPA')) {
                        return;
                    }

                    // Only show GPA and CGPA for Final Grade
                    if (type === 'finalGrade' || (subject.name !== 'GPA' && subject.name !== 'CGPA')) {
                        const row = document.createElement("tr");
                        const subjectCell = document.createElement("td");
                        const gradeCell = document.createElement("td");

                        subjectCell.textContent = subject.subject_name || subject.name;

                        if (type === 'test') {
                            gradeCell.textContent = subject.test;
                        } else if (type === 'quiz') {
                            gradeCell.textContent = subject.quiz;
                        } else if (type === 'finalGrade') {
                            gradeCell.textContent = subject.finalGrade || subject.final_grade;
                        }

                        row.appendChild(subjectCell);
                        row.appendChild(gradeCell);
                        gradesTableBody.appendChild(row);
                    }
                });

                gradesSection.style.display = "block";
            } else {
                document.getElementById("gradesSection").style.display = "none";
            }
        }
    </script>
</body>
</html>