<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$isStaff = false;

if ($isLoggedIn) { //check if staff
    include('db_connect.php');
    $stmt = $conn->prepare("SELECT is_staff FROM Accounts WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($isStaff);
    $stmt->fetch();
    $stmt->close();
}

// Twig
require_once 'twig.php';

echo $twig->render('navbar.twig', [
    'isLoggedIn' => $isLoggedIn,
    'isStaff' => $isStaff,
]);
?>
