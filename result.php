<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles.css">
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
                    <li class="nav-link active">
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
        <div class="upper-text">Academic Results</div>
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
            <h1><span id="studentName"></span></h1>
            <p>Select a semester to view your grades:</p>
        </div>
        <div class="dropdown-container">
            <select id="semesterDropdown" onchange="displayOptions()">
                <option value="">Select a semester</option>
            </select>
        </div>
        <div class="options-container" id="optionsContainer">
            <button onclick="showPerformance('Test')">Test</button>
            <button onclick="showPerformance('Quiz')">Quiz</button>
            <button onclick="showPerformance('Final Grade')">Final Grade</button>
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

    </section>

    <script src="main.js"></script>
</body>
</html>