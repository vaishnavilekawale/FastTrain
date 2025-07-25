<?php
session_start();

if (file_exists("config.php")) {
    include "config.php";
} else {
    $conn = null;
    error_log("Error: config.php not found or does not set \$conn. Database operations will fail.");
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($email) || empty($new_password) || empty($confirm_password)) {
        $msg = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $msg = "Passwords do not match!";
    } elseif (strlen($new_password) < 6) {
        $msg = "New password must be at least 6 characters long.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        if ($conn) {
            $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt_update->bind_param("ss", $hashed_password, $email);

                if ($stmt_update->execute()) {
                    $msg = "Password updated successfully! You can <a href='login.php'>Login now</a>.";
                } else {
                    $msg = "Something went wrong! Please try again.";
                }
                $stmt_update->close();
            } else {
                $msg = "Email not registered! Please check the email address.";
            }
            $stmt_check->close();
        } else {
            $msg = "Database connection error. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password - TrainBook</title>
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
            background: url('images/train_bg.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            border-radius: 20px;
            padding: 50px;
            width: 90%;
            max-width: 450px;
            color: #fff;
            backdrop-filter: blur(15px);
            z-index: 1;
            animation: fadeInScale 0.8s ease-out;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 35px;
            font-size: 38px;
            color: #74b9ff;
            font-weight: 700;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        .back-link {
            text-align: center;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .back-link a {
            color: #a29bfe;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: #74b9ff;
            text-decoration: underline;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 15px 20px;
            margin: 12px 0;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 17px;
            transition: border-color 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #74b9ff;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(116, 185, 255, 0.7);
        }

        button {
            width: 100%;
            padding: 15px;
            margin-top: 30px;
            border: none;
            border-radius: 10px;
            background-color: #0984e3;
            color: white;
            font-size: 19px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        button:hover {
            background-color: #0652dd;
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
            color: #ffeaa7;
            border: 1px solid rgba(255, 234, 167, 0.5);
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 500px) {
            .container {
                margin: 60px 20px;
                padding: 30px 25px;
                max-width: 95%;
            }

            h2 {
                font-size: 32px;
                margin-bottom: 25px;
            }

            .back-link {
                margin-bottom: 20px;
                font-size: 15px;
            }

            input[type="email"],
            input[type="password"] {
                padding: 12px 15px;
                font-size: 16px;
            }

            button {
                padding: 14px;
                font-size: 18px;
            }

            .message {
                padding: 12px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <div class="container">
        <h2>Reset Password</h2>
        <div class="back-link"><a href="login.php">← Back to Login</a></div>

        <?php
        if ($msg) {
            $message_class = '';
            if (strpos($msg, 'successfully') !== false) {
                $message_class = 'success';
            } elseif (strpos($msg, 'match') !== false || strpos($msg, 'wrong') !== false || strpos($msg, 'not registered') !== false || strpos($msg, 'required') !== false || strpos($msg, 'characters') !== false || strpos($msg, 'error') !== false) {
                $message_class = 'error';
            }
            echo "<p class='message $message_class'>$msg</p>";
        }
        ?>

        <form method="post">
            <input type="email" name="email" placeholder="Enter your registered email" required>
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>