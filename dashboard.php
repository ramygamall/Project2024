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
    <title>Dashboard - VAR Simulator</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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
            <a href="#" onclick="toggleDropdownMenu(event, 'videosDropdown')" class="<?php echo (in_array($current_page, ['add_video.php', 'view_videos.php', 'edit_videos.php'])) ? 'active' : ''; ?>">Manage Videos</a>
            <ul id="videosDropdown" class="dropdown-menu <?php echo (in_array($current_page, ['add_video.php', 'view_videos.php', 'edit_videos.php'])) ? 'show' : ''; ?>">
                <li><a href="add_video.php" class="<?php echo ($current_page == 'add_video.php') ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Add Video</a></li>
                <li><a href="view_videos.php" class="<?php echo ($current_page == 'view_videos.php') ? 'active' : ''; ?>"><i class="fas fa-eye"></i> View Videos</a></li>
                <li><a href="edit_videos.php" class="<?php echo ($current_page == 'edit_videos.php') ? 'active' : ''; ?>"><i class="fas fa-edit"></i> Edit Videos</a></li>
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
            <a href="#" onclick="toggleDropdownMenu(event, 'varTimerDropdown')" class="<?php echo (in_array($current_page, ['var_timer.php', 'add_referees.php', 'add_groups.php', 'edit_referees.php', 'timer_setting.php'])) ? 'active' : ''; ?>">VAR Timer</a>
            <ul id="varTimerDropdown" class="dropdown-menu <?php echo (in_array($current_page, ['var_timer.php', 'add_referees.php', 'add_groups.php', 'edit_referees.php', 'timer_setting.php'])) ? 'show' : ''; ?>">
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
    <div class="dashboard">
        <h2><span id="greeting"></span>, <?php echo $user['display_name']; ?>!</h2>
        <div class="cards">
            <div class="card-row">
                <div class="card" style="background-color: #d1e7ff;">
                    <div class="icon"><i class="fas fa-project-diagram"></i></div>
                    <div class="card-content">
                        <h3>PROJECTS</h3>
                        <p>07</p>
                    </div>
                </div>
                <div class="card" style="background-color: #ffe5e5;">
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <div class="card-content">
                        <h3>SESSIONS</h3>
                        <p>12</p>
                    </div>
                </div>
                <div class="card" style="background-color: #d5d5d5;">
                    <div class="icon"><i class="fas fa-video"></i></div>
                    <div class="card-content">
                        <h3>VIDEOS</h3>
                        <p>45</p>
                    </div>
                </div>
            </div>
            <div class="card-row">
                <div class="card" style="background-color: #e5ffe5;">
                    <div class="icon"><i class="fas fa-user-shield"></i></div>
                    <div class="card-content">
                        <h3>VAR REFEREES</h3>
                        <p>36</p>
                    </div>
                </div>
                <div class="card" style="background-color: #e5f2ff;">
                    <div class="icon"><i class="fas fa-desktop"></i></div>
                    <div class="card-content">
                        <h3>VAR OPERATORS</h3>
                        <p>11</p>
                    </div>
                </div>
                <div class="card" style="background-color: #ffffe5;">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div class="card-content">
                        <h3>USERS</h3>
                        <p>06</p>
                    </div>
                </div>
            </div>
        </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
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
                    document.getElementById('profileImage').src = response.newProfileImage; // تحديث الصورة في الهيدر
                    document.querySelector('.user-info span').textContent = response.displayName;
                    closeProfileModal();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Profile updated successfully!',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    // تحديث الإشعارات
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

        // إنشاء عنصر جديد للإشعار
        var newNotification = document.createElement('div');
        newNotification.classList.add('notification-item');
        newNotification.textContent = message;

        // إضافة الإشعار إلى القائمة
        notificationPopup.appendChild(newNotification);

        // تحديث عدد الإشعارات
        var count = parseInt(notificationCount.dataset.count) || 0;
        count++;
        notificationCount.dataset.count = count;
        notificationCount.classList.add('has-notifications');
        notificationSpan.textContent = count + ' notifications';

        // عرض الإشعارات
        notificationPopup.classList.add('show');
    }
</script>
</body>
</html>
