<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


function handle_error($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile on line $errline", 0);
    if (ini_get('display_errors')) {
        echo "<p style='color: red;'>An unexpected error occurred. Please try again later.</p>";
    }
}
set_error_handler("handle_error");


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?section=login");
    exit();
}

include('db_connect.php');

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT email, is_staff, password FROM Accounts WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($email, $is_staff, $hashed_password);
$stmt->fetch();
$stmt->close();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!password_verify($current_password, $hashed_password)) {
        $message = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
        $message = "Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.";
    } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE Accounts SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_hashed_password, $user_id);
        if ($stmt->execute()) {
                session_destroy();
                header("Location: login.php");
                exit();
        } else {
            $message = "Error updating password. Please try again.";
        }
        $stmt->close();
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
    header("Location: index.php");
    exit();
    
}

$pastBookings = [];
$stmt = $conn->prepare("
    SELECT service, reservation_date, reservation_time, guests, additional_info 
    FROM Reservations 
    WHERE user_id = ? 
      AND (reservation_date < CURDATE() 
           OR (reservation_date = CURDATE() AND reservation_time < CURTIME()))
    ORDER BY reservation_date DESC, reservation_time DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $pastBookings[] = $row;
}
$stmt->close();

$upcomingBookings = [];
$stmt = $conn->prepare("
    SELECT service, reservation_date, reservation_time, guests, additional_info 
    FROM Reservations 
    WHERE user_id = ? 
      AND (reservation_date > CURDATE() 
           OR (reservation_date = CURDATE() AND reservation_time >= CURTIME()))
    ORDER BY reservation_date ASC, reservation_time ASC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $upcomingBookings[] = $row;
}
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' data: https://*; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; font-src 'self'; connect-src 'self';">
    <link rel="stylesheet" href="styles.css">
    <script>
function togglePasswordForm(event) {
    event.preventDefault(); 
    const passwordForm = document.getElementById('passwordForm');
    passwordForm.style.display = passwordForm.style.display === 'none' || !passwordForm.style.display ? 'block' : 'none';
}


    </script>
</head>
<body>
    <?php include('navbar.php'); ?>
    <main class="account-page">
        <h1 style="text-align: center; color: #e4c176; padding: 20px ">My Account</h1>
        <section class="account-details">
        <h2><strong>My Details</strong></h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></p>
        <a href="#" class="toggle-password-link" onclick="togglePasswordForm(event)">Change Password</a>

        <section class="password-change">
            <form id="passwordForm" class="password-form" method="post" style="display: none;">
                <?php if ($message): ?>
                    <p class="message" style="color: red; text-align: center;"><?php echo htmlspecialchars($message); ?></p>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            document.getElementById('passwordForm').style.display = 'block';
                        });
                    </script>
                <?php endif; ?>

                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
                
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
                
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                
                <button type="submit" name="change_password" class="submit-btn">Update Password</button>
            </form>
        </section>
        </section>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <section class="bookings">
            <h2>My Bookings</h2>
            <div class="booking-list">
                <h3>Upcoming Bookings</h3>
                <?php if (count($upcomingBookings) > 0): ?>
                    <ul>
                        <?php foreach ($upcomingBookings as $booking): ?>
                            <li>
                                <strong>Service:</strong> <?php echo htmlspecialchars($booking['service'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?><br>
                                <strong>Date:</strong> <?php echo htmlspecialchars($booking['reservation_date'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?><br>
                                <strong>Time:</strong> <?php echo htmlspecialchars($booking['reservation_time'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?><br>
                                <strong>Guests:</strong> <?php echo htmlspecialchars($booking['guests'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?><br>
                                <strong>Additional Info:</strong> <?php echo htmlspecialchars($booking['additional_info'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No upcoming bookings found.</p>
                <?php endif; ?>
            </div>
        </section>

        <section class="logout">
            <form method="post">
                <button type="submit" name="logout" class="logout-btn">Logout</button>
            </form>
        </section>
    </main>
    <?php
        echo $twig->render('footer.twig');
    ?>
</body>
</html>

