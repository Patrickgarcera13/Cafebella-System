<?php
session_start();

// ✅ Check lang kung may booking at receipt code
if (!isset($_SESSION['booking_data']) || !isset($_SESSION['receipt_code'])) {
    header("Location: Bookyourevent.php");
    exit;
}

$booking = $_SESSION['booking_data'];
$receipt_code = $_SESSION['receipt_code'];

$reservation_fee = ($booking['service_type'] === "Tattoo Event") ? 0 : 2000;
if ($booking['payment_type'] === "Full Payment") {
    $amount_paid = $booking['total_amount'];
    $remaining_balance = 0;
} else {
    $amount_paid = $reservation_fee;
    $remaining_balance = $booking['total_amount'] - $reservation_fee;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Receipt - Cafe Bella</title>

<link href="https://fonts.googleapis.com/css2?family=Domine&display=swap" rel="stylesheet">

<!-- PDF LIBRARY -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
body {
    font-family: 'Domine', serif;
    background: #ddd;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    padding: 20px;
}

/* RECEIPT */
.receipt {
    background: #ffffff;
    padding: 25px;
    border-radius: 15px;
    width: 400px;
    max-width: 100%;
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
    color: #114500;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-top: 6px solid #114500;
}

.logo {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #114500;
    display: block;
    margin: 0 auto;
}

h2 {
    text-align: center;
    margin: 10px 0;
}

/* DIVIDER */
.line {
    border-top: 1px dashed #114500;
    margin: 15px 0;
}

.details {
    text-align: left;
    font-size: 14px;
    line-height: 1.8;
}

#total {
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    margin: 10px 0;
}

/* QR SMALL */
#qrImg {
    width: 90px;
    height: 90px;
}

.qr {
    text-align: center;
    margin: 10px 0;
}

button {
    background: #114500;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 25px;
    cursor: pointer;
    width: 100%;
    font-family: 'Domine', serif;
    font-size: 15px;
    margin-top: 15px;
}

button:hover {
    background: #0d3600;
}
</style>
</head>

<body>

<div class="receipt" id="receipt">

    <div>
        <img src="IMAGES/Cafebella.jpg" class="logo" alt="Cafe Bella Logo">
        <h2>Booking Receipt</h2>

        <div class="line"></div>

        <div class="details">
            <p><strong>Receipt Code:</strong> <?= htmlspecialchars($receipt_code) ?></p>
            <p><strong>Full Name:</strong> <?= htmlspecialchars($booking['full_name']) ?></p>
            <p><strong>Contact:</strong> <?= htmlspecialchars($booking['contact_number']) ?></p>
            <p><strong>Service:</strong> <?= htmlspecialchars($booking['service_type']) ?></p>
            <p><strong>Guests:</strong> <?= htmlspecialchars($booking['guest_count']) ?></p>
            <p><strong>Event Date:</strong> <?= date("F j, Y", strtotime($booking['event_date'])) ?></p>
            <p><strong>Event Time:</strong> <?= date("g:i A", strtotime($booking['event_time'])) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($booking['street_address']) ?>, <?= htmlspecialchars($booking['barangay']) ?>, <?= htmlspecialchars($booking['city']) ?>, <?= htmlspecialchars($booking['province']) ?></p>
            <p><strong>Payment Type:</strong> <?= htmlspecialchars($booking['payment_type']) ?></p>
            <p><strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method']) ?></p>
        </div>
    </div>

    <div>
        <div class="line"></div>

        <div id="total">
            <p>Total Package Price: ₱ <?= number_format($booking['total_amount'], 2) ?></p>
            <p>Amount Paid: ₱ <?= number_format($amount_paid, 2) ?></p>
            <p>Remaining Balance: ₱ <?= number_format($remaining_balance, 2) ?></p>
        </div>

        <p style="text-align:center; font-weight:500;">Thank you! 🎉</p>

        <div class="qr">
            <img id="qrImg" alt="QR Code">
        </div>

        <button onclick="downloadPDF()">⬇ Download Receipt as PDF</button>
    </div>

</div>

<script>
// Ilagay ang QR code gamit ang Receipt Code
document.getElementById("qrImg").src = `https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=Receipt:<?= urlencode($receipt_code) ?>`;

// ✅ PDF DOWNLOAD FUNCTION
function downloadPDF() {
    const element = document.getElementById("receipt");

    const opt = {
        margin:       0.5,
        filename:     `CafeBella_Receipt_<?= $receipt_code ?>.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>
