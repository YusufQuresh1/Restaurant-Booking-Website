<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Custom error handling
function handle_error($errno, $errstr, $errfile, $errline)
{

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


include('db_connect.php');

if (isset($_GET['fetch'])) {
    $service = isset($_GET['service']) ? htmlspecialchars($_GET['service'], ENT_QUOTES, 'UTF-8') : '';
    $guests = isset($_GET['guests']) ? intval($_GET['guests']) : 0;

    if ($_GET['fetch'] == 'dates') {
        $tables_required = ceil($guests / 2);
        $available_dates = getAvailableDates($conn, $service, $tables_required);
        echo json_encode($available_dates);
        exit;
    }

    if ($_GET['fetch'] == 'times' && isset($_GET['date'])) {
        $date = htmlspecialchars($_GET['date'], ENT_QUOTES, 'UTF-8');
        $tables_required = ceil($guests / 2);
        $available_times = getAvailableTimes($conn, $service, $date, $tables_required);
        echo json_encode($available_times);
        exit;
    }
}

$reservation_success = false;
$calendar_link = '';
$email_link = '';

$reservation_success = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : NULL;
    $service = $_POST['service'];
    $reservation_date = $_POST['reservation_date'];
    $reservation_time = $_POST['reservation_time'];
    $guests = $_POST['guests'];
    $additional_info = !empty($_POST['additional_info']) ? $_POST['additional_info'] : NULL;

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT email FROM Accounts WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($email);
        $stmt->fetch();
        $stmt->close();
    } else {
        $user_id = NULL;
        $email = $_POST['email'];
    }

    if (empty($email)) {
        $error_message = "Error: Email is required.";
    } else {
        // Check for duplicate booking
        $stmt_check = $conn->prepare("
            SELECT COUNT(*) 
            FROM Reservations 
            WHERE email = ? 
              AND service = ? 
              AND reservation_date = ? 
        ");
        $stmt_check->bind_param("sss", $email, $service, $reservation_date);
        $stmt_check->execute();
        $stmt_check->bind_result($existing_bookings);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($existing_bookings > 0) {
            $error_message = "A booking with this email already exists for this service for the selected date.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Reservations (user_id, email, name, phone, service, reservation_date, reservation_time, guests, additional_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssss", $user_id, $email, $name, $phone, $service, $reservation_date, $reservation_time, $guests, $additional_info);

            if ($stmt->execute()) {
                $reservation_success = true;
            
                // Create eml and calander files
                $email_subject = "Your Reservation Confirmation at Lancaster's";
                $email_body = "Dear $name,\n\nThank you for booking at Lancaster's Restaurant.\n\n" .
                              "Details of your reservation:\n" .
                              "Service: $service\n" .
                              "Date: $reservation_date\n" .
                              "Time: $reservation_time\n" .
                              "Guests: $guests\n\n" .
                              "We look forward to welcoming you!\n\nBest regards,\nLancaster's Restaurant.";
            
                $email_content = "Subject: $email_subject\r\n" .
                                 "To: $email\r\n" .
                                 "From: reservations@lancasters.com\r\n" .
                                 "Content-Type: text/plain; charset=UTF-8\r\n\r\n" .
                                 "$email_body";
            
                $email_filename = "reservation_" . time() . ".eml";
                file_put_contents($email_filename, $email_content);
                $email_link = "<a href='$email_filename' download>Download Confirmation Email</a>";
            
                $ics_content = "BEGIN:VCALENDAR\n";
                $ics_content .= "VERSION:2.0\n";
                $ics_content .= "BEGIN:VEVENT\n";
                $ics_content .= "UID:" . uniqid() . "@lancasters.com\n";
                $ics_content .= "DTSTAMP:" . date('Ymd\THis\Z') . "\n";
                $ics_content .= "DTSTART:" . date('Ymd\THis', strtotime("$reservation_date $reservation_time")) . "\n";
                $ics_content .= "DTEND:" . date('Ymd\THis', strtotime("$reservation_date $reservation_time +2 hours")) . "\n";
                $ics_content .= "SUMMARY:Reservation at Lancaster's Restaurant\n";
                $ics_content .= "DESCRIPTION:Table reservation for $guests guests for $service.\n";
                $ics_content .= "LOCATION:Lancaster's Restaurant, 52 Haymarket, London, SW1Y 4RP\n";
                $ics_content .= "END:VEVENT\n";
                $ics_content .= "END:VCALENDAR";
            
                $ics_filename = "reservation_" . uniqid() . ".ics";
                file_put_contents($ics_filename, $ics_content);
                $calendar_link = "<a href='$ics_filename' download>Download Calendar File</a>";
            
            

                // Calculate tables required based off party size
                $tables_required = ceil($guests / 2);

                // Determine the service ID for the selected date and service
                $stmt_service = $conn->prepare("SELECT service_id FROM Services WHERE name = ? AND date = ?");
                $stmt_service->bind_param("ss", $service, $reservation_date);
                $stmt_service->execute();
                $stmt_service->bind_result($service_id);
                $stmt_service->fetch();
                $stmt_service->close();

                if ($service_id) {
                    // Reduce the number of tables in the Service_Times table
                    $start_time = date('H:i:s', strtotime($reservation_time));
                    $end_time = date('H:i:s', strtotime($reservation_time . ' +2 hours'));

                    $stmt_update = $conn->prepare("UPDATE Service_Times 
                                                   SET num_of_tables = num_of_tables - ? 
                                                   WHERE service_id = ? AND time >= ? AND time < ?");
                    $stmt_update->bind_param("iiss", $tables_required, $service_id, $start_time, $end_time);
                    $stmt_update->execute();
                    $stmt_update->close();
                } else {
                    $error_message = "Error: Service not found for the selected date and service.";
                }
                $_SESSION['email_link'] = $email_link;
                $_SESSION['calendar_link'] = $calendar_link;
                header("Location: {$_SERVER['PHP_SELF']}?success=true");
                exit();

            } else {
                $error_message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dismiss_prompt'])) {
    $_SESSION['dismiss_prompt'] = true;
    echo json_encode(['success' => true]);
    exit();
}


function getAvailableDates($conn, $service, $guests) {
    $tables_required = ceil($guests / 2);
    $current_time = date('H:i:s');
    $stmt = $conn->prepare("
        SELECT DISTINCT s.date
        FROM Services s
        JOIN Service_Times st ON s.service_id = st.service_id
        WHERE s.name = ? 
          AND st.num_of_tables >= ? 
          AND (s.date > CURDATE() OR (s.date = CURDATE() AND EXISTS (
              SELECT 1
              FROM Service_Times st2
              WHERE st2.service_id = s.service_id 
                AND st2.num_of_tables >= ? 
                AND st2.time > ?
          )))
    ");
    $stmt->bind_param("siis", $service, $tables_required, $tables_required, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();

    $dates = [];
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['date'];
    }
    $stmt->close();

    return empty($dates) ? [] : $dates;
}


function getAvailableTimes($conn, $service, $date, $tables_required) {
    // times with enough tables on the selected date
    $stmt = $conn->prepare("SELECT DATE_FORMAT(st.time, '%H:%i') AS time, st.num_of_tables
                            FROM Service_Times st
                            JOIN Services s ON s.service_id = st.service_id
                            WHERE s.name = ? AND s.date = ? AND st.num_of_tables >= ?");
    $stmt->bind_param("ssi", $service, $date, $tables_required);
    $stmt->execute();
    $result = $stmt->get_result();

    $available_times = [];

    while ($row = $result->fetch_assoc()) {
        if ($row['num_of_tables'] >= $tables_required) {
            $available_times[] = $row['time'];
        }
    }

    $stmt->close();

    if (count($available_times) === 0) {
        return ['error' => 'No available times'];
    }

    return $available_times;
}

if (isset($_GET['success']) && $_GET['success'] === 'true') {
    $reservation_success = true;
}

?>

<!doctype html>
<html lang="en-GB">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Lancaster's Restaurant</title>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' data: https://*; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; font-src 'self'; connect-src 'self';">
    <link rel="stylesheet" href="styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const serviceSelect = document.getElementById('service');
    const dateInput = document.getElementById('reservation_date');
    const timeInput = document.getElementById('reservation_time');
    const guestsInput = document.getElementById('guests');

    async function fetchAvailableDates() {
        const service = encodeURIComponent(serviceSelect.value);
        const guests = encodeURIComponent(guestsInput.value);

        timeInput.innerHTML = ''; 
        const response = await fetch(location.pathname + `?fetch=dates&service=${service}&guests=${guests}`);
        const availableDates = await response.json();

        dateInput.innerHTML = '';

        if (availableDates.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No available dates';
            dateInput.appendChild(option);

            // If no available dates set no available times as well
            const timeOption = document.createElement('option');
            timeOption.value = '';
            timeOption.textContent = 'No available times';
            timeInput.appendChild(timeOption);
        } else {
            availableDates.forEach(date => {
                const option = document.createElement('option');
                option.value = date;
                option.textContent = date;
                dateInput.appendChild(option);
            });

            fetchAvailableTimes();
        }
    }

    async function fetchAvailableTimes() {
        if (!dateInput.value) {
            timeInput.innerHTML = '<option value="">No available times</option>';
            return;
        }

        const service = serviceSelect.value;
        const date = dateInput.value;
        const guests = guestsInput.value;

        const response = await fetch(window.location.href + `?fetch=times&service=${service}&date=${date}&guests=${guests}`);
        const availableTimes = await response.json();

        timeInput.innerHTML = '';

        if (availableTimes.error || availableTimes.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No available times';
            timeInput.appendChild(option);
        } else {
            availableTimes.forEach(time => {
                const option = document.createElement('option');
                option.value = time;
                option.textContent = time;
                timeInput.appendChild(option);
            });
        }
    }

    serviceSelect.addEventListener('change', fetchAvailableDates);
    dateInput.addEventListener('change', fetchAvailableTimes);
    guestsInput.addEventListener('change', fetchAvailableDates);
});

    </script>
</head>
<body>
    <?php include('navbar.php'); ?>
    <main role="main">
    <?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['dismiss_prompt'])): ?>
    <section id="login-prompt">
        <p>Log in or create an account to track your bookings and enjoy additional features!</p>
        <button id="dismiss-prompt">Continue as Guest</button>
        <button onclick="window.location.href='login.php?section=login'">Log In</button>
        <button onclick="window.location.href='login.php?section=signup'">Sign Up</button>
    </section>
    <?php endif; ?>
        <div class="reservation-container" style="display: flex; gap: 30px; align-items: flex-start; max-width: 1200px; margin: 0 auto;">
            <section class="intro-section" aria-label="Reservation Introduction" style="flex: 1; text-align: left; padding: 20px;">
            <h1>Feeling Hungry?</h1>
        <div class="intro-text" style="gap: 30px; border: 2px solid #ccc; border-radius: 10px; margin: 20 auto;">
        <p>
            At Lancaster’s Restaurant, we offer up to three services daily: Breakfast, Lunch, and Dinner. Our availability may vary depending on the day of the week. Typical service times are:
        </p>
        <ul style="list-style: none; padding: 20; margin: 20px 0;">
            <li><strong>Breakfast:</strong> 07:30 – 10:30</li>
            <li><strong>Lunch:</strong> 12:00 – 14:30</li>
            <li><strong>Dinner:</strong> 17:00 – 22:30</li>
        </ul>
        <p>
            Secure your table now and join us for an unforgettable culinary journey.
        </p>
        </div>
        <div class="intro-images" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
            <img src="compressed images/restaurant-1-compressed.jpeg" alt="Restaurant Ambiance" style="width: 100%; border-radius: 10px;">
            <img src="compressed images/restaurant-2-compressed.jpeg" alt="Table Setting" style="width: 100%; border-radius: 10px;">
            <img src="compressed images/restaurant-3-compressed.jpeg" alt="Dining Experience" style="width: 100%; border-radius: 10px;">
            <img src="compressed images/restaurant-4-compressed.jpeg" alt="Fine Dining" style="width: 100%; border-radius: 10px;">
        </div>
    </section>


        <!-- Booking Form Section -->
    <section class="book-table-section" style="flex: 1;" role="form" aria-labelledby="book-table-heading">
    <img src="images/logos/Lancaster's-logos_white_cropped.png" alt="Home Page logo" style="height: 60px;">
        <h1 id="book-table-heading" style="text-align: center; color: #e4c176;">Book a Table</h1>
        <?php if (isset($_SESSION['email_link']) && isset($_SESSION['calendar_link'])): ?>
    <article class="confirmation-message">
        <h2>Thank you for your reservation!</h2>
        <p>Your booking has been successfully received.</p>
        <p>You can download your booking details:</p>
        <ul>
            <li><a href="<?php echo $_SESSION['email_link']; ?>" style="color: #ddd;" download>Download Confirmation Email</a></li>
            <li><a href="<?php echo $_SESSION['calendar_link']; ?>" style="color: #ddd;" download>Download Calendar File</a></li>
        </ul>
        <a href="index.php" class="btn" style="color: #ddd;">Return to Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="myaccount.php" class="btn">View Booking</a>
        <?php endif; ?>
    </article>
    <?php 
        unset($_SESSION['email_link'], $_SESSION['calendar_link']);
    ?>
    <?php else: ?>
    <?php if (!empty($error_message)): ?>
    <p style="color: red; font-weight: bold;"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <form id="booking-form" action="" method="post" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Your name" required>
        </div>
        <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="form-group">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="Your email" required>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="phone">Phone (Optional):</label>
            <input type="text" id="phone" name="phone" placeholder="Your phone number">
        </div>
        <div class="form-group">
            <label for="service">Service:</label>
            <select id="service" name="service" required>
                <option value="">Select</option>
                <option value="breakfast">Breakfast</option>
                <option value="lunch">Lunch</option>
                <option value="dinner">Dinner</option>
            </select>
        </div>
        <div class="form-group">
            <label for="guests">Guests:</label>
            <select id="guests" name="guests" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
        </div>
        <div class="form-group">
            <label for="reservation_date">Date:</label>
            <select id="reservation_date" name="reservation_date" required></select>
        </div>
        <div class="form-group">
            <label for="reservation_time">Time:</label>
            <select id="reservation_time" name="reservation_time" required></select>
        </div>
        <div class="form-group" style="grid-column: span 2;">
            <label for="info">Additional Info (Optional):</label>
            <textarea id="info" name="info" rows="2" placeholder="Allergies, etc."></textarea>
        </div>
        <div class="form-group" style="grid-column: span 2; text-align: center;">
            <button type="submit" style="padding: 10px 20px; font-size: 16px;">Book Now</button>
        </div>
    </form>
    <?php endif; ?>
    </section>
    </div>
</main>
<?php echo $twig->render('footer.twig');
?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dismissButton = document.getElementById('dismiss-prompt');

        if (dismissButton) {
            dismissButton.addEventListener('click', function () {
                document.getElementById('login-prompt').style.display = 'none';
            });
        }
    });
</script>
</body>
</html>
