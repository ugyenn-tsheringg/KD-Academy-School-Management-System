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
                        <a href="admin_dashboard.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-link active">
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