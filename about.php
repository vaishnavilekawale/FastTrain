<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About - Train Booking System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #3498db;
            --dark-blue: #2c3e50;
            --light-bg: #f0f2f5;
            --white: #ffffff;
            --text-color: #555;
            --accent-orange: #e67e22;
            --accent-green: #2ecc71;
            --border-radius-medium: 12px;
            --card-shadow: 0 5px 15px rgba(0,0,0,0.1);
            --card-hover-shadow: 0 12px 30px rgba(0,0,0,0.25);
            --transition-speed: 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            font-family: 'Poppins', sans-serif;
            height: 100%;
            scroll-behavior: smooth;
            background-color: var(--light-bg);
            line-height: 1.6;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to right, rgba(29, 68, 113, 0.9), rgba(52, 152, 219, 0.9));
            color: var(--white);
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
            color: var(--white);
            font-weight: 700;
            letter-spacing: 1.5px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        .nav-center {
            display: flex;
            gap: 30px;
        }

        .nav-center a {
            color: var(--white);
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
            background-color: var(--accent-orange);
            color: white;
        }

        .signup {
            background-color: var(--accent-green);
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

        .about-main {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            padding: 100px 80px 60px;
            background-color: var(--white);
            gap: 40px;
            min-height: calc(100vh - 100px);
        }

        .about-image {
            flex: 1 1 450px;
            padding: 20px;
            animation: fadeIn 1.2s ease-out;
        }

        .about-image img {
            width: 100%;
            max-width: 600px;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            transition: transform 0.4s ease;
        }

        .about-image img:hover {
            transform: scale(1.02);
        }

        .about-content {
            flex: 1 1 450px;
            padding: 20px;
            animation: slideInRight 1.2s ease-out;
        }

        .about-content h2 {
            font-size: 42px;
            margin-bottom: 25px;
            color: var(--dark-blue);
            line-height: 1.3;
        }

        .about-content p {
            font-size: 19px;
            color: var(--text-color);
            line-height: 1.8;
            max-width: 700px;
        }

        .section-heading {
            font-size: 38px;
            color: var(--dark-blue);
            margin-bottom: 50px;
            position: relative;
            padding-bottom: 15px;
            text-align: center;
        }

        .section-heading::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--primary-blue);
            border-radius: 2px;
        }

        .why-choose, .facilities {
            background: var(--white);
            padding: 60px 40px;
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .facilities {
            background-color: var(--light-bg);
        }

        .features, .facility-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (min-width: 1200px) {
            .features, .facility-grid {
                grid-template-columns: repeat(4, 1fr);
                max-width: 1180px;
            }
        }

        @media (min-width: 768px) and (max-width: 1199px) {
            .features, .facility-grid {
                grid-template-columns: repeat(2, 1fr);
                max-width: 600px;
            }
        }

        .feature-box, .facility-box {
            background-color: var(--white);
            border-radius: var(--border-radius-medium);
            padding: 30px 25px;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .feature-box i, .facility-box i {
            font-size: 48px;
            color: var(--primary-blue);
            margin-bottom: 20px;
            width: 80px;
            height: 80px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            background-color: rgba(52, 152, 219, 0.1);
            transition: background-color var(--transition-speed), color var(--transition-speed), transform var(--transition-speed);
        }

        .feature-box:hover i, .facility-box:hover i {
            background-color: var(--primary-blue);
            color: var(--white);
            transform: scale(1.1);
        }

        .feature-box h4, .facility-box h4 {
            font-size: 24px;
            margin-bottom: 12px;
            color: var(--dark-blue);
            font-weight: 600;
        }

        .feature-box p, .facility-box p {
            font-size: 17px;
            color: var(--text-color);
            line-height: 1.7;
        }

        .feature-box:hover, .facility-box:hover {
            transform: translateY(-10px);
            box-shadow: var(--card-hover-shadow);
        }

        .offers-section {
            background-color: var(--white);
            padding: 60px 40px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }

        .offer-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            justify-content: center;
            max-width: 1300px;
            margin: 0 auto;
            animation: fadeIn 1.5s ease-out;
        }

        @media (min-width: 992px) {
            .offer-gallery {
                grid-template-columns: repeat(3, 1fr);
                max-width: 1000px;
            }
        }

        .train-card {
            background: var(--white);
            border-radius: var(--border-radius-medium);
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            display: flex;
            flex-direction: column;
        }

        .train-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            border-bottom: 1px solid #eee;
        }

        .train-card h4 {
            padding: 15px 20px;
            font-size: 20px;
            color: var(--dark-blue);
            font-weight: 600;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .train-card:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: var(--card-hover-shadow);
        }

        .footer {
            background: linear-gradient(to right, #1d4471, #3498db);
            color: white;
            text-align: center;
            padding: 30px 20px;
            font-size: 15px;
            box-shadow: 0 -4px 8px rgba(0,0,0,0.2);
            margin-top: 40px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 992px) {
            .navbar {
                padding: 15px 40px;
            }
            .about-main {
                padding: 90px 40px 50px;
                flex-direction: column;
            }
            .about-content h2 {
                font-size: 36px;
                text-align: center;
            }
            .about-content p {
                font-size: 17px;
                text-align: center;
            }
            .section-heading {
                font-size: 34px;
            }
            .offer-gallery {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                max-width: none;
            }
            .train-card {
                max-width: 300px;
            }
            .train-card img {
                height: 200px;
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
                flex-wrap: wrap;
                justify-content: center;
            }
            .nav-right {
                margin-top: 15px;
                flex-wrap: wrap;
                justify-content: center;
            }
            .nav-right .btn {
                width: auto;
            }
            .about-main {
                padding: 80px 20px 40px;
                gap: 20px;
            }
            .about-image, .about-content {
                padding: 10px;
            }
            .about-content h2 {
                font-size: 30px;
            }
            .about-content p {
                font-size: 16px;
            }
            .offers-section, .why-choose, .facilities {
                padding: 40px 20px;
            }
            .section-heading {
                font-size: 30px;
                margin-bottom: 30px;
            }
            .features, .facility-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                max-width: 90%;
                margin: auto;
            }
            .train-card, .feature-box, .facility-box {
                max-width: 90%;
                margin: auto;
            }
            .train-card img {
                height: 180px;
            }
            .feature-box i, .facility-box i {
                font-size: 42px;
                width: 70px;
                height: 70px;
            }
            .feature-box h4, .facility-box h4 {
                font-size: 20px;
            }
            .feature-box p, .facility-box p {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .nav-left .logo {
                font-size: 24px;
            }
            .nav-center a {
                padding: 8px 12px;
                font-size: 14px;
            }
            .nav-right .btn {
                padding: 8px 15px;
                font-size: 14px;
            }
            .about-content h2 {
                font-size: 26px;
            }
            .about-content p {
                font-size: 15px;
            }
            .section-heading {
                font-size: 24px;
            }
            .feature-box h4, .facility-box h4 {
                font-size: 18px;
            }
            .feature-box p, .facility-box p {
                font-size: 14px;
            }
            .feature-box i, .facility-box i {
                font-size: 36px;
                width: 60px;
                height: 60px;
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

    <main>
        <section class="about-main">
            <div class="about-image">
                <img src="images/home4.avif" alt="Train Journey">
            </div>
            <div class="about-content">
                <h2>Experience India's Best Train Journey</h2>
                <p>
                    At TrainBook, we are passionate about connecting people through safe and scenic train travel. With a seamless booking process,
                    real-time seat availability, and user-friendly features, our platform ensures that your journey begins with ease. Whether it's a vacation or a work trip,
                    trust us to get you there comfortably. Our commitment is to provide an unparalleled travel experience, making every journey memorable.
                </p>
            </div>
        </section>

        <section class="why-choose">
            <h3 class="section-heading">Why Choose TrainBook?</h3>
            <div class="features">
                <div class="feature-box">
                    <i class="fas fa-train"></i>
                    <h4>Fast & Reliable</h4>
                    <p>Get instant ticket confirmations and real-time updates for your peace of mind.</p>
                </div>
                <div class="feature-box">
                    <i class="fas fa-lock"></i>
                    <h4>Secure Booking</h4>
                    <p>Your transactions are safeguarded with advanced encryption technology.</p>
                </div>
                <div class="feature-box">
                    <i class="fas fa-exchange-alt"></i>
                    <h4>Easy Modifications</h4>
                    <p>Effortlessly change or cancel your bookings with our flexible policies.</p>
                </div>
                <div class="feature-box">
                    <i class="fas fa-headset"></i>
                    <h4>24/7 Support</h4>
                    <p>Our dedicated support team is always here to assist you, day or night.</p>
                </div>
            </div>
        </section>

        <section class="offers-section">
            <h3 class="section-heading">Popular Trains</h3>
            <div class="offer-gallery">
                <div class="train-card">
                    <img src="images/home1.jpg" alt="Rajdhani Express">
                    <h4>Rajdhani Express</h4>
                </div>
                <div class="train-card">
                    <img src="images/home2.webp" alt="Shatabdi Express">
                    <h4>Shatabdi Express</h4>
                </div>
                <div class="train-card">
                    <img src="images/home3.avif" alt="Duronto Express">
                    <h4>Duronto Express</h4>
                </div>
                <div class="train-card">
                    <img src="images/pexels-pixabay-207377.jpg" alt="Vande Bharat Express">
                    <h4>Vande Bharat Express</h4>
                </div>
                <div class="train-card">
                    <img src="images/home6.webp" alt="Garib Rath Express">
                    <h4>Garib Rath Express</h4>
                </div>
                <div class="train-card">
                    <img src="images/home5.jpg" alt="Fanta Express">
                    <h4>Fanta Express</h4>
                </div>
            </div>
        </section>

        <section class="facilities">
            <h3 class="section-heading">Onboard Facilities</h3>
            <div class="facility-grid">
                <div class="facility-box">
                    <i class="fas fa-utensils"></i>
                    <h4>Delicious Meals</h4>
                    <p>Savor a variety of complimentary and paid meal options on your journey.</p>
                </div>
                <div class="facility-box">
                    <i class="fas fa-wifi"></i>
                    <h4>Free Wi-Fi</h4>
                    <p>Stay connected with high-speed internet access available on select routes.</p>
                </div>
                <div class="facility-box">
                    <i class="fas fa-fan"></i>
                    <h4>Climate Control</h4>
                    <p>Enjoy perfectly air-conditioned coaches for a comfortable travel experience.</p>
                </div>
                <div class="facility-box">
                    <i class="fas fa-bolt"></i>
                    <h4>Charging Ports</h4>
                    <p>Keep your devices powered up with convenient charging sockets at your seat.</p>
                </div>
            </div>
        </section>
    </main>

    <script>
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