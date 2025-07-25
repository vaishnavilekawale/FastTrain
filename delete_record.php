<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

include "config.php";

$type = isset($_GET['type']) ? $_GET['type'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$redirect_url = "admin_dashboard.php";
$message_param = "success";

if ($id > 0) {
    $table_name = '';
    $id_column = 'id';

    switch ($type) {
        case 'user':
            $table_name = 'users';
            $redirect_url .= "?view=users";
            break;
        case 'booking':
            $table_name = 'tickets';
            $redirect_url .= "?view=bookings";
            break;
        case 'message':
            $table_name = 'contact';
            $redirect_url .= "?view=messages";
            break;
        default:
            header("Location: admin_dashboard.php?error=invalid_type");
            exit();
    }

    if ($table_name) {
        $sql = "DELETE FROM {$table_name} WHERE {$id_column} = ?";
        
        if (isset($conn) && $conn instanceof mysqli) {
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    header("Location: {$redirect_url}&{$message_param}=deleted");
                    exit();
                } else {
                    header("Location: {$redirect_url}&error=delete_failed&reason=" . urlencode($stmt->error));
                    exit();
                }
                $stmt->close();
            } else {
                header("Location: {$redirect_url}&error=prepare_failed&reason=" . urlencode($conn->error));
                exit();
            }
        } elseif (isset($pdo) && $pdo instanceof PDO) {
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id]);
                header("Location: {$redirect_url}&{$message_param}=deleted");
                exit();
            } catch (PDOException $e) {
                header("Location: {$redirect_url}&error=delete_failed&reason=" . urlencode($e->getMessage()));
                exit();
            }
        } else {
            header("Location: admin_dashboard.php?error=db_connection_missing");
            exit();
        }
    }
}

header("Location: admin_dashboard.php?error=invalid_request");
exit();
?>