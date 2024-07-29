<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('includes/config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectName = $_POST['projectName'];
    $topics = $_POST['topics'];
    $assignUsers = $_POST['assignUsers'];

    $stmt = $conn->prepare("INSERT INTO projects (name, topics, assign_users) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $projectName, $topics, $assignUsers);

    if ($stmt->execute()) {
        $projectId = $stmt->insert_id;
        $stmt->close();

        echo json_encode(['success' => true, 'project' => ['id' => $projectId, 'name' => $projectName, 'topics' => $topics, 'assign' => $assignUsers]]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to save project.']);
    }
}
?>
