<?php
session_start();
include('includes/config.php');

$response = array('success' => false, 'error' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $displayName = $_POST['displayName'];
    $profileImage = $_FILES['newProfileImage'];
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    $uploadFile = '';
    if ($profileImage['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'assets/users_img/';
        $uploadFile = $uploadDir . basename($profileImage['name']);
        
        if (!move_uploaded_file($profileImage['tmp_name'], $uploadFile)) {
            $response['error'] = 'File upload failed';
            echo json_encode($response);
            exit;
        }
    }

    if (!empty($oldPassword) && !empty($newPassword) && !empty($confirmPassword)) {
        if ($newPassword !== $confirmPassword) {
            $response['error'] = 'New passwords do not match';
            echo json_encode($response);
            exit;
        }

        $query = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();
        $user = $result->fetch_assoc();
        $query->close();

        if ($oldPassword !== $user['password']) {
            $response['error'] = 'Old password is incorrect';
            echo json_encode($response);
            exit;
        }

        $query = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $query->bind_param("ss", $newPassword, $username);
        if (!$query->execute()) {
            $response['error'] = 'Password update failed';
            echo json_encode($response);
            exit;
        }
        $query->close();
    }

    if (!empty($uploadFile)) {
        $query = $conn->prepare("UPDATE users SET display_name = ?, profile_image = ? WHERE username = ?");
        $query->bind_param("sss", $displayName, $uploadFile, $username);
    } else {
        $query = $conn->prepare("UPDATE users SET display_name = ? WHERE username = ?");
        $query->bind_param("ss", $displayName, $username);
    }

    if ($query->execute()) {
        $response['success'] = true;
        $response['newProfileImage'] = $uploadFile;
        $response['displayName'] = $displayName;
        $_SESSION['display_name'] = $displayName;
        if (!empty($uploadFile)) {
            $_SESSION['profile_image'] = $uploadFile;
        }
    } else {
        $response['error'] = 'Database update failed';
    }
    $query->close();
}

echo json_encode($response);
$conn->close();
?>
