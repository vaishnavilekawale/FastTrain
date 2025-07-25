<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login to book a ticket.'); window.location.href='login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $from_station = trim($_POST['from_station'] ?? '');
    $to_station = trim($_POST['to_station'] ?? '');
    $travel_date = $_POST['travel_date'] ?? '';
    $travel_time = $_POST['travel_time'] ?? '';
    $travel_class = $_POST['travel_class'] ?? '';
    $seats = filter_var($_POST['seats'] ?? '', FILTER_VALIDATE_INT);

    if (empty($from_station) || empty($to_station) || empty($travel_date) || empty($travel_time) || empty($travel_class) || $seats === false || $seats < 1) {
        echo "<script>alert('All fields are required and number of seats must be a positive number.'); window.location.href='index.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO tickets (user_id, from_station, to_station, travel_date, travel_time, travel_class, seats) VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("isssssi", $user_id, $from_station, $to_station, $travel_date, $travel_time, $travel_class, $seats);

        if ($stmt->execute()) {
            echo "<script>alert('Ticket booked successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            error_log("Database error during ticket booking: " . $stmt->error);
            echo "<script>alert('Error booking ticket: " . $stmt->error . " Please try again.'); window.location.href='index.php';</script>";
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare SQL statement: " . $conn->error);
        echo "<script>alert('A database error occurred. Please try again later.'); window.location.href='index.php';</script>";
    }
} else {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

if (isset($conn)) {
    $conn->close();
}
?>