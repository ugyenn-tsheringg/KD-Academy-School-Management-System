


//User profile icon click toggle
function togglePopup() {
  const popup = document.getElementById('userPopup');
  popup.classList.toggle('active');
}

document.addEventListener('click', (event) => {
  const popup = document.getElementById('userPopup');
  const userIcon = document.getElementById('userIcon');

  // Close the popup if the click is outside the popup or the user icon
  if (
    popup.classList.contains('active') &&
    !popup.contains(event.target) &&
    event.target !== userIcon &&
    !userIcon.contains(event.target)
  ) {
    popup.classList.remove('active');
  }
});

document.getElementById('userIcon').addEventListener('click', (event) => {
  event.stopPropagation(); // Prevent the event from closing the popup when clicking the icon
  togglePopup();
});

//Register Course - Student
function registerCourse() {
  const courseSelect = document.getElementById('course');
  const selectedCourse = courseSelect.options[courseSelect.selectedIndex].text;
  if (selectedCourse === "Select a Course") {
      alert("Please select a course to register.");
      return;
  }
  const registeredMessage = document.getElementById('registeredMessage');
  registeredMessage.textContent = `Registered: ${selectedCourse}`;
}

//Student - Class Attendance
document.addEventListener('DOMContentLoaded', function () {
  const body = document.querySelector('body');
  const sidebar = body.querySelector('nav');
  const toggle = body.querySelector(".toggle");
  const searchBtn = body.querySelector(".search-box");
  const modeSwitch = body.querySelector(".toggle-switch");
  const modeText = body.querySelector(".mode-text");

  // Add event listener to the toggle button
  if (toggle) {
      toggle.addEventListener("click", () => {
          sidebar.classList.toggle("close");
      });
  } else {
      console.error("Toggle button not found in the DOM.");
  }

  // Add event listener to the search button
  if (searchBtn) {
      searchBtn.addEventListener("click", () => {
          sidebar.classList.remove("close");
      });
  } else {
      console.error("Search button not found in the DOM.");
  }

  // Add event listener to the mode switch
  if (modeSwitch) {
      modeSwitch.addEventListener("click", () => {
          body.classList.toggle("dark");

          if (body.classList.contains("dark")) {
              modeText.innerText = "Light mode";
          } else {
              modeText.innerText = "Dark mode";
          }
      });
  } else {
      console.error("Mode switch not found in the DOM.");
  }

  // User profile icon click toggle
  function togglePopup() {
      const popup = document.getElementById('userPopup');
      popup.classList.toggle('active');
  }

  document.addEventListener('click', (event) => {
      const popup = document.getElementById('userPopup');
      const userIcon = document.getElementById('userIcon');

      // Close the popup if the click is outside the popup or the user icon
      if (
          popup.classList.contains('active') &&
          !popup.contains(event.target) &&
          event.target !== userIcon &&
          !userIcon.contains(event.target)
      ) {
          popup.classList.remove('active');
      }
  });

  if (document.getElementById('userIcon')) {
      document.getElementById('userIcon').addEventListener('click', (event) => {
          event.stopPropagation(); // Prevent the event from closing the popup when clicking the icon
          togglePopup();
      });
  } else {
      console.error("User icon not found in the DOM.");
  }

  // Register Course - Student
  function registerCourse() {
      const courseSelect = document.getElementById('course');
      const selectedCourse = courseSelect.options[courseSelect.selectedIndex].text;
      if (selectedCourse === "Select a Course") {
          alert("Please select a course to register.");
          return;
      }
      const registeredMessage = document.getElementById('registeredMessage');
      registeredMessage.textContent = `Registered: ${selectedCourse}`;
  }