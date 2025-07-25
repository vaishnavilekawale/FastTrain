<?php
session_start();
header('Content-Type: application/json');

if (file_exists("config.php")) {
    include "config.php";
} else {
    error_log("process_payment.php: config.php not found.");
    echo json_encode(['success' => false, 'message' => 'Database configuration file missing. Please contact support.']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    error_log("process_payment.php: User not logged in. Session ID: " . session_id());
    echo json_encode(['success' => false, 'message' => 'Authentication required. Please log in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

$input = file_get_contents('php://input');
$data = json_decode($input, true);

error_log("process_payment.php: Received raw POST input: " . $input);
error_log("process_payment.php: Decoded data: " . print_r($data, true));

if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
    error_log("process_payment.php: JSON decode error: " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Invalid data format received.']);
    exit;
}

$ticket_id = filter_var($data['ticket_id'] ?? null, FILTER_VALIDATE_INT);
$payment_method = htmlspecialchars($data['payment_method'] ?? '');
$transaction_id = htmlspecialchars($data['transaction_id'] ?? uniqid('TRAINPAY_'));
$amount_paid = filter_var($data['amount_paid'] ?? 0.0, FILTER_VALIDATE_FLOAT);

error_log("process_payment.php: Extracted - Ticket ID: " . $ticket_id);
error_log("process_payment.php: Extracted - Payment Method: " . $payment_method);
error_log("process_payment.php: Extracted - Transaction ID: " . $transaction_id);
error_log("process_payment.php: Extracted - Amount Paid: " . $amount_paid);
error_log("process_payment.php: Extracted - User ID from session: " . $user_id);

if (!$ticket_id || $ticket_id <= 0 || empty($payment_method) || $amount_paid === false || $amount_paid <= 0) {
    error_log("process_payment.php: Validation failed for input data.");
    echo json_encode(['success' => false, 'message' => 'Missing or invalid payment details.']);
    exit;
}

$payment_successful_from_gateway = true;

if ($payment_successful_from_gateway) {
    $new_payment_status = 'Paid';

    $stmt = $conn->prepare("UPDATE tickets SET payment_status = ?, payment_method = ?, transaction_id = ?, amount_paid = ? WHERE id = ? AND user_id = ?");

    if ($stmt) {
        $stmt->bind_param("sssdii", $new_payment_status, $payment_method, $transaction_id, $amount_paid, $ticket_id, $user_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                error_log("process_payment.php: Ticket ID " . $ticket_id . " updated to Paid successfully.");
                echo json_encode(['success' => true, 'message' => 'Payment successful and ticket status updated.']);
            } else {
                error_log("process_payment.php: No rows affected for ticket ID " . $ticket_id . " for user " . $user_id . ". Ticket might be invalid or already paid/cancelled.");
                echo json_encode(['success' => false, 'message' => 'Ticket not found or already processed.']);
            }
        } else {
            error_log("process_payment.php: SQL execute error: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        error_log("process_payment.php: SQL prepare error: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare database update statement: ' . $conn->error]);
    }
} else {
    error_log("process_payment.php: Payment simulation failed for ticket ID " . $ticket_id);
    echo json_encode(['success' => false, 'message' => 'Payment failed during processing.']);
}

$conn->close();
?>