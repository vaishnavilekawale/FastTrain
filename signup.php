<?php
include "config.php";

if (!isset($conn) || $conn->connect_error) {
    error_log("Error: Database connection not established in config.php. Please check your config.php file.");
    die("Database connection error. Please try again later.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>alert('All fields are required.');</script>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Please enter a valid email address.');</script>";
    } elseif (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long.');</script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "<script>alert('This email address is already registered. Please try logging in or use a different email.');</script>";
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                echo "<script>alert('Signup successful! You can now log in.'); window.location='login.php';</script>";
            } else {
                echo "<script>alert('Error registering your account. Please try again: " . $conn->error . "');</script>";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Signup - TrainBook</title>
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

        .signup-container {
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

        .signup-container h2 {
            text-align: center;
            margin-bottom: 35px;
            font-size: 38px;
            color: #74b9ff;
            font-weight: 700;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        .signup-container form input {
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

        .signup-container form input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .signup-container form input:focus {
            outline: none;
            border-color: #74b9ff;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(116, 185, 255, 0.7);
        }

        .signup-container button {
            width: 100%;
            padding: 15px;
            margin-top: 30px;
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

        .signup-container button:hover {
            background-color: #27ae60;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        @media screen and (max-width: 480px) {
            .signup-container {
                padding: 30px 25px;
                max-width: 95%;
            }

            .signup-container h2 {
                font-size: 32px;
                margin-bottom: 25px;
            }

            .signup-container form input {
                padding: 12px 15px;
                font-size: 16px;
            }

            .signup-container button {
                padding: 14px;
                font-size: 18px;
            }

            .back-button {
                top: 20px;
                left: 20px;
                padding: 10px 20px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <a href="index.php" class="back-button">← Back to Home</a>

    <div class="signup-container">
        <h2>Join TrainBook</h2>
        <form method="post">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>