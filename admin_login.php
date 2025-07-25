<?php
session_start();
include 'config.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin_data = $result->fetch_assoc();
        if (MD5($password) === $admin_data['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $msg = "Invalid password!";
        }
    } else {
        $msg = "Invalid email or credentials!";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - TrainBook</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1A293A 0%, #2C3E50 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #E0E5EC;
            position: relative;
            overflow: hidden;
        }

        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.2;
            z-index: 0;
        }

        body::before {
            background: #FF5E5E;
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            animation: blob1 18s infinite alternate ease-in-out;
        }

        body::after {
            background: #5ECFFF;
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            animation: blob2 22s infinite alternate-reverse ease-in-out;
        }

        @keyframes blob1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(50px, 80px) scale(1.1); }
        }

        @keyframes blob2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-70px, -60px) scale(0.9); }
        }

        .back-home {
            position: absolute;
            top: 25px;
            left: 25px;
            color: #E0E5EC;
            font-weight: 500;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.15);
            padding: 10px 20px;
            border-radius: 30px;
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            backdrop-filter: blur(8px);
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .back-home::before {
            content: '🏠';
            font-size: 1.1em;
            line-height: 1;
        }

        .back-home:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        .login-box {
            background: rgba(255, 255, 255, 0.08);
            padding: 50px 40px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 15px 45px rgba(0,0,0,0.4);
            width: 100%;
            max-width: 450px;
            z-index: 1;
            text-align: center;
            animation: slideUp 0.8s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            font-weight: 700;
            font-size: 36px;
            margin-bottom: 30px;
            color: #FFFFFF;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 16px 20px;
            margin: 15px 0;
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.15);
            border-color: #74b9ff;
            box-shadow: 0 0 15px rgba(116, 185, 255, 0.6);
        }

        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(to right, #2ecc71, #27ae60);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(46, 204, 113, 0.4);
            margin-top: 25px;
        }

        button:hover {
            background: linear-gradient(to right, #27ae60, #2ecc71);
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 8px 25px rgba(46, 204, 113, 0.5);
        }

        .message {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            background-color: rgba(255, 235, 59, 0.1);
            color: #FFEB3B;
            border-left: 5px solid #FBC02D;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        @media (max-width: 500px) {
            .login-box {
                padding: 40px 25px;
                margin: 20px;
            }
            h2 {
                font-size: 30px;
            }
            .back-home {
                top: 15px;
                left: 15px;
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-home">← Back to Home</a>

    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if ($msg) echo "<div class='message'>" . htmlspecialchars($msg) . "</div>"; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>