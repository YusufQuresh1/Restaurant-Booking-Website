<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function sanitise_input($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        $_POST[$key] = sanitise_input($value);
    }
}

// Custom error handler to log errors
function handle_error($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
    if (ini_get('display_errors')) {
        echo "<p style='color: red;'>An error occurred. Please try again later.</p>";
    }
}
set_error_handler("handle_error");

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("Referrer-Policy: no-referrer");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Permissions-Policy: accelerometer=(), camera=(), geolocation=(), microphone=(), interest-cohort=()");

$signupSuccessMessage = "";
$signupErrorMessage = "";
$loginErrorMessage = "";

include('db_connect.php');

// Check if any users exist in the database
$userCountQuery = "SELECT COUNT(*) FROM Accounts";
$result = $conn->query($userCountQuery);
$row = $result->fetch_array();
$noUsers = ($row[0] == 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['signup']) || isset($_POST['staff-signup']))) {
    $email = trim($_POST['email']);
    $pword = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $isStaff = isset($_POST['is_staff']) ? 1 : 0;

    if (strlen($pword) < 8 || !preg_match('/[A-Z]/', $pword) || !preg_match('/[a-z]/', $pword) || !preg_match('/[0-9]/', $pword)) {
        $signupErrorMessage = "Password must be at least 8 characters long and include an uppercase letter, a lowercase letter, and a number.";
    } elseif ($pword !== $confirmPassword) {
        $signupErrorMessage = "Passwords do not match!";
    } else {
        $hashedPassword = password_hash($pword, PASSWORD_DEFAULT);

        // Check if the email already exists
        $stmt = $conn->prepare("SELECT id FROM Accounts WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $signupErrorMessage = "An account with this email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Accounts (email, password, is_staff) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $email, $hashedPassword, $isStaff);

            if ($stmt->execute()) {
                $signupSuccessMessage = "Account created successfully! Please log in.";

                $email_subject = "Welcome to Lancaster's Restaurant!";
                $email_body = "Dear User,\n\nThank you for creating an account with Lancaster's Restaurant.\n\n" .
                              "You can now log in and start booking tables for our services.\n\n" .
                              "Best regards,\nLancaster's Restaurant Team.";

                $email_content = "Subject: $email_subject\r\n" .
                                 "To: $email\r\n" .
                                 "From: welcome@lancasters.com\r\n" .
                                 "Content-Type: text/plain; charset=UTF-8\r\n\r\n" .
                                 "$email_body";

                $email_filename = "welcome_" . time() . ".eml";
                file_put_contents($email_filename, $email_content);

                $_SESSION['signup_email_link'] = "<a href='$email_filename' download style='color: #ddd;'>Download Account Confirmation Email</a>";

                header("Location: login.php?section=login");
                exit();
            } else {
                $signupErrorMessage = "Error creating account: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pword = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, password, is_staff FROM Accounts WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashedPassword, $isStaff);
        $stmt->fetch();

        if (password_verify($pword, $hashedPassword)) {
            $_SESSION['user_id'] = $userId;
            session_regenerate_id(true); // Security measure to prevent session fixation

            // Redirect based on account type
            if ($isStaff) {
                header("Location: staffdashboard.php");
            } else {
                header("Location: booktable.php");
            }
            exit();
        } else {
            $loginErrorMessage = "Invalid email or password.";
        }
    } else {
        $loginErrorMessage = "Invalid email or password.";
    }
    $stmt->close();
}


// logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lancaster’s Restaurant</title>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' data: https://*; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; font-src 'self'; connect-src 'self';">

    <script>
        function showSection(section) {
            document.getElementById('signup').style.display = (section === 'signup') ? 'block' : 'none';
            document.getElementById('login').style.display = (section === 'login') ? 'block' : 'none';
            document.getElementById('staff-signup').style.display = (section === 'staff-signup') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section');

            <?php if ($noUsers): ?>
                showSection('staff-signup');
            <?php else: ?>
                showSection(section === 'signup' || section === 'login' ? section : 'login');
            <?php endif; ?>
        });
    </script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include('navbar.php'); ?>

<main>
    <section id="staff-signup" style="display: none;">
        <img src="images/logos/Lancaster's-logos_white_cropped.png" alt="Home Page logo" style="height: 60px;">
        <h2>Staff Sign-Up</h2>

        <?php if (!empty($signupErrorMessage) && isset($_POST['staff-signup'])): ?>
            <p style="color: red;"><?php echo htmlspecialchars($signupErrorMessage); ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <input type="hidden" name="staff-signup" value="1">
            <input type="hidden" name="is_staff" value="1">
            <label for="staff-email">Email:</label>
            <input type="email" id="staff-email" name="email" required>

            <label for="staff-password">Password:</label>
            <input type="password" id="staff-password" name="password" required>

            <label for="staff-confirm-password">Confirm Password:</label>
            <input type="password" id="staff-confirm-password" name="confirm_password" required>

            <button type="submit">Create Staff Account</button>
        </form>
    </section>

    <section id="signup" style="display: none;">
        <img src="images/logos/Lancaster's-logos_white_cropped.png" alt="Home Page logo" style="height: 60px;">
        <h2>Create an Account</h2>

        <?php if (!empty($signupErrorMessage) && isset($_POST['signup'])): ?>
            <p style="color: red;"><?php echo $signupErrorMessage; ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <input type="hidden" name="signup">
            <label for="signup-email">Email:</label>
            <input type="email" id="signup-email" name="email" required>

            <label for="signup-password">Password:</label>
            <input type="password" id="signup-password" name="password" required>

            <label for="signup-confirm-password">Confirm Password:</label>
            <input type="password" id="signup-confirm-password" name="confirm_password" required>

            <button type="submit">Create an Account</button>
        </form>
        <br>

        <a href="login.php?section=login">Already have an account? Login</a>
    </section>

    <!-- Login Section -->
    <section id="login" style="display: none;">
        <img src="images/logos/Lancaster's-logos_white_cropped.png" alt="Home Page logo" style="height: 60px;">
        <h2>Login</h2>

        <?php if (!empty($loginErrorMessage)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($loginErrorMessage); ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['signup_email_link'])): ?>
            <p style="color: #ddd;"><?php echo $_SESSION['signup_email_link']; ?></p>
            <?php unset($_SESSION['signup_email_link']);?>
        <?php endif; ?>

        <form action="" method="post">
            <input type="hidden" name="login">
            <label for="login-email">Email:</label>
            <input type="email" id="login-email" name="email" required>

            <label for="login-password">Password:</label>
            <input type="password" id="login-password" name="password" required>

            <button type="submit">Log In</button>
        </form>
        <br>

        <a href="javascript:void(0)" onclick="window.location.href='login.php?section=signup'">Haven't got an account? Create an account</a>
    </section>

</main>

<?php
echo $twig->render('footer.twig');
?>
</body>
</html>
