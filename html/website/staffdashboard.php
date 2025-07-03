<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('db_connect.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

function fetch_results($conn, $query, $params = [], $types = "") {
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $result;
}

$changePasswordErrorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("SELECT password FROM Accounts WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if (empty($hashed_password)) {
            $changePasswordErrorMessage = "Error fetching current password. Please try again.";
        } elseif (!password_verify($current_password, $hashed_password)) {
            $changePasswordErrorMessage = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $changePasswordErrorMessage = "New passwords do not match.";
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
            $changePasswordErrorMessage = "New password must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.";
        } else {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE Accounts SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_hashed_password, $user_id);

            if ($stmt->execute()) {
                session_destroy();
                header("Location: login.php");
                exit();
            } else {
                $changePasswordErrorMessage = "An error occurred while updating the password.";
            }
            $stmt->close();
        }
    } else {
        $changePasswordErrorMessage = "User not logged in. Please log in again.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Fetch services for a given date
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_data = json_decode(file_get_contents('php://input'), true);

    if (isset($input_data['select_date'])) {
        $selected_date = htmlspecialchars($input_data['select_date'], ENT_QUOTES, 'UTF-8');
        $services_today = fetch_results($conn, "
            SELECT S.service_id, S.name, 
                   DATE_FORMAT(MIN(ST.time), '%H:%i') AS start_time, 
                   DATE_FORMAT(ADDTIME(MAX(ST.time), '00:30:00'), '%H:%i') AS end_time, 
                   S.max_tables 
            FROM Services S 
            JOIN Service_Times ST ON S.service_id = ST.service_id 
            WHERE S.date = ? 
            GROUP BY S.service_id", 
            [$selected_date], "s");
    
        echo json_encode(['services' => $services_today]);
        exit;
    }
    
}

if (isset($_POST['update_service'])) {
    $service_id = $_POST['service_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $max_tables = $_POST['tables'];

    // Update max_tables in the Services table
    $stmt = $conn->prepare("UPDATE Services SET max_tables = ? WHERE service_id = ?");
    $stmt->bind_param("ii", $max_tables, $service_id);
    $stmt->execute();
    $stmt->close();

    // Clear existing times for the service in Service_Times table
    $stmt = $conn->prepare("DELETE FROM Service_Times WHERE service_id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $stmt->close();

    // Generate 15-minute interval times
    $time_slots = [];
    $current_time = strtotime($start_time);
    $end_time_calc = strtotime($end_time) - 1800; // Subtract 30 minutes
    while ($current_time <= $end_time_calc) {
        $time_slots[] = date('H:i', $current_time);
        $current_time = strtotime('+15 minutes', $current_time);
    }

    // Insert updated times and table counts into Service_Times table
    $stmt = $conn->prepare("INSERT INTO Service_Times (service_id, time, num_of_tables) VALUES (?, ?, ?)");
    foreach ($time_slots as $time_slot) {
        $stmt->bind_param("isi", $service_id, $time_slot, $max_tables);
        $stmt->execute();
    }
    $stmt->close();
}


if (isset($_POST['add_service'])) {
    $service_name = $_POST['service_name'];
    $service_date = $_POST['select_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $max_tables = $_POST['tables'];

    // Insert service into Services table
    $stmt = $conn->prepare("INSERT INTO Services (name, date, max_tables) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $service_name, $service_date, $max_tables);
    $stmt->execute();
    $service_id = $stmt->insert_id;
    $stmt->close();

    // Generate 15-minute interval times
    $time_slots = [];
    $current_time = strtotime($start_time);
    $end_time_calc = strtotime($end_time) - 1800; // Subtract 30 minutes
    while ($current_time <= $end_time_calc) {
        $time_slots[] = date('H:i', $current_time);
        $current_time = strtotime('+15 minutes', $current_time);
    }

    // Insert service times into Service_Times table
    $stmt = $conn->prepare("INSERT INTO Service_Times (service_id, time, num_of_tables) VALUES (?, ?, ?)");
    foreach ($time_slots as $time_slot) {
        $stmt->bind_param("isi", $service_id, $time_slot, $max_tables);
        $stmt->execute();
    }
    $stmt->close();

    // Redirect to avoid duplicate form submission
    header("Location: staffdashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' data: https://*; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; font-src 'self'; connect-src 'self';">
    <link rel="stylesheet" href="styles.css">
    <script>
    function togglePasswordForm(event) {
        event.preventDefault(); // Prevent the link from navigating
        const passwordForm = document.getElementById('passwordForm');
        passwordForm.style.display = passwordForm.style.display === 'none' || !passwordForm.style.display ? 'block' : 'none';
    }
    </script>
</head>
<body>
    <?php include('navbar.php'); ?>
    <main class="staff-page">
        <h1>Staff Dashboard</h1>
        <?php
            $current_datetime = date('Y-m-d H:i');
            $next_service = null;

            // Get the next upcoming service - sort by date and time
            $future_services = fetch_results(
                $conn,
                "SELECT S.service_id, S.name, S.date, MIN(ST.time) AS earliest_time 
                FROM Services S
                JOIN Service_Times ST ON S.service_id = ST.service_id
                WHERE S.date >= CURDATE()
                GROUP BY S.service_id, S.name, S.date
                ORDER BY S.date, earliest_time",
                []
            );

            foreach ($future_services as $service) {
                $service_date_time = $service['date'] . ' ' . $service['earliest_time'];
                
                // Check if the service is in future
                if ($service_date_time >= $current_datetime) {
                    $next_service = $service;
                    break;
                }
            }

            if (!empty($next_service)) {
                $next_service_details = fetch_results($conn, "
                    SELECT S.service_id, S.name, S.date, MIN(ST.time) AS start_time, MAX(ST.time) AS end_time 
                    FROM Services S 
                    JOIN Service_Times ST ON S.service_id = ST.service_id 
                    WHERE S.service_id = ? 
                    GROUP BY S.service_id", 
                    [$next_service['service_id']], "i"
                );

            if (!empty($next_service_details)) {
                $service = $next_service_details[0];
                echo "<section id='next-service' section class='next-service'>";
                echo "<div id='service-details'>";
                echo "<h2>Next Service</h2>";
                echo "<p>Service: " . htmlspecialchars($service['name']) . "</p>";
                echo "<p>Date: " . htmlspecialchars($service['date']) . "</p>";
                echo "<p>Time: " . htmlspecialchars(date('H:i', strtotime($service['start_time']))) . 
                    " - " . date('H:i', strtotime($service['end_time'] . ' +30 minutes')) . "</p>";

                $reservations = fetch_results($conn, "
                    SELECT reservation_time, guests, name, email 
                    FROM Reservations 
                    WHERE reservation_date = ? AND service = ?", 
                    [$service['date'], $service['name']], "ss"
                );
                echo "</div>";
                if (empty($reservations)) {
                    echo "<h4>Bookings:</h4>";
                    echo "<p>No bookings for this service.</p>";
                } else {
                    echo "<div class='booking-list' style='margin-top=0'>";
                    echo "<h4>Bookings:</h4><ul>";
                    foreach ($reservations as $reservation) {
                        echo "<li>" . date('H:i', strtotime($reservation['reservation_time'])) . 
                            " - " . htmlspecialchars($reservation['name']) . " - " . htmlspecialchars($reservation['email']). 
                            " (" . htmlspecialchars($reservation['guests']) . " guests)</li>";
                    }
                    echo "</ul>";
                }
                echo "</div>";   
                echo "<button onclick='printNextService()'>Print Next Service</button>";
                echo "</section>";
            }
        } else {
            echo "<section class='next-service'><div id='service-details'><h2>Next Service</h2><p>No upcoming services available.</p></div></section>";
        }
        ?>
        <section class='today-service'>
            <div id="service-details">
            <h2>Today's Services</h2>
            <?php
            $date_today = date('Y-m-d');
            $services_today = fetch_results($conn, "SELECT S.service_id, S.name, MIN(ST.time) AS start_time, MAX(ST.time) AS end_time, S.max_tables FROM Services S JOIN Service_Times ST ON S.service_id = ST.service_id WHERE S.date = ? GROUP BY S.service_id", [$date_today], "s");

            if (empty($services_today)):
            ?>
            <p>No services scheduled for today.</p>
            <?php else: ?>
                <?php foreach ($services_today as $service): ?>
                    <div>
                    <h3>
                        <?php 
                        echo ucfirst(htmlspecialchars($service['name'])); // Capitalise the first letter of the name
                        ?> 
                        (<?php 
                        echo htmlspecialchars(date('H:i', strtotime($service['start_time'])));
                        ?> - <?php 
                        echo date('H:i', strtotime($service['end_time'] . ' +30 minutes')); // Add 30 minutes to the end time
                        ?>)
                    </h3>
                    </div>
                    </div>
                    <div class='booking-list' style='margin-top=0'>
                    <h4>Bookings:</h4>
                    <?php
                        $reservations = fetch_results($conn, "SELECT reservation_id, reservation_time, guests, email, name FROM Reservations WHERE reservation_date = ? AND service = ?", [$date_today, $service['name']], "ss");
                    ?>
                    <?php if (empty($reservations)): ?>
                    <p>No bookings for this service today.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($reservations as $reservation): ?>
                            <li><?php echo htmlspecialchars(date('H:i', strtotime($reservation['reservation_time']))); ?> - <?php echo htmlspecialchars($reservation['name']); ?> - <?php echo htmlspecialchars($reservation['email']); ?> (<?php echo htmlspecialchars($reservation['guests']); ?> guests)</li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="upcoming-services">
            <h2>Upcoming Services</h2>
            <form id="date-form">
                <label for="select_date">Select Date:</label>
                <input type="date" id="select_date" name="select_date" required min="<?php echo date('Y-m-d'); ?>">
            </form>

            <div id="upcoming-services-list">
                <!-- Services for the selected date will appear here -->
            </div>
            <button id="add-service-button" style="display: none;">Add Service</button>
        

        <section id="edit-service-form" style="display: none;">
            <h2>Edit Service</h2>
            <form action="staffdashboard.php" method="post">
                <input type="hidden" id="edit_service_id" name="service_id">
                <label for="edit_service_name">Service Name:</label>
                <input type="text" id="edit_service_name" name="service_name" required>
                <label for="edit_start_time">Start Time:</label>
                <input type="time" id="edit_start_time" name="start_time" required>
                <label for="edit_end_time">End Time:</label>
                <input type="time" id="edit_end_time" name="end_time" required>
                <label for="edit_max_tables">Max Tables:</label>
                <input type="number" id="tables" name="tables" required>
                <button type="submit" name="update_service">Update Service</button>
                <button type="button" onclick="cancelEdit()">Cancel</button>
            </form>
        </section>

        <section id="add-service-form" style="display: none;">
            <h2>Add Service</h2>
            <form action="staffdashboard.php" method="post">
                <input type="hidden" id="add_service_date" name="select_date">
                <label for="service_name">Service Type:</label>
                <select id="service_name" name="service_name" onchange="updateTimeDefaults()" required>
                    <option value="breakfast">Breakfast</option>
                    <option value="lunch">Lunch</option>
                    <option value="dinner">Dinner</option>
                </select>
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" value="07:30" required>
                <label for="end_time">End Time:</label>
                <input type="time" id="end_time" name="end_time" value="10:30" required>
                <label for="tables">Number of Tables:</label>
                <input type="number" id="tables" name="tables" value="10" required>
                <button type="submit" name="add_service">Add Service</button>
                <button type="button" onclick="cancelAdd()">Cancel</button>
            </form>
        </section>
        </section>

        <section id="logout">
        <form method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
        </section>
        <section class="password-change">
        <!-- Display Error Message -->
        <?php if (!empty($changePasswordErrorMessage)): ?>
            <p style="color: red; text-align: center ;"><?php echo htmlspecialchars($changePasswordErrorMessage); ?></p>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    document.getElementById('passwordForm').style.display = 'block';
                });
            </script>
        <?php endif; ?>

        <a href="#" class="toggle-password-link" onclick="togglePasswordForm(event)">Change Password</a>

        <form id="passwordForm" class="password-form" method="post" style="display: none;">
            <label for="current_password">Current Password:</label>
            <input type="password" id="current_password" name="current_password" required>

            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit" name="change_password" class="submit-btn">Update Password</button>
        </form>
        </section>
    </main>
    <?php
        echo $twig->render('footer.twig');
    ?>

    <script>
        document.getElementById('select_date').addEventListener('change', async function() {
            const selectedDate = this.value;
            if (!selectedDate) return;

            document.getElementById('add-service-button').style.display = 'block';

            const response = await fetch('staffdashboard.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ select_date: selectedDate })
            });

            const data = await response.json();
            const servicesContainer = document.getElementById('upcoming-services-list');
            servicesContainer.innerHTML = ''; // Clear previous content

            if (data.services.length === 0) {
                servicesContainer.innerHTML = '<p>No services scheduled for this date.</p>';
            } else {
                const ul = document.createElement('ul');
                data.services.forEach(service => {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        Service ID: ${service.service_id} <br>
                        Name: ${service.name} <br>
                        Start Time: ${service.start_time} <br>
                        End Time: ${service.end_time} <br>
                        Max Tables: ${service.max_tables} <br>
                    `;

                    const button = document.createElement('button');
                    button.innerText = 'Edit Service';
                    button.onclick = function() {
                        document.getElementById('edit-service-form').style.display = 'block';
                        document.getElementById('add-service-form').style.display = 'none';
                        document.getElementById('edit_service_id').value = service.service_id;
                        document.getElementById('edit_service_name').value = service.name;
                        document.getElementById('edit_max_tables').value = service.max_tables;
                    };
                    li.appendChild(button);

                    ul.appendChild(li);
                });
                servicesContainer.appendChild(ul);
            }
        });

        // Show the add service form when the button is clicked
        document.getElementById('add-service-button').addEventListener('click', function() {
            document.getElementById('add-service-form').style.display = 'block';
            document.getElementById('edit-service-form').style.display = 'none';
            document.getElementById('add_service_date').value = document.getElementById('select_date').value;
        });

        function updateTimeDefaults() {
            const serviceType = document.getElementById('service_name').value;
            const startField = document.getElementById('start_time');
            const endField = document.getElementById('end_time');
            switch (serviceType) {
                case 'breakfast':
                    startField.value = '07:30';
                    endField.value = '10:30';
                    break;
                case 'lunch':
                    startField.value = '12:00';
                    endField.value = '14:00';
                    break;
                case 'dinner':
                    startField.value = '17:00';
                    endField.value = '22:30';
                    break;
            }
        }

        function cancelAdd() {
            document.getElementById('add-service-form').style.display = 'none';
        }

        function cancelEdit() {
            document.getElementById('edit-service-form').style.display = 'none';
        }
        
        function printNextService() {
            const serviceSection = document.getElementById('next-service');
            const newWindow = window.open('', '_blank');
            newWindow.document.write('<html><head><title>Print Next Service</title></head><body>');
            newWindow.document.write(serviceSection.outerHTML);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        }
    </script>
</body>
</html>
