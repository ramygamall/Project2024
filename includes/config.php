<?php
$servername = "localhost";
$username = "ramygamal_varonlineusers";
$password = "Ramy@159357";
$dbname = "ramygamal_varonline";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
