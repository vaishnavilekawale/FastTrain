<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Train Ticket Booking</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            font-family: 'Poppins', sans-serif;
            height: 100%;
            scroll-behavior: smooth;
            background-color: #f0f2f5;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, rgba(29, 68, 113, 0.9), rgba(52, 152, 219, 0.9));
            color: white;
            padding: 15px 60px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            padding: 10px 60px;
            background: linear-gradient(to right, rgba(29, 68, 113, 1), rgba(52, 152, 219, 1));
        }

        .nav-left .logo {
            font-size: 28px;
            color: #fff;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .nav-center {
            display: flex;
            gap: 30px;
        }

        .nav-center a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 25px;
            transition: background 0.3s ease, transform 0.2s ease;
            font-weight: 500;
        }

        .nav-center a:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .nav-right {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 22px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .login {
            background-color: #e67e22;
            color: white;
        }

        .signup {
            background-color: #2ecc71;
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

        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('images/home.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding-left: 80px;
            padding-right: 60px;
            color: white;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
            animation: fadeIn 1.5s ease-out;
        }

        .hero-content {
            max-width: 700px;
            margin-top: 0;
        }

        .hero h1 {
            font-size: 58px;
            margin-bottom: 20px;
            line-height: 1.2;
            animation: slideInLeft 1s ease-out;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 40px;
            animation: slideInLeft 1.2s ease-out;
        }

        .hero button {
            padding: 15px 35px;
            font-size: 18px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            animation: fadeIn 1.5s ease-out 0.5s backwards;
        }

        .hero button:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.4);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            width: 95%;
            max-width: 450px;
            text-align: center;
            position: relative;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            animation: zoomIn 0.4s ease-out;
        }

        .modal-content h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 25px;
        }

        .modal-content input,
        .modal-content select,
        .modal-content button {
            width: calc(100% - 0px);
            padding: 14px;
            margin: 12px 0;
            font-size: 17px;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .modal-content select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
        }

        .modal-content input:focus,
        .modal-content select:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.4);
            outline: none;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 32px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .close:hover {
            color: #333;
            transform: rotate(90deg);
        }

        .modal-content button {
            background-color: #27ae60;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .modal-content button:hover {
            background-color: #219d59;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
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
            .hero {
                padding-left: 20px;
                text-align: center;
                justify-content: center;
            }
            .hero-content {
                max-width: 90%;
            }
            .hero h1 {
                font-size: 40px;
            }
            .hero p {
                font-size: 16px;
            }
            .modal-content {
                margin: 5% auto;
                padding: 25px;
            }
        }

        @media (max-width: 480px) {
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
            .hero h1 {
                font-size: 32px;
            }
            .hero p {
                font-size: 14px;
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


<div class="hero">
    <div class="hero-content">
        <h1>Discover Traveling the World by Train</h1>
        <p>Experience comfort, speed, and scenic routes — book your journey now!</p>
        <button id="openModal">Book Ticket</button>
    </div>
</div>

<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Book Your Train Ticket</h2>
        <form action="book_ticket.php" method="post">
            <label for="modal_from_station">From Station:</label>
            <input type="text" id="modal_from_station" name="from_station" placeholder="From Station" required>

            <label for="modal_to_station">To Station:</label>
            <input type="text" id="modal_to_station" name="to_station" placeholder="To Station" required>

            <label for="modal_travel_date">Travel Date:</label>
            <input type="date" id="modal_travel_date" name="travel_date" required>

            <label for="modal_travel_time">Travel Time:</label>
            <input type="time" id="modal_travel_time" name="travel_time" required>

            <label for="modal_travel_class">Class:</label>
            <select id="modal_travel_class" name="travel_class" required>
                <option value="">Select Class</option>
                <option value="Sleeper">Sleeper</option>
                <option value="AC">AC</option>
                <option value="First Class">First Class</option>
            </select>

            <label for="modal_seats">Number of Seats:</label>
            <input type="number" id="modal_seats" name="seats" placeholder="Number of Seats" min="1" required>

            <button type="submit">Confirm Booking</button>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById("bookingModal");
    const openBtn = document.getElementById("openModal");
    const closeBtn = document.getElementById("closeModal");

    openBtn.onclick = () => {
        <?php if (!isset($_SESSION['user_id'])): ?>
            alert('Please login to book a ticket.');
            window.location.href = 'login.php';
        <?php else: ?>
            modal.style.display = "flex";
        <?php endif; ?>
    };

    closeBtn.onclick = () => {
        modal.style.display = "none";
    };

    window.onclick = function(e) {
        if (e.target == modal) {
            modal.style.display = "none";
        }
    };

    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
</script>

</body>
</html>