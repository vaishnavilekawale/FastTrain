<?php
session_start();

if (!file_exists("config.php")) {
    die("Database connection error. Please contact support.");
}
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$ticket_id = filter_var($_GET['ticket_id'] ?? '', FILTER_VALIDATE_INT);

$ticket_details = null;
if ($ticket_id) {
    $stmt = $conn->prepare("SELECT from_station, to_station, travel_date, travel_time, travel_class, seats FROM tickets WHERE id = ? AND user_id = ?");
    if ($stmt) {
        $stmt->bind_param("ii", $ticket_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $ticket_details = $result->fetch_assoc();
        }
        $stmt->close();
    }
}

if (!$ticket_details) {
    header("Location: dashboard.php");
    exit;
}

$base_price_per_seat = 100;
$price = $base_price_per_seat * $ticket_details['seats'];

if ($ticket_details['travel_class'] == 'AC') {
    $price *= 1.5;
} elseif ($ticket_details['travel_class'] == 'Sleeper') {
    $price *= 1.2;
}

$formatted_price = number_format($price, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Proceed to Pay - TrainBook</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-accent: #74b9ff;
            --secondary-accent: #3498db;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --dark-bg: rgba(0,0,0,0.8);
            --card-bg: rgba(255, 255, 255, 0.15);
            --text-light: white;
            --text-muted: rgba(255, 255, 255, 0.7);
            --border-dark: rgba(255, 255, 255, 0.3);
            --input-bg: rgba(255, 255, 255, 0.1);
            --input-border: rgba(255, 255, 255, 0.4);
            --shadow-strong: rgba(0, 0, 0, 0.4);
            --shadow-subtle: rgba(0, 0, 0, 0.2);
            --modal-bg: rgba(255, 255, 255, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.8)), url('images/train_bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: var(--text-light);
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        .payment-container {
            max-width: 900px;
            width: 95%;
            margin: auto;
            background: var(--card-bg);
            border: 1px solid var(--border-dark);
            box-shadow: 0 10px 40px var(--shadow-strong);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(15px);
            animation: fadeIn 1s ease-out;
            position: relative;
            transform: rotateZ(0deg);
            padding-bottom: 40px;
        }
        .payment-container > * {
            transform: rotateZ(0deg);
        }

        .back-button {
            position: fixed;
            top: 40px;
            left: 40px;
            background-color: var(--secondary-accent);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            font-size: 17px;
            font-weight: 600;
            border-radius: 30px;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            z-index: 2;
        }

        .back-button:hover {
            background-color: #2980b9;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.3);
        }

        .header-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            gap: 20px;
        }

        h1 {
            font-size: 42px;
            color: var(--primary-accent);
            font-weight: 700;
            text-shadow: 0 2px 5px rgba(0,0,0,0.3);
            text-align: left;
            margin-bottom: 0;
            flex-shrink: 0;
            max-width: 50%;
        }
        
        .ticket-info {
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 20px 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 20px;
            text-align: left;
            flex-grow: 1;
            min-width: 300px;
        }

        .ticket-info p {
            font-size: 1em;
            margin-bottom: 0;
            color: var(--text-light);
            line-height: 1.4;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .ticket-info p span:first-child {
            font-weight: 400;
            color: var(--text-muted);
            margin-bottom: 2px;
            font-size: 0.85em;
        }

        .ticket-info p strong {
            color: var(--primary-accent);
            font-weight: 600;
            font-size: 1em;
        }

        .total-amount {
            font-size: 3.2em;
            font-weight: 800;
            color: var(--success-color);
            margin: 40px 0;
            display: flex;
            justify-content: center;
            align-items: baseline;
            text-shadow: 0 0 15px rgba(46, 204, 113, 0.4);
        }
        .total-amount span {
            font-size: 0.6em;
            margin-right: 10px;
            font-weight: 500;
            color: var(--text-muted);
        }

        h2 {
            font-size: 32px;
            color: var(--primary-accent);
            margin-top: 35px;
            margin-bottom: 30px;
            font-weight: 600;
            text-align: center;
        }

        .payment-options {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 25px;
            padding: 0 15px;
        }

        .payment-option-trigger {
            text-decoration: none;
            display: block;
            width: 100%;
            max-width: 190px;
            transition: transform 0.3s ease, box-shadow 0.4s ease;
            cursor: pointer;
            border-radius: 15px;
            flex-grow: 1;
            flex-shrink: 1;
        }
        .payment-option-trigger:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px var(--shadow-strong);
        }

        .payment-option {
            background-color: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 15px;
            padding: 20px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 4px 15px var(--shadow-subtle);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .payment-option:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: var(--primary-accent);
        }

        .payment-option img {
            width: 55px;
            height: 55px;
            object-fit: contain;
            border-radius: 8px;
            filter: brightness(1.1);
        }

        .payment-option h3 {
            font-size: 1.2em;
            color: var(--text-light);
            margin: 0;
            font-weight: 500;
            text-align: center;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            visibility: hidden;
            opacity: 0;
            transition: visibility 0.4s, opacity 0.4s;
            backdrop-filter: blur(5px);
        }
        .modal-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        .modal-content {
            background-color: var(--modal-bg);
            border: 1px solid var(--border-dark);
            border-radius: 18px;
            padding: 35px;
            width: 90%;
            max-width: 480px;
            box-shadow: 0 15px 50px var(--shadow-strong);
            text-align: center;
            transform: scale(0.85);
            opacity: 0;
            transition: transform 0.4s ease-out, opacity 0.4s ease-out;
            color: var(--text-light);
        }
        .modal-overlay.active .modal-content {
            transform: scale(1);
            opacity: 1;
        }

        .modal-content h3 {
            font-size: 2.2em;
            color: var(--primary-accent);
            margin-bottom: 25px;
            font-weight: 700;
        }

        .modal-content p {
            font-size: 1.05em;
            color: var(--text-muted);
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .modal-content input[type="text"] {
            width: calc(100% - 20px);
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid var(--input-border);
            border-radius: 10px;
            background-color: var(--input-bg);
            color: var(--text-light);
            font-size: 1.05em;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .modal-content input[type="text"]:focus {
            outline: none;
            border-color: var(--primary-accent);
            box-shadow: 0 0 15px rgba(116, 185, 255, 0.7);
        }
        .modal-content input[type="text"]::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .modal-content img.qr-code {
            width: 190px;
            height: 190px;
            border: 5px solid #fff;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
            object-fit: contain;
        }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .modal-actions button {
            padding: 15px 30px;
            border: none;
            border-radius: 30px;
            font-size: 19px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .modal-actions button.pay-button {
            background-color: var(--success-color);
            color: #fff;
        }
        .modal-actions button.pay-button:hover {
            background-color: #27ae60;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }
        .modal-actions button.cancel-button {
            background-color: var(--error-color);
            color: #fff;
        }
        .modal-actions button.cancel-button:hover {
            background-color: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }


        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 992px) {
            .payment-container {
                padding: 30px;
            }
            .header-section {
                flex-direction: column;
                align-items: center;
            }
            h1 {
                font-size: 36px;
                text-align: center;
                max-width: 100%;
            }
            .ticket-info {
                max-width: 450px;
                margin-top: 20px;
            }
            h2 {
                font-size: 28px;
            }
            .back-button {
                top: 30px;
                left: 30px;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px 0;
            }
            .payment-container {
                margin: auto 20px;
                padding: 25px;
            }
            .back-button {
                top: 20px;
                left: 20px;
                padding: 10px 20px;
                font-size: 15px;
            }
            h1 {
                font-size: 30px;
                margin-bottom: 20px;
            }
            .ticket-info {
                padding: 15px;
                grid-template-columns: 1fr;
                gap: 8px;
            }
            .ticket-info p {
                font-size: 0.95em;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
                padding-bottom: 8px;
            }
            .ticket-info p:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }
            .ticket-info p span:first-child {
                margin-bottom: 0;
                font-size: 0.8em;
            }
            .ticket-info p strong {
                font-size: 0.95em;
                text-align: right;
            }
            .total-amount {
                font-size: 2.8em;
                margin: 30px 0;
            }
            h2 {
                font-size: 24px;
                margin-top: 30px;
                margin-bottom: 25px;
            }
            .payment-options {
                gap: 20px;
            }
            .payment-option-trigger {
                max-width: 160px;
            }
            .payment-option {
                padding: 15px;
                gap: 10px;
            }
            .payment-option img {
                width: 50px;
                height: 50px;
            }
            .payment-option h3 {
                font-size: 1.05em;
            }
            .modal-content {
                padding: 30px;
            }
            .modal-content h3 {
                font-size: 1.8em;
            }
            .modal-content p {
                font-size: 0.95em;
            }
            .modal-content input[type="text"] {
                padding: 12px;
                font-size: 1em;
            }
            .modal-content img.qr-code {
                width: 160px;
                height: 160px;
            }
            .modal-actions button {
                padding: 12px 25px;
                font-size: 17px;
            }
        }

        @media (max-width: 576px) {
            .payment-container {
                margin: auto 15px;
                padding: 20px;
            }
            h1 {
                font-size: 26px;
                margin-bottom: 15px;
            }
            h2 {
                font-size: 20px;
                margin-top: 20px;
                margin-bottom: 15px;
            }
            .ticket-info {
                padding: 12px;
            }
            .ticket-info p {
                font-size: 0.85em;
                padding-bottom: 5px;
            }
            .ticket-info p span:first-child,
            .ticket-info p strong {
                font-size: 0.85em;
            }
            .total-amount {
                font-size: 2.4em;
                margin: 20px 0;
            }
            .payment-options {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            .payment-option-trigger {
                max-width: 260px;
            }
            .payment-option {
                padding: 12px;
            }
            .payment-option img {
                width: 45px;
                height: 45px;
            }
            .payment-option h3 {
                font-size: 1em;
            }
            .modal-content {
                padding: 20px;
            }
            .modal-content h3 {
                font-size: 1.6em;
            }
            .modal-content p {
                font-size: 0.8em;
            }
            .modal-content input[type="text"] {
                padding: 10px;
                font-size: 0.9em;
            }
            .modal-content img.qr-code {
                width: 120px;
                height: 120px;
                margin-bottom: 15px;
            }
            .modal-actions button {
                padding: 8px 18px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <a href="dashboard.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

    <div class="payment-container">
        <div class="header-section">
            <h1>Proceed to Pay for Your Ticket</h1>
            <div class="ticket-info">
                <p><span>From:</span> <strong><?php echo htmlspecialchars($ticket_details['from_station']); ?></strong></p>
                <p><span>To:</span> <strong><?php echo htmlspecialchars($ticket_details['to_station']); ?></strong></p>
                <p><span>Date:</span> <strong><?php echo htmlspecialchars($ticket_details['travel_date']); ?></strong></p>
                <p><span>Time:</span> <strong><?php echo htmlspecialchars($ticket_details['travel_time']); ?></strong></p>
                <p><span>Class:</span> <strong><?php echo htmlspecialchars($ticket_details['travel_class']); ?></strong></p>
                <p><span>Seats:</span> <strong><?php echo htmlspecialchars($ticket_details['seats']); ?></strong></p>
            </div>
        </div>

        <div class="total-amount"><span>₹</span><?php echo $formatted_price; ?></div>
        <h2>Choose a Payment Method</h2>
        <div class="payment-options">
            <div class="payment-option-trigger" data-payment-method="GooglePay" data-type="phone">
                <div class="payment-option"><img src="images/gpay.svg" alt="Google Pay"><h3>Google Pay</h3></div>
            </div>
            <div class="payment-option-trigger" data-payment-method="PhonePe" data-type="phone">
                <div class="payment-option"><img src="images/ppay.svg" alt="PhonePe"><h3>PhonePe</h3></div>
            </div>
            <div class="payment-option-trigger" data-payment-method="Paytm" data-type="phone">
                <div class="payment-option"><img src="images/paytm.png" alt="Paytm"><h3>Paytm</h3></div>
            </div>
            <div class="payment-option-trigger" data-payment-method="Card" data-type="card">
                <div class="payment-option"><img src="images/card.png" alt="Credit/Debit Card"><h3>Card Payment</h3></div>
            </div>
            <div class="payment-option-trigger" data-payment-method="UPI" data-type="upi">
                <div class="payment-option"><img src="images/upi.svg" alt="UPI"><h3>UPI</h3></div>
            </div>
        </div>
    </div>

    <div id="upiModal" class="modal-overlay">
        <div class="modal-content">
            <h3 id="upiModalTitle">Pay with UPI</h3>
            <p id="upiModalText">Enter your UPI ID or scan the QR code:</p>
            <input type="text" id="upiIdInput" placeholder="yourname@bank" aria-label="UPI ID">
            <img src="https://dummyimage.com/200x200/ffffff/000000&text=UPI+QR+Code" alt="UPI QR Code" class="qr-code">
            <div class="modal-actions">
                <button class="pay-button" id="payUpiButton">Pay ₹<?php echo $formatted_price; ?></button>
                <button class="cancel-button" onclick="closeModal('upiModal')">Cancel</button>
            </div>
        </div>
    </div>

    <div id="phonePayModal" class="modal-overlay">
        <div class="modal-content">
            <h3 id="phonePayModalTitle">Pay with PhonePe</h3>
            <p id="phonePayModalText">Enter your registered mobile number:</p>
            <input type="text" id="phoneNumberInput" placeholder="e.g., 9876543210" aria-label="Phone Number" inputmode="numeric" pattern="[0-9]*" maxlength="10">
            <div class="modal-actions">
                <button class="pay-button" id="payPhoneButton">Pay ₹<?php echo $formatted_price; ?></button>
                <button class="cancel-button" onclick="closeModal('phonePayModal')">Cancel</button>
            </div>
        </div>
    </div>

    <div id="cardModal" class="modal-overlay">
        <div class="modal-content">
            <h3 id="cardModalTitle">Card Payment</h3>
            <p id="cardModalText">Enter your card details:</p>
            <input type="text" id="cardNumber" placeholder="Card Number" aria-label="Card Number" maxlength="19" inputmode="numeric">
            <input type="text" id="cardExpiry" placeholder="MM/YY" aria-label="Expiry Date" maxlength="5" inputmode="numeric">
            <input type="text" id="cardCVC" placeholder="CVC" aria-label="CVC" maxlength="4" inputmode="numeric">
            <div class="modal-actions">
                <button class="pay-button" id="payCardButton">Pay ₹<?php echo $formatted_price; ?></button>
                <button class="cancel-button" onclick="closeModal('cardModal')">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        let currentTicketId = <?php echo json_encode($ticket_id); ?>;
        let selectedPaymentMethod = '';
        let totalAmount = <?php echo json_encode($price); ?>;

        function openModal(modalId, method = '') {
            selectedPaymentMethod = method;
            document.getElementById(modalId).classList.add('active');

            const modalContent = document.getElementById(modalId).querySelector('.modal-content');
            const inputs = modalContent.querySelectorAll('input[type="text"]');
            const buttons = modalContent.querySelector('.modal-actions');
            
            inputs.forEach(input => input.value = '');
            buttons.style.display = 'flex';

            if (modalId === 'upiModal') {
                document.getElementById('upiIdInput').focus();
            } else if (modalId === 'phonePayModal') {
                document.getElementById('phoneNumberInput').focus();
            } else if (modalId === 'cardModal') {
                document.getElementById('cardNumber').focus();
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        async function handlePaymentAndRedirect(modalId, transactionId) {
            closeModal(modalId);

            const data = {
                ticket_id: currentTicketId,
                payment_method: selectedPaymentMethod,
                transaction_id: transactionId,
                amount_paid: totalAmount
            };

            console.log("Sending payment data:", data);

            try {
                const response = await fetch('process_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                console.log("Server response:", result);

                if (result.success) {
                    window.location.href = `payment_successful.php?ticket_id=${currentTicketId}`;
                } else {
                    alert('Payment processing failed: ' + result.message);
                }
            } catch (error) {
                console.error('Error during payment processing:', error);
                alert('An error occurred during payment. Please try again.');
            }
        }

        document.querySelectorAll('.payment-option-trigger').forEach(trigger => {
            trigger.addEventListener('click', function() {
                const paymentMethod = this.dataset.paymentMethod;
                const paymentType = this.dataset.type;
                if (paymentType === 'upi') {
                    openModal('upiModal', paymentMethod);
                } else if (paymentType === 'phone') {
                    openModal('phonePayModal', paymentMethod);
                } else if (paymentType === 'card') {
                    openModal('cardModal', paymentMethod);
                }
            });
        });

        document.getElementById('payUpiButton').addEventListener('click', function() {
            const upiId = document.getElementById('upiIdInput').value;
            if (upiId.trim() === '') { alert('Please enter your UPI ID.'); return; }
            handlePaymentAndRedirect('upiModal', upiId); 
        });

        document.getElementById('payPhoneButton').addEventListener('click', function() {
            const phoneNumber = document.getElementById('phoneNumberInput').value;
            if (phoneNumber.trim() === '' || !/^\d{10}$/.test(phoneNumber)) { alert('Please enter a valid 10-digit phone number.'); return; }
            handlePaymentAndRedirect('phonePayModal', phoneNumber); 
        });

        document.getElementById('payCardButton').addEventListener('click', function() {
            const cardNumber = document.getElementById('cardNumber').value;
            const cardExpiry = document.getElementById('cardExpiry').value;
            const cardCVC = document.getElementById('cardCVC').value;

            if (cardNumber.trim() === '' || cardExpiry.trim() === '' || cardCVC.trim() === '') { alert('Please fill in all card details.'); return; }
            if (!/^\d{13,19}$/.test(cardNumber.replace(/\s/g, ''))) { alert('Please enter a valid card number.'); return; }
            if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(cardExpiry)) { alert('Please enter a valid expiry date (MM/YY).'); return; }
            if (!/^\d{3,4}$/.test(cardCVC)) { alert('Please enter a valid CVC.'); return; }
            handlePaymentAndRedirect('cardModal', cardNumber.substring(0,4) + '...'); 
        });
    </script>
</body>
</html>
