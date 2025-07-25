<?php
session_start();

if (file_exists("config.php")) {
    include "config.php";
} else {
    die("Database connection error. Please contact support.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$name = htmlspecialchars($_SESSION['name']);
$email = htmlspecialchars($_SESSION['email']);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_ticket'])) {
    $from = trim($_POST['from_station'] ?? '');
    $to = trim($_POST['to_station'] ?? '');
    $date = $_POST['travel_date'] ?? '';
    $time = $_POST['travel_time'] ?? '';
    $class = $_POST['travel_class'] ?? '';
    $seats = filter_var($_POST['seats'] ?? '', FILTER_VALIDATE_INT);

    if (empty($from) || empty($to) || empty($date) || empty($time) || empty($class) || $seats === false || $seats < 1) {
        $message = "<p class='message error'>All fields are required for booking, and seats must be a positive number.</p>";
    } else {
        $stmt = $conn->prepare("INSERT INTO tickets (user_id, from_station, to_station, travel_date, travel_time, travel_class, seats, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
        if ($stmt) {
            $stmt->bind_param("isssssi", $user_id, $from, $to, $date, $time, $class, $seats);

            if ($stmt->execute()) {
                $message = "<p class='message success'>Ticket booked successfully! Please proceed to pay.</p>";
                $_POST['from_station'] = $_POST['to_station'] = $_POST['travel_date'] = $_POST['travel_time'] = $_POST['travel_class'] = $_POST['seats'] = '';
            } else {
                $message = "<p class='message error'>Error booking ticket: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            $message = "<p class='message error'>Database statement preparation failed (Booking): " . $conn->error . "</p>";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_ticket'])) {
    $ticket_id_to_delete = filter_var($_POST['ticket_id'] ?? '', FILTER_VALIDATE_INT);

    if ($ticket_id_to_delete === false || $ticket_id_to_delete <= 0) {
        $message = "<p class='message error'>Invalid ticket ID provided for cancellation.</p>";
    } else {
        $stmt_delete = $conn->prepare("DELETE FROM tickets WHERE id = ? AND user_id = ?");
        if ($stmt_delete) {
            $stmt_delete->bind_param("ii", $ticket_id_to_delete, $user_id);

            if ($stmt_delete->execute()) {
                if ($stmt_delete->affected_rows > 0) {
                    $message = "<p class='message success'>Ticket cancelled successfully!</p>";
                } else {
                    $message = "<p class='message error'>Ticket not found or you don't have permission to cancel this ticket.</p>";
                }
            } else {
                $message = "<p class='message error'>Error cancelling ticket: " . $stmt_delete->error . "</p>";
            }
            $stmt_delete->close();
        } else {
            $message = "<p class='message error'>Database statement preparation failed (Cancellation): " . $conn->error . "</p>";
        }
    }
}

$result_tickets = null;
$stmt_tickets = $conn->prepare("SELECT id, from_station, to_station, travel_date, travel_time, travel_class, seats, booked_at, payment_status FROM tickets WHERE user_id = ? ORDER BY booked_at DESC");
if ($stmt_tickets) {
    $stmt_tickets->bind_param("i", $user_id);
    $stmt_tickets->execute();
    $result_tickets = $stmt_tickets->get_result();
} else {
    $message .= "<p class='message error'>Error fetching your tickets: " . $conn->error . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - TrainBook</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('images/train_bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: white;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        .back-button {
            position: absolute;
            top: 30px;
            left: 30px;
            background-color: #3498db;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            font-size: 17px;
            font-weight: 600;
            border-radius: 30px;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 2;
        }

        .back-button:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        .dashboard-container {
            max-width: 1200px; 
            width: 95%;
            margin: auto;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(15px);
            animation: fadeIn 1s ease-out;
        }

        h1, h2 {
            text-align: center;
            color: #74b9ff;
            margin-bottom: 25px;
            font-weight: 700;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        h1 {
            font-size: 42px;
            margin-bottom: 30px;
        }

        h2 {
            font-size: 32px;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .booking-form {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .booking-form input,
        .booking-form select {
            width: 100%;
            padding: 15px 20px;
            margin: 10px 0;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 17px;
            transition: border-color 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .booking-form input::placeholder,
        .booking-form select option {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .booking-form select option {
            background-color: #1a1a1a;
            color: #fff;
        }

        .booking-form select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 15px center;
        }

        .booking-form input:focus,
        .booking-form select:focus {
            outline: none;
            border-color: #74b9ff;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(116, 185, 255, 0.7);
        }

        .booking-form button {
            width: 100%;
            padding: 15px;
            margin-top: 20px;
            border: none;
            border-radius: 10px;
            background-color: #2ecc71;
            color: white;
            font-size: 19px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .booking-form button:hover {
            background-color: #27ae60;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 10px;
            font-weight: 500;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid;
            animation: fadeIn 0.5s ease-out;
        }

        .message.success {
            color: #2ecc71;
            background-color: rgba(46, 204, 113, 0.1);
            border-color: #2ecc71;
        }
        .message.error {
            color: #e74c3c;
            background-color: rgba(231, 76, 60, 0.1);
            border-color: #e74c3c;
        }

        .ticket-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: separate;
            border-spacing: 0;
            background-color: rgba(0, 0, 0, 0.4);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }

        .ticket-table th,
        .ticket-table td {
            padding: 15px;
            text-align: center;
            color: #eee;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            vertical-align: middle; 
        }

        .ticket-table th {
            background-color: rgba(0, 0, 0, 0.6);
            color: #74b9ff;
            font-weight: 600;
            font-size: 1.1em;
            text-transform: uppercase;
        }

        .ticket-table tr:last-child td {
            border-bottom: none;
        }

        .ticket-table td:last-child,
        .ticket-table th:last-child {
            border-right: none;
        }

        .ticket-table tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.08);
        }
        
        .ticket-table td form {
            display: inline-block; 
            margin: 0; 
            padding: 0;
        }

        .cancel-btn, .pay-btn, .paid-status, .cancelled-status {
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: inline-block; 
            text-decoration: none;
            white-space: nowrap; 
        }

        .cancel-btn {
            background-color: #e74c3c;
        }

        .cancel-btn:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .pay-btn {
            background-color: #f39c12;
        }

        .pay-btn:hover {
            background-color: #e67e22;
            transform: translateY(-2px);
        }

        .paid-status {
            background-color: #28a745;
            cursor: default;
            font-weight: bold;
        }

        .cancelled-status {
            background-color: #6c757d;
            cursor: default;
            font-weight: bold;
        }

        .logout-form {
            text-align: center;
            margin-top: 40px;
        }
        .logout-form .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .logout-form .logout-btn:hover {
            background-color: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 992px) {
            .dashboard-container {
                padding: 30px;
            }
            h1 {
                font-size: 36px;
            }
            h2 {
                font-size: 28px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px 0;
            }
            .dashboard-container {
                margin: auto 20px;
                padding: 25px;
            }
            h1 {
                font-size: 30px;
            }
            h2 {
                font-size: 24px;
            }
            .booking-form {
                padding: 20px;
            }
            .booking-form input,
            .booking-form select,
            .booking-form button {
                padding: 12px 15px;
                font-size: 16px;
            }
            .ticket-table th,
            .ticket-table td {
                padding: 10px;
                font-size: 0.9em;
            }
            .logout-form .logout-btn {
                padding: 10px 25px;
                font-size: 16px;
            }
            .back-button {
                top: 20px;
                left: 20px;
                padding: 10px 20px;
                font-size: 15px;
            }
        }

        @media (max-width: 576px) {
            .dashboard-container {
                margin: auto 15px;
                padding: 20px;
            }
            h1 {
                font-size: 26px;
            }
            h2 {
                font-size: 20px;
            }
            .ticket-table {
                font-size: 0.8em;
            }
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .ticket-table td button,
            .ticket-table td .paid-status,
            .ticket-table td .cancelled-status {
                padding: 6px 10px; 
                font-size: 0.8em; 
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-button">← Back to Home</a>

    <div class="dashboard-container">
        <h1>Welcome, <?php echo $name; ?>!</h1>

        <div class="booking-form">
            <h2>Book a New Ticket</h2>
            <?php echo $message; ?>
            <form method="POST">
                <input type="hidden" name="book_ticket" value="1">
                <input type="text" name="from_station" placeholder="From Station" required value="<?php echo isset($_POST['from_station']) ? htmlspecialchars($_POST['from_station']) : ''; ?>">
                <input type="text" name="to_station" placeholder="To Station" required value="<?php echo isset($_POST['to_station']) ? htmlspecialchars($_POST['to_station']) : ''; ?>">
                <input type="date" name="travel_date" required value="<?php echo isset($_POST['travel_date']) ? htmlspecialchars($_POST['travel_date']) : ''; ?>">
                <input type="time" name="travel_time" required value="<?php echo isset($_POST['travel_time']) ? htmlspecialchars($_POST['travel_time']) : ''; ?>">
                <select name="travel_class" required>
                    <option value="">Select Class</option>
                    <option value="Sleeper" <?php echo (isset($_POST['travel_class']) && $_POST['travel_class'] == 'Sleeper') ? 'selected' : ''; ?>>Sleeper</option>
                    <option value="AC" <?php echo (isset($_POST['travel_class']) && $_POST['travel_class'] == 'AC') ? 'selected' : ''; ?>>AC</option>
                    <option value="General" <?php echo (isset($_POST['travel_class']) && $_POST['travel_class'] == 'General') ? 'selected' : ''; ?>>General</option>
                </select>
                <input type="number" name="seats" min="1" placeholder="No. of Seats" required value="<?php echo isset($_POST['seats']) ? htmlspecialchars($_POST['seats']) : ''; ?>">
                <button type="submit">Book Ticket</button>
            </form>
        </div>

        <h2>Your Booked Tickets</h2>
        <div class="table-responsive">
            <table class="ticket-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>To</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Class</th>
                        <th>Seats</th>
                        <th>Booked At</th>
                        <th>Payment Status</th>
                        <th>Cancel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_tickets && $result_tickets->num_rows > 0): ?>
                        <?php while ($row = $result_tickets->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['from_station']); ?></td>
                                <td><?php echo htmlspecialchars($row['to_station']); ?></td>
                                <td><?php echo htmlspecialchars($row['travel_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['travel_time']); ?></td>
                                <td><?php echo htmlspecialchars($row['travel_class']); ?></td>
                                <td><?php echo htmlspecialchars($row['seats']); ?></td>
                                <td><?php echo htmlspecialchars($row['booked_at']); ?></td>
                                <td>
                                    <?php if ($row['payment_status'] === 'Pending'): ?>
                                        <form action="payment.php" method="GET">
                                            <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="pay-btn">Proceed to Pay</button>
                                        </form>
                                    <?php elseif ($row['payment_status'] === 'Paid'): ?>
                                        <span class="paid-status">Paid</span>
                                    <?php elseif ($row['payment_status'] === 'Cancelled'): ?>
                                        <span class="cancelled-status">Cancelled</span>
                                    <?php else: ?>
                                        <span><?php echo htmlspecialchars($row['payment_status']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['payment_status'] !== 'Cancelled'): ?>
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this ticket?');">
                                            <input type="hidden" name="cancel_ticket" value="1">
                                            <input type="hidden" name="ticket_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="cancel-btn">Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 20px; color: rgba(255,255,255,0.7);">No tickets booked yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <form action="logout.php" method="post" class="logout-form">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
if (isset($stmt_tickets)) {
    $stmt_tickets->close();
}
?>