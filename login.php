<?php
session_start();
if (file_exists("config.php")) {
    include "config.php";
} else {
    $conn = null;
    error_log("Error: config.php not found or does not set \$conn. Database operations will fail.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($conn) {
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];

                header("Location: dashboard.php");
                exit;
            } else {
                echo "<script>alert('Incorrect password. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('No account found with that email. Please check your email or sign up.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database connection error. Please try again later.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - TrainBook</title>
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

        .login-container {
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

        .login-container h2 {
            text-align: center;
            margin-bottom: 35px;
            font-size: 38px;
            color: #74b9ff;
            font-weight: 700;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        .login-container form input {
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

        .login-container form input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .login-container form input:focus {
            outline: none;
            border-color: #74b9ff;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 15px rgba(116, 185, 255, 0.7);
        }

        .login-container button {
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

        .login-container button:hover {
            background-color: #0652dd;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .login-container .forgot {
            display: block;
            text-align: right;
            margin-top: 15px;
            font-size: 15px;
            color: #a29bfe;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-container .forgot:hover {
            color: #74b9ff;
        }

        .login-container .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
        }

        .login-container .register-link a {
            color: #2ecc71;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-container .register-link a:hover {
            color: #27ae60;
            text-decoration: underline;
        }

        @media screen and (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
                max-width: 95%;
            }

            .login-container h2 {
                font-size: 32px;
                margin-bottom: 25px;
            }

            .login-container form input {
                padding: 12px 15px;
                font-size: 16px;
            }

            .login-container button {
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

    <div class="login-container">
        <h2>Login to TrainBook</h2>
        <form method="post">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <a class="forgot" href="reset_password.php">Forgot Password?</a>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            Don't have an account? <a href="signup.php">Register here</a>
        </div>
    </div>
</body>
</html>