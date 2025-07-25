<?php
session_start(); // Start the session at the very beginning

// Assuming config.php contains your database connection ($conn)
// For demonstration, I'm including a placeholder for config.php
// In a real application, ensure config.php correctly sets up $conn
if (file_exists("config.php")) {
    include "config.php";
} else {
    // Placeholder for database connection if config.php is not found
    // In a real scenario, you would handle this error or ensure config.php exists
    $conn = null; // Set conn to null if not included
    error_log("Error: config.php not found. Database operations will fail.");
}


$name = $email = $message = "";
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $message = trim($_POST["message"]);

    if ($name != "" && $email != "" && $message != "") {
        // Check if $conn is properly initialized before proceeding with DB operations
        if ($conn) {
            $sql = "INSERT INTO contact (name, email, message) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);

                if (mysqli_stmt_execute($stmt)) {
                    $success = "Your message has been sent successfully!";
                    $name = $email = $message = ""; // Clear form fields on success
                } else {
                    $error = "Error sending message: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error = "Database statement preparation failed: " . mysqli_error($conn);
            }
        } else {
            $error = "Database connection not available. Message could not be sent.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - TrainBook</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* General Resets and Body Styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            font-family: 'Poppins', sans-serif; /* Consistent modern font */
            height: 100%;
            scroll-behavior: smooth;
            background-image: url('images/home5.jpg'); /* Your background image */
            background-size: cover;
            background-position: center;
            background-attachment: fixed; /* Fix background image */
            position: relative;
            display: flex; /* Use flexbox for centering form */
            flex-direction: column;
            align-items: center; /* Center horizontally */
            justify-content: flex-start; /* Start from top, but allow form to center */
            padding-top: 100px; /* Space for fixed navbar */
            min-height: 100vh; /* Ensure body takes full viewport height */
        }

        /* Dark overlay for background image */
        body::before {
            content: "";
            position: fixed; /* Fixed so it covers the whole viewport even on scroll */
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.75); /* Slightly darker overlay for better contrast */
            z-index: 0;
        }

        /* --- Navbar (Original Styles - NOT MODIFIED AS REQUESTED) --- */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, rgba(29, 68, 113, 0.9), rgba(52, 152, 219, 0.9)); /* Gradient background */
            color: white;
            padding: 15px 60px; /* Adjusted padding */
            position: fixed; /* Fixed navbar */
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000; /* Higher z-index */
            box-shadow: 0 4px 8px rgba(0,0,0,0.2); /* Subtle shadow */
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 10px 60px; /* Smaller padding when scrolled */
            background: linear-gradient(to right, rgba(29, 68, 113, 1), rgba(52, 152, 219, 1));
        }

        .nav-left .logo {
            font-size: 28px; /* Larger logo */
            color: #fff;
            font-weight: 700; /* Bolder font weight */
            letter-spacing: 1.5px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .nav-center {
            display: flex;
            gap: 30px; /* Slightly less gap */
        }

        .nav-center a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 25px; /* Pill-shaped buttons */
            transition: background 0.3s ease, transform 0.2s ease;
            font-weight: 500;
        }

        .nav-center a:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px); /* Slight lift on hover */
        }

        .nav-right {
            display: flex;
            gap: 10px; /* Less gap for buttons */
        }

        .btn {
            padding: 10px 22px; /* Adjusted padding */
            border-radius: 25px; /* Pill-shaped buttons */
            text-decoration: none;
            font-weight: 600; /* Bolder text */
            transition: background 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .login {
            background-color: #e67e22; /* A warm orange */
            color: white;
        }

        .signup {
            background-color: #2ecc71; /* A vibrant green */
            color: white;
        }

        .login:hover {
            background-color: #d35400;
            transform: translateY(-2px);
        }

        .signup:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
        }
        /* --- End Navbar Original Styles --- */


        /* --- Contact Form Specific Styling (MODIFIED) --- */
        .contact-container {
            max-width: 600px; /* Wider form */
            width: 90%; /* Responsive width */
            margin: 50px auto; /* Centered with vertical margin */
            background: rgba(255,255,255,0.08); /* Lighter, more transparent background */
            padding: 45px; /* More padding */
            border-radius: 25px; /* More rounded corners */
            backdrop-filter: blur(15px); /* Stronger blur effect for a prominent glass effect */
            border: 1px solid rgba(255,255,255,0.15); /* Delicate white border */
            position: relative;
            z-index: 2;
            color: white;
            box-shadow: 0 15px 45px rgba(0,0,0,0.4); /* Stronger shadow */
            animation: fadeInScale 0.8s ease-out; /* Fade-in and slight scale animation */
        }

        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .contact-container h2 {
            text-align: center;
            margin-bottom: 35px; /* More space below heading */
            font-size: 40px; /* Larger heading */
            color: #fff;
            text-shadow: 1px 1px 8px rgba(0,0,0,0.6);
            font-weight: 700;
        }

        .contact-container input,
        .contact-container textarea {
            width: 100%;
            padding: 16px; /* More padding */
            margin: 15px 0; /* More margin */
            border: 1px solid rgba(255,255,255,0.3); /* Light, transparent border */
            background-color: rgba(255,255,255,0.1); /* Slightly visible input background */
            color: #fff; /* White text for inputs */
            border-radius: 12px; /* More rounded input fields */
            font-size: 17px; /* Larger font size */
            transition: border-color 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .contact-container input::placeholder,
        .contact-container textarea::placeholder {
            color: rgba(255,255,255,0.6); /* Lighter placeholder text */
        }

        .contact-container input:focus,
        .contact-container textarea:focus {
            border-color: #3498db; /* Blue border on focus */
            background-color: rgba(255,255,255,0.15); /* Slightly brighter background on focus */
            box-shadow: 0 0 20px rgba(52, 152, 219, 0.7); /* Stronger glow effect on focus */
            outline: none;
        }

        .contact-container textarea {
            resize: vertical; /* Allow vertical resizing */
            min-height: 120px; /* Minimum height for textarea */
        }

        .contact-container button {
            width: 100%;
            padding: 18px; /* More padding */
            background: linear-gradient(to right, #2ecc71, #27ae60); /* Vibrant green gradient button */
            color: white;
            border: none;
            border-radius: 12px; /* Rounded button */
            font-size: 20px; /* Larger font size */
            font-weight: 700; /* Bolder text */
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 8px 20px rgba(46, 204, 113, 0.4); /* Green shadow */
            margin-top: 25px; /* Space above button */
        }

        .contact-container button:hover {
            background: linear-gradient(to right, #27ae60, #2ecc71); /* Reverse gradient on hover */
            transform: translateY(-4px); /* More pronounced lift effect */
            box-shadow: 0 10px 25px rgba(46, 204, 113, 0.5);
        }

        /* Message (Success/Error) Styling */
        .message {
            text-align: center;
            margin-bottom: 25px; /* More space above form */
            padding: 18px; /* More padding */
            border-radius: 12px;
            font-weight: 600;
            font-size: 18px;
            animation: fadeIn 0.5s ease-out; /* Fade-in animation for messages */
            box-shadow: 0 5px 15px rgba(0,0,0,0.2); /* Subtle shadow for messages */
        }

        .success {
            background-color: rgba(46, 204, 113, 0.25); /* Green with more transparency */
            color: #2ecc71; /* Vibrant green text */
            border: 1px solid #2ecc71;
        }

        .error {
            background-color: rgba(231, 76, 60, 0.25); /* Red with more transparency */
            color: #e74c3c; /* Vibrant red text */
            border: 1px solid #e74c3c;
        }

        /* Animations (Consistent with index.php) */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive Design (MODIFIED for contact form specifically) */
        @media (max-width: 768px) {
            /* Navbar responsiveness (kept original) */
            .navbar {
                flex-direction: column;
                padding: 15px 20px;
            }
            .nav-center {
                margin-top: 15px;
                gap: 20px;
            }
            .nav-right {
                margin-top: 15px;
            }

            /* Contact form specific adjustments */
            .contact-container {
                margin: 30px 20px; /* Adjust margins for smaller screens */
                padding: 30px;
            }
            .contact-container h2 {
                font-size: 32px;
                margin-bottom: 25px;
            }
            .contact-container input,
            .contact-container textarea,
            .contact-container button {
                padding: 14px;
                font-size: 16px;
            }
            .message {
                font-size: 16px;
                padding: 14px;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 480px) {
            /* Navbar responsiveness (kept original) */
            .nav-center {
                flex-direction: column;
                gap: 10px;
            }
            .nav-center a {
                width: 100%;
                text-align: center;
            }
            .nav-right {
                flex-direction: column;
                width: 100%;
            }
            .nav-right .btn {
                width: 100%;
                text-align: center;
            }

            /* Contact form specific adjustments */
            .contact-container {
                margin: 20px 15px;
                padding: 25px;
            }
            .contact-container h2 {
                font-size: 28px;
                margin-bottom: 20px;
            }
             .contact-container input,
            .contact-container textarea,
            .contact-container button {
                font-size: 15px;
                padding: 12px;
            }
            .message {
                font-size: 14px;
                padding: 12px;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-left">
            <h2 class="logo">TrainBook</h2>
        </div>
        <div class="nav-center">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="contact.php">Contact</a>
            <a href="admin_login.php">Admin Panel</a>
        </div>
        <div class="nav-right">
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn login">Login</a>
                <a href="signup.php" class="btn signup">Signup</a>
            <?php else: ?>
                <a href="dashboard.php" class="btn login">Dashboard</a>
                <a href="logout.php" class="btn signup">Logout</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="contact-container">
        <h2>Get in Touch</h2>

        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Your Name" value="<?php echo htmlspecialchars($name); ?>" required>
            <input type="email" name="email" placeholder="Your Email" value="<?php echo htmlspecialchars($email); ?>" required>
            <textarea name="message" rows="6" placeholder="Your Message" required><?php echo htmlspecialchars($message); ?></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>

    <script>
        // Navbar shrink and change background on scroll (Consistent with index.php)
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) { // Adjust this value as needed
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    </script>

</body>
</html>