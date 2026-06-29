<?php
// Simulan ang session para mabasa ang data galing sa booking
session_start();

// Kung walang booking data, ibalik sa pahina ng pag-book
if (!isset($_SESSION['booking_data'])) {
    header("Location: Bookyourevent.html");
    exit;
}

// Kunin ang lahat ng detalye mula sa session
$booking = $_SESSION['booking_data'];

// Kunin ang mga halaga para sa pagkukuwenta
$total_amount = $booking['total_amount'];
$payment_type = $booking['payment_type']; // "Full Payment" o "Reservation Fee Only"
$service_type = $booking['service_type'];

// Itakda ang reservation fee (ayon sa rules mo)
$reservation_fee = 2000;
// Walang reservation fee para sa Tattoo Event
if ($service_type === "Tattoo Event") {
    $reservation_fee = 0;
}

// Kuwentahin ang halaga
if ($payment_type === "Full Payment") {
    $amount_to_pay = $total_amount;
    $remaining_balance = 0;
} else {
    $amount_to_pay = $reservation_fee;
    $remaining_balance = $total_amount - $reservation_fee;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cash Payment</title>

<link href="https://fonts.googleapis.com/css2?family=Domine:wght@400;600&display=swap" rel="stylesheet">

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Plus Jakarta Sans', 'Segoe UI', Roboto, Arial, sans-serif;
  background: #f2f2f2;
  color: #114500;
}
/******************************** TOPBAR ********************************/
.topbar {
  background: #fff;
  display: flex;
  justify-content: space-between;
  padding: 8px 60px;
}

.topbar-center {
  flex: 1;
  text-align: center;
  font-size: 13px;
}

.topbar-right {
  display: flex;
  gap: 15px;
}

.topbar-right img {
  width: 18px;
}
/******************************** NAVBAR ********************************/
.navbar {
  background: #114500;
  display: flex;
  align-items: center;
  padding: 15px 60px;
}

.nav-logo img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
}

.nav-menu {
  flex: 1;
  display: flex;
  justify-content: center;
  gap: 50px;
  list-style: none;
}

.nav-menu a {
  color: white;
  text-decoration: none;
  position: relative;
}

.nav-menu a::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: -3px;
  width: 0%;
  height: 2px;
  background: white;
  transition: 0.3s;
}

.nav-menu a:hover::after {
  width: 100%;
}
/******************************** SCROLL ANIMATION ********************************/
.scroll-animate {
  opacity: 0;
  transform: translateY(50px);
  transition: all 0.8s ease;
}
.scroll-animate.show {
  opacity: 1;
  transform: translateY(0);
}

/******************************** RISE ANIMATION ********************************/
.rise-title,
.rise-desc,
.rise-item {
  opacity: 0;
  transform: translateY(40px);
  transition: opacity 0.8s ease, transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
}
.rise-title.show,
.rise-desc.show,
.rise-item.show {
  opacity: 1;
  transform: translateY(0);
}
/******************************** CONTAINER ********************************/
.container {
  width: 850px;
  margin: 50px auto;
  text-align: center;
}
.title h1 {
  font-size: 34px;
  margin-bottom: 5px;
}
.title p {
  font-size: 14px;
  margin-bottom: 25px;
}
.card {
  border: 2px solid #114500;
  border-radius: 15px;
  padding: 25px;
  margin-bottom: 20px;
  background: #eee;
  text-align: left;
}
.card h3 {
  font-size: 18px;
  margin-bottom: 15px;
}
.section-title{
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 15px;
}

.service-badge{
  display: inline-block;
  background: #114500;
  color: #fff;
  padding: 6px 14px;
  border-radius: 50px;
  font-size: 13px;
  margin-bottom: 18px;
  text-transform: capitalize;
}

.summary-clean{
  margin-top: 15px;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* EACH ROW */
.summary-line{
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 16px;
  border-radius: 12px;
  background: #f6f7f6;
  border: 1px solid rgba(17,69,0,0.08);
}

/* LABEL (LEFT SIDE) */
.summary-line .label{
  font-size: 15px;
  color: #444;
  font-weight: 500;
}

/* VALUE (RIGHT SIDE) */
.summary-line .value{
  font-size: 18px;
  font-weight: 700;
  color: #114500;
}

/* RESERVATION FEE SPECIAL */
.summary-line .value.fee{
  color: #b07a00;
}
.summary-list{
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.summary-item{
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 12px;
  border-radius: 12px;
  background: #f7f8f7;
  transition: 0.2s ease;
}
.summary-item:hover{
  background: #eef5ee;
  transform: translateX(2px);
}
.summary p {
  font-size: 14px;
}
.summary-grid .row {
  display: flex;
  justify-content: space-between;
  padding: 10px 12px;
  background: #f7f7f7;
  border-radius: 10px;
  font-size: 14px;
}
.summary-grid .row span {
  color: #555;
}
.summary-grid .row strong {
  color: #114500;
  font-weight: 600;
}

.amount-banner {
  border: 1.5px solid #114500;
  border-radius: 8px;
  padding: 6px 10px;
  margin: 8px auto;
  font-size: 14px;
  font-weight: 500;
  background: #fff;
  display: block;
  width: fit-content;
  text-align: center;
}
.amount-banner span {
  font-weight: 600;
  margin-left: 10px;
}
.amount-box {
  margin-top: 18px;
  padding: 18px;
  border-radius: 14px;
  background: linear-gradient(135deg, #114500, #1e6b00);
  color: white;
  text-align: center;
}

.amount-box div {
  font-size: 13px;
  opacity: 0.9;
}

.amount-box h2 {
  margin-top: 5px;
  font-size: 28px;
  font-weight: 700;
}
.amount-highlight{
  margin-top: 20px;
  padding: 18px;
  border-radius: 14px;
  background: linear-gradient(135deg, #114500, #1f6d00);
  color: white;
  text-align: center;
}

.amount-highlight p{
  font-size: 13px;
  opacity: 0.9;
}

.amount-highlight h2{
  font-size: 30px;
  margin-top: 5px;
  font-weight: 800;
}

.instruction-card{
  background: #fff;
  border-radius: 18px;
  padding: 25px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.06);
  border: 1px solid rgba(17,69,0,0.12);
}

/* LIST WRAPPER */
.instruction-list{
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-top: 10px;
}

/* EACH ITEM */
.instruction-item{
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px;
  border-radius: 12px;
  background: #f7f8f7;
  transition: 0.2s ease;
}


.instruction-item .icon{
  font-size: 20px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #114500;
  color: white;
  border-radius: 10px;
}

/* TEXT */
.instruction-item .text h4{
  font-size: 15px;
  margin-bottom: 3px;
  color: #114500;
}

.instruction-item .text p{
  font-size: 13px;
  color: #555;
  line-height: 1.4;
}
.instruction-card ul {
  margin-top: 10px;
  padding-left: 18px;
  font-size: 14px;
  color: #333;
}

.instruction-card li {
  margin-bottom: 6px;
}
.confirm-card{
  background: #ffffff;
  border-radius: 18px;
  padding: 28px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.08);
  border: 1px solid rgba(17,69,0,0.12);
}
.confirm-header h3{
  font-size: 20px;
  font-weight: 800;
  color: #114500;
  margin-bottom: 5px;
}
.confirm-header p{
  font-size: 13px;
  color: #666;
  margin-bottom: 20px;
}
.confirm-box label{
  font-size: 13px;
  font-weight: 600;
  color: #444;
}
.confirm-card input.ref-input {
  margin-top: 10px;
  width: 100%;
  padding: 12px 14px;
  border-radius: 12px;
  border: 1.5px solid #ccc;
  outline: none;
  transition: 0.3s;
  font-size: 14px;
}
.confirm-card input.ref-input:focus {
  border-color: #114500;
  box-shadow: 0 0 0 3px rgba(17,69,0,0.15);
}
.confirm-btn{
  width: 100%;
  margin-top: 18px;
  padding: 14px;
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg, #114500, #1f6d00);
  color: white;
  font-weight: 700;
  font-size: 15px;
  cursor: pointer;
  transition: 0.25s ease;
  letter-spacing: 0.3px;
}
.confirm-btn:hover{
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(17,69,0,0.25);
}
button {
  margin-top: 20px;
  padding: 12px 28px;
  background: white;
  color: #114500;
  border: 2px solid #114500;
  border-radius: 25px;
  cursor: pointer;
  transition: 0.3s;
  font-weight: 600;
  font-family: 'Domine', serif;
}
button:hover {
  background: #114500;
  color: white;
  transform: scale(1.05);
}

.input-wrapper{
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 8px;
  background: #f6f7f6;
  border: 1px solid rgba(17,69,0,0.12);
  padding: 12px 14px;
  border-radius: 12px;
  transition: 0.2s ease;
}

.input-wrapper:focus-within{
  border-color: #114500;
  box-shadow: 0 0 0 3px rgba(17,69,0,0.12);
}

.input-wrapper .icon{
  font-size: 16px;
}

.payment-card{
  background: #fff;
  border-radius: 18px;
  padding: 28px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.08);
  border: 1px solid rgba(17,69,0,0.12);
}

.label{
  font-size: 13px;
  color: #666;
}

/* VALUE */
.value{
  font-weight: 700;
  color: #114500;
}

/* RESERVATION FEE SPECIAL COLOR */
.value.fee{
  color: #b07a00;
}

.ref-input{
  border: none;
  outline: none;
  background: transparent;
  width: 100%;
  font-size: 14px;
}

/* helper text */
.hint{
  font-size: 12px;
  color: #777;
  margin-top: 8px;
}
/******************************** FOOTBAR ********************************/
.footbar {
  background: white;
  border-top: 2px solid #114500;
  border-bottom: 2px solid #114500;
  text-align: center;
  padding: 15px 20px;
  font-family: 'Domine', serif;
  color: #114500;
  font-size: 14px;
  margin-top: 40px;
}
/******************************** FOOTER **************************************/
.footer {
  background: #eee;
  padding: 40px 60px;
}

.footer-top {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
}

.footer-logo img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;    
}

.footer-col {
  font-size: 13px;
}

.footer-col h4 {
  margin-bottom: 10px;
  color: #114500;
}

.footer-col p,
.footer-col a {
  display: block;
  color: #114500;
  text-decoration: none;
  margin-bottom: 5px;
}

.footer-bottom {
  text-align: center;
  margin-top: 20px;
  color: #114500;
}

/******************************** GCASH LOGO **************************************/
.gcash img {
  width: 40px;
  height: 40px;         
  border-radius: 50%;    
  object-fit: cover;      
}
</style>
</head>
<body>

<!-------------------------------- TOPBAR ------------------------------------->
<div class="topbar">
      <div class="topbar-center">Planning an event? <a href="Package.html" class="book-link">Book Now</a> and reserve your date with Cafe Bella.</div>

  <div class="topbar-right">
    <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png">
    <img src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png">
  </div>
</div>

<!-------------------------------- NAVBAR ------------------------------------->
  <div class="navbar">
    <div class="nav-logo">
      <a href="index.html">
      <img src="IMAGES/Cafebella.jpg" alt="Cafe Bella Logo">
  </div>

  <ul class="nav-menu">
      <li><a href="index.html">Home</a></li>
      <li><a href="Menu.html">Menu</a></li>
      <li><a href="Package.html" class="active">Packages</a></li>
      <li><a href="#">Location</a></li>
      <li><a href="#">FAQs</a></li>
  </ul>
</div>
<!-------------------------------- PAYMENT ------------------------------------->
<!-- PAYMENT -->
<div class="container rise-item">

  <div class="title rise-title">
    <h1>Cash Payment</h1>
    <p>Complete your reservation payment in cash at our branch</p>
  </div>

  <!-- PAYMENT SUMMARY -->
<!-- PAYMENT SUMMARY -->
<div class="card payment-card rise-item">
  <div class="section-title">Payment Summary</div>

  <!-- SERVICE BADGE -->
  <div class="service-badge">
    <span id="selected-service"><?= htmlspecialchars($service_type) ?></span>
  </div>

  <!-- DETAILS -->
  <div class="summary-clean">

    <div class="summary-line">
      <span class="label">Total Package Price</span>
      <span class="value">₱ <?= number_format($total_amount, 2) ?></span>
    </div>

    <div class="summary-line">
      <span class="label">Reservation Fee</span>
      <span class="value fee">₱ <?= number_format($reservation_fee, 2) ?></span>
    </div>

    <div class="summary-line">
      <span class="label">Remaining Balance</span>
      <span class="value">₱ <?= number_format($remaining_balance, 2) ?></span>
    </div>

  </div>

  <!-- MAIN AMOUNT (HIGHLIGHT) -->
  <div class="amount-highlight rise-item">
    <p>Amount to Pay Now</p>
    <h2 id="amount-now-banner">₱ <?= number_format($amount_to_pay, 2) ?></h2>
  </div>

</div>

  <!-- INSTRUCTIONS -->
<div class="card instruction-card rise-item">
  <div class="section-title">Cash Payment Instructions</div>

  <div class="instruction-list">

    <div class="instruction-item">
      <div class="icon">📍</div>
      <div class="text">
        <h4>Visit Branch</h4>
        <p>Cafe BELLA, Molino Branch to complete your cash payment.</p>
      </div>
    </div>

    <div class="instruction-item">
      <div class="icon">🧾</div>
      <div class="text">
        <h4>Bring Requirements</h4>
        <p>Bring your booking details and reference number.</p>
      </div>
    </div>

    <div class="instruction-item">
      <div class="icon">📌</div>
      <div class="text">
        <h4>Keep Receipt</h4>
        <p>Always keep your receipt as proof of payment.</p>
      </div>
    </div>

  </div>
</div>
  <!-- CONFIRM -->
<!-- CONFIRM PAYMENT (UPGRADED UI) -->
<div class="card confirm-card rise-item">

  <div class="confirm-header">
    <h3>Confirm Payment</h3>
    <p>Enter your reference number to complete the transaction</p>
  </div>

  <div class="confirm-box">

    <label>Reference Number</label>

    <div class="input-wrapper">
      <span class="icon">🔐</span>
      <input class="ref-input" id="ref-input" placeholder="e.g. ABC12345" required>
    </div>

    <div class="hint">
      Make sure your reference number matches your receipt.
    </div>

    <button class="confirm-btn" onclick="confirmPayment()">
      Confirm & Proceed
    </button>

  </div>
</div>

</div>
<!-------------------------------- FOOTBAR ------------------------------------->
<div class="footbar rise-item rise-item">
  Kape lang saglit tapos laban ulit
</div>
<!-------------------------------- FOOTER ------------------------------------->
<div class="footer rise-item ">

  <div class="footer-top rise-item">

    <div class="footer-logo rise-item">
      <img src="IMAGES/Cafebella.jpg">
    </div>

    <div class="footer-col rise-item">
      <h4>Quick Links</h4>
      <a href="index.html">Home</a>
      <a href="Menu.html">Menu</a>
      <a href="Package.html">Event Packages</a>
      <a href="#">Feedback</a>
      <a href="#">Location</a>
      <a href="#">FAQ's</a>
    </div>

    <div class="footer-col rise-item">
      <h4>Services</h4>
      <a href="Package.html">Coffee Booth</a>
      <a href="Package.html">Matcha Booth</a>
      <a href="Package.html">Tattoo Event</a>
    </div>

    <div class="footer-col rise-item">
      <h4>About</h4>
      <a href="#">Contact Our Team</a>
    </div>

    <div class="footer-col rise-item">
      <h4>Socials</h4>
      <a href="https://www.facebook.com/share/1CiwKkiCkY/" target="_blank" rel="noopener">Facebook</a>
      <a href="https://www.tiktok.com/@christiandavidangelo?_r=1&_t=ZS-94tpPJhFWzZ" target="_blank" rel="noopener">Tiktok</a>
    </div>

    <div class="footer-col gcash rise-item">
      <h4>We accept</h4>
      <img src="IMAGES/GCash.png">
    </div>

  </div>

  <div class="footer-bottom rise-item">
    © 2026 Cafe BELLA. All rights reserved.
  </div>
</div>
<script>
/******************************** CONSISTENT RISE ANIMATION ********************************/

const riseElements = document.querySelectorAll(".rise-title, .rise-desc, .rise-item, .event-card");

function handleRise() {
  const triggerPoint = window.innerHeight * 0.85;

  riseElements.forEach(el => {
    const elementTop = el.getBoundingClientRect().top;

    if (elementTop < triggerPoint) {
      el.classList.add("show");
    } else {
      el.classList.remove("show");
    }
  });
}

window.addEventListener("scroll", handleRise);
window.addEventListener("load", handleRise);

/******************************** GENERATE REFERENCE NUMBER ********************************/
document.addEventListener("DOMContentLoaded", function() {
    function generateRef(length = 8) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let ref = '';
        for(let i=0; i<length; i++){
            ref += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return ref;
    }

    document.querySelector(".ref-input").value = generateRef();
});

/******************************** CONFIRM PAYMENT & SAVE TO DATABASE ********************************/
function confirmPayment() {
    const refNumber = document.getElementById("ref-input").value.trim();

    if (!refNumber) {
        alert("Please enter or confirm your reference number.");
        return;
    }

    const formData = new FormData();
    formData.append("payment_reference", refNumber);
    formData.append("payment_method", "CASH");

    fetch("website_php/process_payment.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        if (response.trim() === "success") {
            window.location.href = "Receipt.php";
        } else {
            alert("Error: " + response);
        }
    })
    .catch(err => alert("Connection error: " + err));
}
</script>

</body>
</html>
