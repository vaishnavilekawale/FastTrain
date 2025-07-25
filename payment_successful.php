<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$ticket_id = filter_var($_GET['ticket_id'] ?? '', FILTER_VALIDATE_INT);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment Successful - TrainBook</title>
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
            height: 100vh;
            color: white;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .success-container {
            max-width: 500px;
            width: 95%;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            border-radius: 25px;
            padding: 40px;
            backdrop-filter: blur(15px);
            animation: fadeIn 1s ease-out;
            text-align: center;
        }

        h1 {
            font-size: 38px;
            color: #28a745;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        p {
            font-size: 1.2em;
            color: #e0e0e0;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .ticket-id-display {
            font-size: 1.5em;
            font-weight: 600;
            color: #c0e6ff;
            margin-top: 20px;
            margin-bottom: 30px;
            display: inline-block;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .dashboard-btn {
            display: inline-block;
            margin-top: 30px;
            background-color: #3498db;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            border-radius: 30px;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .dashboard-btn:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .success-container {
                padding: 30px;
            }
            h1 {
                font-size: 32px;
            }
            p {
                font-size: 1.1em;
            }
            .dashboard-btn {
                padding: 10px 25px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .success-container {
                padding: 20px;
            }
            h1 {
                font-size: 28px;
            }
            p {
                font-size: 1em;
            }
            .ticket-id-display {
                font-size: 1.3em;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <h1>Payment Successful! 🎉</h1>
        <p>Your train ticket has been successfully booked.</p>
        <?php if ($ticket_id): ?>
            <p>Your Ticket ID: <span class="ticket-id-display">#<?php echo htmlspecialchars($ticket_id); ?></span></p>
        <?php else: ?>
            <p>You can view your booked tickets on your dashboard.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="dashboard-btn">Go to Dashboard</a>
    </div>
</body>
</html>