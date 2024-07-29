<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// استرجاع معلومات المستخدم الحالية
include('includes/config.php');
$username = $_SESSION['username'];
$query = $conn->prepare("SELECT profile_image, display_name FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VAR Timer - VAR Simulator</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
        .container {
            text-align: center;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: auto; /* Center the container */
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .timer {
            font-size: 10em;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            font-family: 'Cairo', sans-serif;
        }
        .controls {
            margin-top: 20px;
            text-align: center;
        }
        .controls button {
            font-size: 1em;
            padding: 10px 15px;
            margin: 10px 5px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            display: inline-block;
            min-width: 120px;
        }
        #startStop {
            background-color: #3f51b5;
            color: #fff;
        }
        #reset {
            background-color: #f44336;
            color: #fff;
        }
        #toggleTable {
            background-color: #4caf50;
            color: #fff;
        }
        #exportExcel {
            background-color: #2196f3;
            color: #fff;
        }
        .fullscreen-btn {
            background-color: #3f4b55;
            color: #fff;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .popup-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 500px;
        }
        .popup-header {
            text-align: center;
            margin-bottom: 10px;
            font-size: 1.5em;
            font-weight: bold;
        }
        .popup-footer {
            text-align: center;
            margin-top: 20px;
        }
        .dropdown-container {
            margin-bottom: 20px;
        }
        .dropdown-container select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1.2em;
        }
        .popup-footer button {
            width: 45%;
            padding: 12px;
            margin: 5px;
            font-size: 1em;
            border-radius: 5px;
        }
        .btn-red {
            background-color: #f44336;
            color: #fff;
        }
        .btn-blue {
            background-color: #3f51b5;
            color: #fff;
        }
        .action-button {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1em;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 40px;
            user-select: none;
            width: 100%;
        }
        #recordTable {
            display: none;
            max-height: 500px;
            overflow: auto;
            transition: max-height 0.3s ease-out;
        }
        #recordTable.active {
            display: table;
        }
        .toggle-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
        }
        .toggle-container input {
            display: none;
        }
        .toggle-container label {
            background-color: #ccc;
            border-radius: 50px;
            cursor: pointer;
            height: 30px;
            position: relative;
            width: 60px;
            margin: 0 10px;
        }
        .toggle-container label::after {
            background-color: #fff;
            border-radius: 50%;
            content: '';
            height: 24px;
            left: 3px;
            position: absolute;
            top: 3px;
            transition: 0.3s;
            width: 24px;
        }
        .toggle-container input:checked + label {
            background-color: #4caf50;
        }
        .toggle-container input:checked + label::after {
            transform: translateX(30px);
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="logo">
        <img src="assets/images/logo.png" alt="VAR Simulator Light">
        <span>VAR SIMULATOR<br>LIGHT VERSION</span>
    </div>
    <ul class="menu">
        <li><a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
        <li class="dropdown">
            <a href="#" onclick="toggleDropdownMenu(event, 'projectsDropdown')" class="<?php echo (in_array($current_page, ['add_project.php', 'view_projects.php', 'edit_projects.php'])) ? 'active' : ''; ?>">Manage Projects</a>
            <ul id="projectsDropdown" class="dropdown-menu <?php echo (in_array($current_page, ['add_project.php', 'view_projects.php', 'edit_projects.php'])) ? 'show' : ''; ?>">
                <li><a href="add_project.php" class="<?php echo ($current_page == 'add_project.php') ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Add Project</a></li>
                <li><a href="view_projects.php" class="<?php echo ($current_page == 'view_projects.php') ? 'active' : ''; ?>"><i class="fas fa-eye"></i> View Projects</a></li>
                <li><a href="edit_projects.php" class="<?php echo ($current_page == 'edit_projects.php') ? 'active' : ''; ?>"><i class="fas fa-edit"></i> Edit Projects</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" onclick="toggleDropdownMenu(event, 'sessionsDropdown')" class="<?php echo (in_array($current_page, ['add_session.php', 'view_sessions.php', 'edit_sessions.php'])) ? 'active' : ''; ?>">Manage Sessions</a>
            <ul id="sessionsDropdown" class="dropdown-menu <?php echo (in_array($current_page, ['add_session.php', 'view_sessions.php', 'edit_sessions.php'])) ? 'show' : ''; ?>">
                <li><a href="add_session.php" class="<?php echo ($current_page == 'add_session.php') ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Add Session</a></li>
                <li><a href="view_sessions.php" class="<?php echo ($current_page == 'view_sessions.php') ? 'active' : ''; ?>"><i class="fas fa-eye"></i> View Sessions</a></li>
                <li><a href="edit_sessions.php" class="<?php echo ($current_page == 'edit_sessions.php') ? 'active' : ''; ?>"><i class="fas fa-edit"></i> Edit Sessions</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" onclick="toggleDropdownMenu(event, 'refereesDropdown')" class="<?php echo (in_array($current_page, ['add_referee.php', 'view_referees.php', 'edit_referees.php'])) ? 'active' : ''; ?>">Manage VAR Referees</a>
            <ul id="refereesDropdown" class="dropdown-menu <?php echo (in_array($current_page, ['add_referee.php', 'view_referees.php', 'edit_referees.php'])) ? 'show' : ''; ?>">
                <li><a href="add_referee.php" class="<?php echo ($current_page == 'add_referee.php') ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Add Referee</a></li>
                <li><a href="view_referees.php" class="<?php echo ($current_page == 'view_referees.php') ? 'active' : ''; ?>"><i class="fas fa-eye"></i> View Referees</a></li>
                <li><a href="edit_referee.php" class="<?php echo ($current_page == 'edit_referee.php') ? 'active' : ''; ?>"><i class="fas fa-edit"></i> Edit Referee</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" onclick="toggleDropdownMenu(event, 'operatorsDropdown')" class="<?php echo (in_array($current_page, ['add_operator.php', 'view_operators.php', 'edit_operators.php'])) ? 'active' : ''; ?>">Manage VAR Operators</a>
            <ul id="operatorsDropdown" class="dropdown-menu <?php echo (in_array($current_page, ['add_operator.php', 'view_operators.php', 'edit_operators.php'])) ? 'show' : ''; ?>">
                <li><a href="add_operator.php" class="<?php echo ($current_page == 'add_operator.php') ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Add Operator</a></li>
                <li><a href="view_operators.php" class="<?php echo ($current_page == 'view_operators.php') ? 'active' : ''; ?>"><i class="fas fa-eye"></i> View Operators</a></li>
                <li><a href="edit_operators.php" class="<?php echo ($current_page == 'edit_operators.php') ? 'active' : ''; ?>"><i class="fas fa-edit"></i> Edit Operators</a></li>
            </ul>
        </li>
        <li><a href="#">Manage Users</a></li>
        <li class="dropdown">
            <a href="#" onclick="toggleDropdownMenu(event, 'varTimerDropdown')" class="<?php echo ($current_page == 'var_timer.php') ? 'active' : ''; ?>">VAR Timer</a>
            <ul id="varTimerDropdown" class="dropdown-menu <?php echo ($current_page == 'var_timer.php') ? 'show' : ''; ?>">
                <li><a href="var_timer.php" class="<?php echo ($current_page == 'var_timer.php') ? 'active' : ''; ?>"><i class="fas fa-clock"></i> VAR Timer</a></li>
                <li><a href="add_referees.php" class="<?php echo ($current_page == 'add_referees.php') ? 'active' : ''; ?>"><i class="fas fa-user-plus"></i> Add Referees</a></li>
                <li><a href="add_groups.php" class="<?php echo ($current_page == 'add_groups.php') ? 'active' : ''; ?>"><i class="fas fa-users"></i> Add Groups</a></li>
                <li><a href="edit_referees.php" class="<?php echo ($current_page == 'edit_referees.php') ? 'active' : ''; ?>"><i class="fas fa-user-edit"></i> Edit Referees</a></li>
                <li><a href="timer_setting.php" class="<?php echo ($current_page == 'timer_setting.php') ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Timer Setting</a></li>
            </ul>
        </li>
        <li><a href="#">Settings</a></li>
    </ul>
    <div class="footer">
        <p>DEVELOPED BY RAMY GAMAL</p>
    </div>
</div>
<div class="main-content">
    <div class="header">
        <div class="notifications" onclick="toggleNotifications()">
            <i class="fas fa-bell"></i>
            <div class="notifications-popup">
                <div class="popup-header">
                    <span>No notifications</span>
                    <i class="fas fa-times" onclick="toggleNotifications()"></i>
                </div>
            </div>
        </div>
        <div class="user-info" onclick="toggleUserDropdown()">
            <span><?php echo $user['display_name']; ?></span>
            <img src="<?php echo $user['profile_image']; ?>" alt="Profile Image" id="profileImage" class="avatar">
            <div class="user-dropdown-menu">
                <a href="#" onclick="openProfileModal()"><i class="fas fa-user"></i> My Profile</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sign out</a>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="timer" id="display">00:00</div>
        <div class="controls">
            <button id="startStop" onclick="startStop()">Start</button>
            <button id="reset" onclick="reset()">Reset</button>
            <button id="toggleTable" onclick="toggleTable()">Toggle Table</button>
            <button id="exportExcel" onclick="exportToExcel()">Export to Excel</button>
            <button class="fullscreen-btn" onclick="toggleFullscreen()"><i class="fas fa-expand"></i> Fullscreen</button>
        </div>
        <div class="toggle-container">
            <input type="checkbox" id="togglePopup">
            <label for="togglePopup"></label>
            <span>VAR Additional time</span>
        </div>
        <table id="recordTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Referee</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Records will be inserted here dynamically -->
            </tbody>
        </table>
    </div>
</div>

<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeProfileModal()">&times;</span>
        <h2>My Profile</h2>
        <form id="profileForm">
            <div class="form-group">
                <label for="currentProfileImage">Current Profile Image</label>
                <img id="currentProfileImage" src="<?php echo $user['profile_image']; ?>" alt="Current Profile Image" width="100">
            </div>
            <div class="form-group">
                <label for="newProfileImage">New Profile Image</label>
                <input type="file" id="newProfileImage" name="newProfileImage" accept="image/*">
                <img id="previewImage" src="#" alt="Preview Image" style="display: none;" width="100">
            </div>
            <div class="form-group">
                <label for="displayName">Display Name</label>
                <input type="text" id="displayName" name="displayName" value="<?php echo $user['display_name']; ?>">
            </div>
            <div class="form-group">
                <label for="oldPassword">Old Password</label>
                <input type="password" id="oldPassword" name="oldPassword">
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="newPassword">
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword">
            </div>
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
<script>
    let timer;
    let startTime;
    let running = false;
    let totalTime = 0;
    let recordList = [];
    let enablePopup = true;

    document.getElementById('togglePopup').addEventListener('change', function() {
        enablePopup = !this.checked; // Invert the logic: enablePopup is true when checkbox is unchecked
    });

    function startStop() {
        if (running) {
            clearInterval(timer);
            running = false;
            if (enablePopup) {
                openPopup(); // Show popup window on stop if enabled
            }
            document.getElementById("startStop").textContent = "Start";
        } else {
            startTime = Date.now() - totalTime;
            timer = setInterval(updateDisplay, 1000);
            running = true;
            document.getElementById("startStop").textContent = "Stop";
        }
    }

    function reset() {
        clearInterval(timer); // Stop the timer
        running = false;
        totalTime = 0; // Reset total time
        startTime = Date.now(); // Reset start time to current time
        updateDisplay(); // Update display to 00:00
        document.getElementById("startStop").textContent = "Start"; // Update start/stop button text
    }

    function updateDisplay() {
        let currentTime = Date.now();
        totalTime = currentTime - startTime;
        let seconds = Math.floor((totalTime / 1000) % 60);
        let minutes = Math.floor((totalTime / (1000 * 60)) % 60);
        document.getElementById("display").textContent = formatTime(minutes) + ":" + formatTime(seconds);
    }

    function formatTime(time) {
        return time < 10 ? "0" + time : time;
    }

    function openPopup() {
        document.getElementById("popup").style.display = "flex";
    }

    function closePopup() {
        document.getElementById("popup").style.display = "none";
        let addButton = document.querySelector(".popup-footer .btn-blue");
        addButton.textContent = "Add";
        addButton.onclick = function() {
            addRecord();
        };
    }

    function addRecord() {
        let refereeName = document.getElementById("refereeSelect").value;
        let currentDate = new Date();
        recordList.push({
            date: currentDate,
            time: formatTime(Math.floor(totalTime / (1000 * 60))) + ":" + formatTime(Math.floor((totalTime / 1000) % 60)),
            referee: refereeName
        });
        renderRecords();
        closePopup();
    }

    function renderRecords() {
        let tableBody = document.querySelector("#recordTable tbody");
        tableBody.innerHTML = "";
        recordList.forEach((record, index) => {
            let row = document.createElement("tr");
            let dateCell = document.createElement("td");
            let timeCell = document.createElement("td");
            let refereeCell = document.createElement("td");
            let actionsCell = document.createElement("td");

            dateCell.textContent = record.date.toLocaleDateString();
            timeCell.textContent = record.time;
            refereeCell.textContent = record.referee;

            let editButton = document.createElement("button");
            editButton.textContent = "Edit";
            editButton.className = "action-button btn-blue";
            editButton.onclick = function() {
                editRecord(index);
            };

            let deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.className = "action-button btn-red";
            deleteButton.onclick = function() {
                deleteRecord(index);
            };

            actionsCell.appendChild(editButton);
            actionsCell.appendChild(deleteButton);

            row.appendChild(dateCell);
            row.appendChild(timeCell);
            row.appendChild(refereeCell);
            row.appendChild(actionsCell);

            tableBody.appendChild(row);
        });
    }

    function editRecord(index) {
        openPopup();
        document.getElementById("refereeSelect").value = recordList[index].referee;

        let addButton = document.querySelector(".popup-footer .btn-blue");
        addButton.textContent = "Update";
        addButton.onclick = function() {
            updateRecord(index);
        };
    }

    function updateRecord(index) {
        let refereeName = document.getElementById("refereeSelect").value;
        recordList[index].referee = refereeName;
        renderRecords();
        closePopup();
    }

    function deleteRecord(index) {
        recordList.splice(index, 1);
        renderRecords();
    }

    function toggleTable() {
        const table = document.getElementById("recordTable");
        table.classList.toggle("active");
    }

    function exportToExcel() {
        const worksheet = XLSX.utils.json_to_sheet(recordList);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Records');
        XLSX.writeFile(workbook, 'records.xlsx');
    }

    function toggleFullscreen() {
        const elem = document.documentElement;
        if (!document.fullscreenElement) {
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        $('.js-example-basic-single').select2();
        document.addEventListener('keydown', function(event) {
            if (event.code === 'Space') {
                startStop();
                event.preventDefault();
            }
        });
    });

    document.addEventListener('contextmenu', function(e) {
        e.preventDefault();
    });

    function toggleUserDropdown() {
        var dropdown = document.querySelector('.user-dropdown-menu');
        dropdown.classList.toggle('show');
    }

    function toggleDropdownMenu(event, menuId) {
        event.preventDefault();
        var dropdowns = document.querySelectorAll('.dropdown-menu');
        var menuLinks = document.querySelectorAll('.dropdown > a');
        
        dropdowns.forEach(function(dropdown) {
            if (dropdown.id !== menuId) {
                dropdown.classList.remove('show');
            }
        });
        
        menuLinks.forEach(function(link) {
            if (link.getAttribute('onclick').includes(menuId)) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        var dropdown = document.getElementById(menuId);
        dropdown.classList.toggle('show');
    }

    function toggleNotifications() {
        var popup = document.querySelector('.notifications-popup');
        popup.classList.toggle('show');
    }

    function openProfileModal() {
        var modal = document.getElementById('profileModal');
        modal.style.display = 'block';
    }

    function closeProfileModal() {
        var modal = document.getElementById('profileModal');
        modal.style.display = 'none';
    }

    document.getElementById('newProfileImage').addEventListener('change', function(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var previewImage = document.getElementById('previewImage');
            previewImage.src = reader.result;
            previewImage.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    });

    document.getElementById('profileForm').addEventListener('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_profile.php', true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    document.getElementById('currentProfileImage').src = response.newProfileImage;
                    document.getElementById('profileImage').src = response.newProfileImage;
                    document.querySelector('.user-info span').textContent = response.displayName;
                    closeProfileModal();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Profile updated successfully!',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    addNotification('Profile updated successfully.');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.error,
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            }
        };
        xhr.send(formData);
    });

    window.onclick = function(event) {
        if (event.target == document.getElementById('profileModal')) {
            closeProfileModal();
        }
    }

    function getGreeting() {
        var now = new Date();
        var hours = now.getHours();
        var greeting;
        var icon;

        if (hours < 12) {
            greeting = "Good Morning";
            icon = "fas fa-sun";
        } else if (hours < 18) {
            greeting = "Good Afternoon";
            icon = "fas fa-cloud-sun";
        } else {
            greeting = "Good Evening";
            icon = "fas fa-moon";
        }

        return '<i class="' + icon + '"></i> ' + greeting;
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('greeting').innerHTML = getGreeting();
    });

    function addNotification(message) {
        var notificationPopup = document.querySelector('.notifications-popup');
        var notificationCount = document.querySelector('.notifications i');
        var notificationSpan = document.querySelector('.popup-header span');

        var newNotification = document.createElement('div');
        newNotification.classList.add('notification-item');
        newNotification.textContent = message;

        notificationPopup.appendChild(newNotification);

        var count = parseInt(notificationCount.dataset.count) || 0;
        count++;
        notificationCount.dataset.count = count;
        notificationCount.classList.add('has-notifications');
        notificationSpan.textContent = count + ' notifications';

        notificationPopup.classList.add('show');
    }
</script>
</body>
</html>
