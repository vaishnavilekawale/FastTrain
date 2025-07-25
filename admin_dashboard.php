<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
include "config.php";

$view = isset($_GET['view']) ? htmlspecialchars($_GET['view']) : 'users';

$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 'deleted') {
    $success_message = 'Record deleted successfully!';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - TrainBook</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f0f2f5 0%, #e0e5ec 100%);
            min-height: 100vh;
            padding: 0;
            color: #333;
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dashboard-container {
            width: 100%;
            max-width: 100%;
            background-color: #ffffff;
            border-radius: 0;
            box-shadow: none;
            padding: 30px 40px;
            margin-top: 0;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
            margin-bottom: 30px;
        }

        .top-bar h1 {
            color: #1d4471;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .logout-btn {
            background: linear-gradient(to right, #e74c3c, #c0392b);
            color: #fff;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);
        }

        .logout-btn:hover {
            background: linear-gradient(to right, #c0392b, #e74c3c);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(231, 76, 60, 0.4);
        }

        .menu {
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .menu a {
            background: #3498db;
            color: #fff;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 3px 8px rgba(52, 152, 219, 0.2);
        }

        .menu a:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(52, 152, 219, 0.3);
        }

        .menu a.active {
            background: linear-gradient(to right, #2ecc71, #27ae60);
            transform: translateY(-1px);
            box-shadow: 0 5px 12px rgba(46, 204, 113, 0.4);
            font-weight: 600;
        }

        h2 {
            margin-top: 40px;
            margin-bottom: 25px;
            color: #2c3e50;
            font-size: 26px;
            font-weight: 600;
            text-align: center;
            position: relative;
            padding-bottom: 10px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #3498db;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            overflow: hidden;
            table-layout: fixed;
        }

        th, td {
            padding: 16px;
            text-align: left;
            border: none;
            font-size: 15px;
            white-space: normal;
            word-wrap: break-word;
            vertical-align: top;
        }

        th {
            background: #2c3e50;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th:first-child { border-top-left-radius: 10px; }
        th:last-child { border-top-right-radius: 10px; }

        td {
            background-color: #fcfcfc;
            border-bottom: 1px solid #eee;
        }

        tr:nth-child(even) td {
            background-color: #f7f7f7;
        }

        tr:hover td {
            background-color: #e8f3fb;
            transform: scale(1.01);
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .no-records {
            text-align: center;
            padding: 40px;
            font-size: 18px;
            color: #7f8c8d;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            transition: background-color 0.2s ease, transform 0.2s ease;
            display: inline-block;
        }

        .delete-btn:hover {
            background-color: #c0392b;
            transform: translateY(-1px);
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Specific column widths for better layout */
        #bookings-table th:nth-child(1), #bookings-table td:nth-child(1) { width: 5%; } /* ID */
        #bookings-table th:nth-child(2), #bookings-table td:nth-child(2) { width: 10%; } /* User Name */
        #bookings-table th:nth-child(3), #bookings-table td:nth-child(3) { width: 8%; } /* From */
        #bookings-table th:nth-child(4), #bookings-table td:nth-child(4) { width: 8%; } /* To */
        #bookings-table th:nth-child(5), #bookings-table td:nth-child(5) { width: 8%; } /* Date */
        #bookings-table th:nth-child(6), #bookings-table td:nth-child(6) { width: 6%; } /* Time */
        #bookings-table th:nth-child(7), #bookings-table td:nth-child(7) { width: 6%; } /* Class */
        #bookings-table th:nth-child(8), #bookings-table td:nth-child(8) { width: 5%; } /* Seats */
        #bookings-table th:nth-child(9), #bookings-table td:nth-child(9) { width: 10%; } /* Booked At */
        #bookings-table th:nth-child(10), #bookings-table td:nth-child(10) { width: 8%; } /* Payment Status */
        #bookings-table th:nth-child(11), #bookings-table td:nth-child(11) { width: 8%; } /* Payment Method */
        #bookings-table th:nth-child(12), #bookings-table td:nth-child(12) { width: 12%; } /* Transaction ID */
        #bookings-table th:nth-child(13), #bookings-table td:nth-child(13) { width: 7%; } /* Amount Paid */
        #bookings-table th:nth-child(14), #bookings-table td:nth-child(14) { width: 7%; } /* Action - Adjusted width */


        @media (max-width: 768px) {
            body { padding: 15px; }
            .dashboard-container { padding: 20px 25px; }
            .top-bar { flex-direction: column; gap: 15px; text-align: center; }
            .top-bar h1 { font-size: 28px; }
            .menu { flex-direction: column; gap: 10px; }
            .menu a { width: 100%; text-align: center; padding: 10px 15px; font-size: 14px; }
            table, thead, tbody, th, td, tr { display: block; }
            thead tr { position: absolute; top: -9999px; left: -9999px; }
            table tr { border: 1px solid #ddd; margin-bottom: 15px; border-radius: 8px; overflow: hidden; }
            table td { border: none; border-bottom: 1px solid #eee; position: relative; padding-left: 50%; text-align: right; }
            table td:before {
                position: absolute;
                top: 0;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
                color: #555;
            }
            table#users-table td:nth-of-type(1):before { content: "ID:"; }
            table#users-table td:nth-of-type(2):before { content: "Name:"; }
            table#users-table td:nth-of-type(3):before { content: "Email:"; }
            table#users-table td:nth-of-type(4):before { content: "Registered At:"; }
            table#users-table td:nth-of-type(5):before { content: "Action:"; }

            table#bookings-table td:nth-of-type(1):before { content: "ID:"; }
            table#bookings-table td:nth-of-type(2):before { content: "User Name:"; }
            table#bookings-table td:nth-of-type(3):before { content: "From:"; }
            table#bookings-table td:nth-of-type(4):before { content: "To:"; }
            table#bookings-table td:nth-of-type(5):before { content: "Date:"; }
            table#bookings-table td:nth-of-type(6):before { content: "Time:"; }
            table#bookings-table td:nth-of-type(7):before { content: "Class:"; }
            table#bookings-table td:nth-of-type(8):before { content: "Seats:"; }
            table#bookings-table td:nth-of-type(9):before { content: "Booked At:"; }
            table#bookings-table td:nth-of-type(10):before { content: "Payment Status:"; }
            table#bookings-table td:nth-of-type(11):before { content: "Payment Method:"; }
            table#bookings-table td:nth-of-type(12):before { content: "Transaction ID:"; }
            table#bookings-table td:nth-of-type(13):before { content: "Amount Paid:"; }
            table#bookings-table td:nth-of-type(14):before { content: "Action:"; }

            table#messages-table td:nth-of-type(1):before { content: "Name:"; }
            table#messages-table td:nth-of-type(2):before { content: "Email:"; }
            table#messages-table td:nth-of-type(3):before { content: "Message:"; }
            table#messages-table td:nth-of-type(4):before { content: "Date:"; }
            table#messages-table td:nth-of-type(5):before { content: "Action:"; }
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="top-bar">
        <h1>Admin Dashboard</h1>
        <a href="admin_logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="menu">
        <a href="?view=users" class="<?php echo ($view == 'users') ? 'active' : ''; ?>">All Users</a>
        <a href="?view=bookings" class="<?php echo ($view == 'bookings') ? 'active' : ''; ?>">Total Bookings</a>
        <a href="?view=messages" class="<?php echo ($view == 'messages') ? 'active' : ''; ?>">Contact Messages</a>
    </div>

    <?php if ($success_message): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <?php
    if ($view == 'users') {
        echo "<h2>Registered Users</h2>";
        $res = $conn->query("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC");
        if ($res->num_rows > 0) {
            echo "<table id='users-table'><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Registered At</th><th>Action</th></tr></thead><tbody>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>{$row['created_at']}</td>
                        <td><a href='delete_record.php?type=user&id={$row['id']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this user? This action cannot be undone.\");'>Delete</a></td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='no-records'>No registered users found.</p>";
        }
    } elseif ($view == 'bookings') {
        echo "<h2>Total Bookings</h2>";
        $res = $conn->query("SELECT t.*, u.name AS user_name FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.booked_at DESC");
        if ($res->num_rows > 0) {
            echo "<table id='bookings-table'><thead><tr>
                    <th>ID</th>
                    <th>User Name</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Class</th>
                    <th>Seats</th>
                    <th>Booked At</th>
                    <th>Payment Status</th>
                    <th>Payment Method</th>
                    <th>Transaction ID</th>
                    <th>Amount Paid</th>
                    <th>Action</th>
                </tr></thead><tbody>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['user_name']) . "</td>
                        <td>" . htmlspecialchars($row['from_station']) . "</td>
                        <td>" . htmlspecialchars($row['to_station']) . "</td>
                        <td>{$row['travel_date']}</td>
                        <td>{$row['travel_time']}</td>
                        <td>" . htmlspecialchars($row['travel_class']) . "</td>
                        <td>{$row['seats']}</td>
                        <td>{$row['booked_at']}</td>
                        <td>" . htmlspecialchars($row['payment_status']) . "</td>
                        <td>" . (isset($row['payment_method']) && $row['payment_method'] !== null ? htmlspecialchars($row['payment_method']) : 'N/A') . "</td>
                        <td>" . (isset($row['transaction_id']) && $row['transaction_id'] !== null ? htmlspecialchars($row['transaction_id']) : 'N/A') . "</td>
                        <td>" . (isset($row['amount_paid']) && $row['amount_paid'] !== null ? htmlspecialchars(number_format($row['amount_paid'], 2)) : 'N/A') . "</td>
                        <td><a href='delete_record.php?type=booking&id={$row['id']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this booking? This action cannot be undone.\");'>Delete</a></td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='no-records'>No bookings found.</p>";
        }
    } elseif ($view == 'messages') {
        echo "<h2>Contact Messages</h2>";
        $res = $conn->query("SELECT id, name, email, message, submitted_at FROM contact ORDER BY submitted_at DESC");
        if ($res->num_rows > 0) {
            echo "<table id='messages-table'><thead><tr><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th>Action</th></tr></thead><tbody>";
            while ($row = $res->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td>" . htmlspecialchars($row['message']) . "</td>
                        <td>{$row['submitted_at']}</td>
                        <td><a href='delete_record.php?type=message&id={$row['id']}' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this message? This action cannot be undone.\");'>Delete</a></td>
                    </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='no-records'>No contact messages found.</p>";
        }
    }
    ?>

</div>

</body>
</html>
