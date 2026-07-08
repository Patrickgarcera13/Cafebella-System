<?php
// Simulan ang session at i-check kung naka-login
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../website_php/auth_check.php';
require_once '../website_php/database.php';

require_admin_or_staff();

// Get current user's full name and role for greeting
$user_stmt = $pdo->prepare("SELECT full_name, role FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$current_user_details = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Set greeting text based on role
$greeting_role = ($current_user_details['role'] === 'Admin') ? 'Admin' : 'Staff';

// Kunin ang filter mula sa URL, default ay 'all'
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// --- Kumuha ng datos base sa filter ---
try {
    if ($filter === 'pos') {
    $sql = "SELECT 
                receipt_code, 
                order_date AS created_at,
                customer_name AS full_name,
                order_type AS service_type, 
                payment_method, 
                total_amount, 
                status, // ✅ Dito sa orders table, 'status' pa rin
                order_id,
                'POS' AS source
            FROM orders 
            ORDER BY order_date DESC";
} elseif ($filter === 'online') {
    $sql = "SELECT 
                receipt_code, 
                created_at, 
                full_name, 
                service_type, 
                payment_method, 
                total_amount, 
                booking_status AS status, // ✅ GAWING 'status' PARA TUGMA SA FORMAT
                NULL AS order_id,
                'ONLINE_BOOKING' AS source
            FROM bookings 
            ORDER BY created_at DESC";
} else {
    $sql = "
        SELECT * FROM (
            SELECT receipt_code, created_at, full_name, service_type, payment_method, total_amount, booking_status AS status, NULL AS order_id, 'ONLINE_BOOKING' AS source FROM bookings
            UNION ALL
            SELECT receipt_code, order_date AS created_at, customer_name AS full_name, order_type AS service_type, payment_method, total_amount, status, order_id, 'POS' AS source FROM orders
        ) AS combined
        ORDER BY created_at DESC
    ";
}
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    date_default_timezone_set('Asia/Manila'); // ✅ Itinakda ang tamang oras para sa buong pahina
$totalTransactions = count($transactions);
$totalSales = 0;
$todaySales = 0;
$today = date('Y-m-d');

foreach ($transactions as $trx) {
    $totalSales += $trx['total_amount'];

    // ✅ Simple at siguradong pagbabasa ng petsa
    $txnDate = date('Y-m-d', strtotime($trx['created_at']));

    if ($txnDate === $today) {
        $todaySales += $trx['total_amount'];
    }
}

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Transaction History</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Plus Jakarta Sans', 'Segoe UI', Roboto, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background: #f4f6f9;
}
:root {
  --primary: #16a34a;
  --primary-dark: #15803d;
  --bg: #f8fafc;
  --card: #ffffff;
  --text: #1e293b;
  --subtext: #64748b;
  --border: #e2e8f0;
  --radius: 12px;
}
/******************************** SIDEBAR (PRO UI) ********************************/
.sidebar {
  width: 270px;
  height: 100vh;
  background: 
    linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)),
    url('IMAGES/webapppic.jpg'); 
  background-size: cover;      
  background-position: center;   
  background-repeat: no-repeat;
  display: flex;
  flex-direction: column;
  color: white;
  padding: 20px;
  position: fixed;
  left: 0;
  top: 0;
  box-shadow: 8px 0 25px rgba(0,0,0,0.15);
  z-index: 100;
  transition: all 0.3s ease;
}
.sidebar.hide {
  transform: translateX(-100%);
}

/******************************** MENU BUTTON ********************************/
.menu {
  position: relative;
  margin-top: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid white;
  z-index: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.menu button {
  width: 100%;
  padding: 12px 14px;
  border: none;
  border-radius: 12px;
  background: transparent;
  color: #e8f5e9;
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.25s ease;
}
.menu button.active {
  background: linear-gradient(135deg, #66bb6a, #43a047);
  color: white;
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}
.menu button.active::before {
  content: "";
  position: absolute;
  left: 0;
  height: 20px;
  width: 4px;
  background: #c8facc;
  border-radius: 0 4px 4px 0;
}
.menu button:hover {
  background: rgba(255,255,255,0.08);
  transform: translateX(6px);
}
.menu button img.icon {
  width: 25px;
  height: 25px;
  object-fit: contain;
  display: block;
  filter: brightness(0) invert(1);
}
#menu-btn {
  font-size: 22px;
  background: #114500;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
  margin-right: 15px;
}

/*********************************** MAIN ********************************/
.main {
  height: 100vh;
  margin-left: 270px;
  display: flex;
  flex-direction: column;
  overflow: hidden; 
}
.main.full {
  margin-left: 0;
  gap: 20px;
  flex: 1.2;
  min-width: 0;
}

/******************************** TOP BAR ********************************/
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 25px;
  background: #ffffff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
  border-bottom: 1px solid #eee;
}
.left-section {
  display: flex;
  align-items: center;
  gap: 15px;
}

/******************************** SEARCH BAR ********************************/
.search-container {
  display: flex;
  align-items: center;
  background: #f1f3f6;
  box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
  padding: 8px 15px;
  border-radius: 25px;
  width: 350px;
}

.search-container span {
  margin-right: 10px;
  font-size: 16px;
}
.search-container input {
  border: none;
  background: transparent;
  outline: none;
  width: 100%;
}

/******************************** ADMIN ********************************/
.admin {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  position: relative;
  background: white;
  padding: 8px 15px;
  border-radius: 50px;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  transition: 0.3s;
}
.admin:hover {
  background: #f1f1f1;
}
.admin img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid #2e7d32;
  object-fit: cover;
}
.admin-info {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}
.admin-name {
  font-size: 14px;
  font-weight: 600;
  color: #1b5e20;
}
.admin-role {
  font-size: 11px;
  color: gray;
}
.admin-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-bottom: 18px;
  margin-bottom: 15px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
}
.admin-header h2 {
  font-size: 18px;
  font-weight: 600;
}
.admin-header img {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 2px solid #66bb6a;
}
.arrow {
  font-size: 12px;
  color: #555;
}
.dropdown {
  display: none;
  position: absolute;
  top: 55px;
  right: 0;
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  overflow: hidden;
  z-index: 100;
}
.dropdown button {
  width: 160px;
  padding: 12px;
  border: none;
  background: white;
  text-align: left;
  cursor: pointer;
  font-size: 14px;
}
.dropdown button:hover {
  background: #2e7d32;
  color: white;
}

/******************************** TRANSACTION ********************************/
.transaction-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 20px 24px; 
  margin: 20px 25px;  
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
.transaction-header .header-left h1 {
  font-size: 20px;
  margin: 0;
  color: #114500;
  line-height: 1.2;
}
.transaction-header .header-left p {
  font-size: 13px;
  margin-top: 6px;
  color: #6b7280;
  line-height: 1.4;
}
.transaction-header .date-box {
  background: #f4f6f9;
  padding: 10px 14px; 
  border-radius: 10px;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 8px;
  white-space: nowrap;
  font-weight: 500;
  color: #333;
}
.container {
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  overflow: visible; /* ✅ FIX */
}
/******************************** CONTENT ********************************/
.content {
  display: flex;
  flex: 1;
  gap: 20px;
  overflow: hidden;
  min-height: 0; 
}
.content-wrapper {
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/******************************** KPI ********************************/
.kpi-wrapper {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin: 0px 20px 5px;
}

.kpi-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  padding: 18px 20px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  transition: 0.2s ease;
}

.kpi-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.kpi-label {
  font-size: 13px;
  color: #64748b;
}

.kpi-value {
  font-size: 26px;
  font-weight: 700;
  margin-top: 6px;
  color: #0f172a;
}

/******************************** FILTER BAR  ********************************/
.filter-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 25px;
  margin: 0 25px 20px;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
}
.filter-right button {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  border-radius: 8px;
  border: 1px solid var(--border);
  background: white;
  cursor: pointer;
  transition: 0.2s;
}
.filter-right button:hover {
  background: #f1f5f9;
}

/******************************** SOURCE ********************************/
.src {
  padding: 10px 16px;
  border-radius: 999px;
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
  color: #64748b;

  transition: all 0.25s ease;
  position: relative;
}
.src:hover {
  background: linear-gradient(135deg, #34d399, #16a34a);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(22, 163, 74, 0.25);
}
.src.active {
  background: #16a34a;
  color: white;
  box-shadow: 0 6px 15px rgba(22, 163, 74, 0.25);
}
.source-buttons {
  display: flex;
  gap: 8px;
  background: #f1f5f9;
  padding: 6px;
  border-radius: 999px;
  border: 1px solid #e2e8f0;
}
.src.active::after {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: 999px;
  background: rgba(255,255,255,0.15);
}
/******************************** HISTORY CONTAINER ********************************/
#historyContainer {
  margin: 0 25px 25px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 14px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

/******************************** TABLE ********************************/
.table-container {
  margin: 0 25px 25px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  height: calc(100vh - 320px); 
  overflow-y: auto;
  overflow-x: auto;
}
/******************************** TRANSACTION TABLE ********************************/
.transaction-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Inter', sans-serif;
}
.transaction-table thead {
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: white;
}
.transaction-table thead th {
  position: sticky;
  top: 0;
  z-index: 10;
  background: linear-gradient(135deg, #16a34a, #15803d);
}
.transaction-table th {
  padding: 14px;
  font-size: 13px;
  text-align: left;
  font-weight: 600;
  letter-spacing: 0.3px;
}
.transaction-table td {
  padding: 14px;
  font-size: 14px;
  color: #0f172a;
  border-bottom: 1px solid #f1f5f9;
}
.transaction-table tbody tr:hover {
  background: #f8fafc;
  transition: 0.2s;
}
/******************************** ID / AMOUNT/  ********************************/
.id {
  font-weight: 600;
  color: #1e293b;
}
.amount {
  font-weight: 600;
  color: #0f172a;
}

/******************************** BADGE ********************************/
.badge {
  padding: 5px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

/* SOURCE BADGE */
.badge.pos {
  background: #dbeafe;
  color: #1d4ed8;
}

.badge.online {
  background: #e0f2fe;
  color: #0284c7;
}

/* STATUS BADGE */
.badge.paid {
  background: #dcfce7;
  color: #16a34a;
}

.badge.pending {
  background: #fef9c3;
  color: #ca8a04;
}


/******************************** VIEW TRANSACTION ********************************/
.modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.5);

  justify-content: center;
  align-items: center;

  z-index: 999;
}

.modal-content {
  background: white;
  width: 350px;
  border-radius: 12px;
  padding: 20px;

  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  animation: pop 0.2s ease;
}

@keyframes pop {
  from { transform: scale(0.9); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.modal-content h3 {
  margin-bottom: 15px;
  color: #114500;
}

.modal-content p {
  font-size: 14px;
  margin: 6px 0;
  color: #333;
}

.close-btn {
  margin-top: 15px;
  padding: 8px 12px;
  border: none;
  background: #16a34a;
  color: white;
  border-radius: 8px;
  cursor: pointer;
}
.close-btn:hover {
  background: #15803d;
}

/******************************** UI MODAL ********************************/
.txn-card {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.txn-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.txn-header h2 {
  font-size: 18px;
  color: #114500;
}

.txn-id {
  font-size: 13px;
  color: #64748b;
}

/* STATUS */
.txn-status {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.txn-status.paid {
  background: #dcfce7;
  color: #16a34a;
}

.txn-status.pending {
  background: #fef9c3;
  color: #ca8a04;
}

.txn-status.cancelled {
  background: #fee2e2;
  color: #dc2626;
}

/* GRID */
.txn-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.txn-item {
  display: flex;
  flex-direction: column;
  background: #f8fafc;
  padding: 10px;
  border-radius: 10px;
}

.txn-item .label {
  font-size: 11px;
  color: #64748b;
}

.txn-item .value {
  font-size: 14px;
  font-weight: 600;
  color: #0f172a;
}

/* TOTAL */
.txn-total {
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: white;
  padding: 15px;
  border-radius: 12px;
  text-align: center;
}

.txn-total span {
  font-size: 12px;
  opacity: 0.8;
}

.txn-total h1 {
  margin-top: 5px;
  font-size: 24px;
}
/******************************** VIEW BUTTON ********************************/
.view-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 8px;
  background: #16a34a;
  color: white;
  font-size: 12px;
  cursor: pointer;
  transition: 0.2s;
}

.view-btn:hover {
  background: #15803d;
  transform: scale(1.05);
}
/******************************** MODAL ********************************/
.date-modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  justify-content: center;
  align-items: center;
  z-index: 9999; /* 🔥 increase */
}

.date-content {
  background: white;
  padding: 25px;
  border-radius: var(--radius);
  width: 300px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.date-actions {
  margin-top: 15px;
  display: flex;
  justify-content: space-between;
}

.date-actions button {
  padding: 8px 14px;
  border-radius: 8px;
  border: none;
  cursor: pointer;
}

.date-actions button:first-child {
  background: var(--primary);
  color: white;
}

</style>
</head>
<body>

<!--------------------------------------- SIDEBAR ---------------------------------------------> 
    <div class="sidebar">
      <div class="admin-header">
        <img src="IMAGES/cafebella.jpg" alt="Logo">
        <h2>Hello, <?= htmlspecialchars($greeting_role) ?>!</h2>
      </div>

<!--------------------------------------- MENU SIDEBAR ---------------------------------------------> 
    <div class="menu">
      <?php if(isAdmin()): ?>
      <button data-page="Dashboard.php"><img src="IMAGES/dashboardpic.png" class="icon">Dashboard</button>
      <?php endif; ?>

      <?php if(isAdmin()): ?>
      <button data-page="Calendar.php"><img src="IMAGES/calendaricon.png" class="icon">Calendar</button>
      <?php endif; ?>

      <button data-page="POS.php"><img src="IMAGES/POSicon.png" class="icon">Point of Sale</button>
      <button data-page="Transactionhistory.php"><img src="IMAGES/transactionhistoryicon.png" class="icon">Transaction History</button>
      
      <?php if(isAdmin()): ?>
      <button data-page="Reports.php"><img src="IMAGES/reporticon.png" class="icon">Reports</button>
      <?php endif; ?>
      
      <?php if(isAdmin()): ?>
      <button data-page="Bookingrequest.php"><img src="IMAGES/Bookingicon.png" class="icon">Booking Request</button>
      <?php endif; ?>

      <?php if(isAdmin()): ?>
      <button data-page="Eventmanagement.php"><img src="IMAGES/eventmanagementicon.png" class="icon">Event Management</button>
      <?php endif; ?>
      
      <button data-page="Inventory.php"><img src="IMAGES/inventoryicon.png" class="icon">Inventory</button>
      
      <?php if(isAdmin()): ?>
      <button data-page="Feedback.php"><img src="IMAGES/feedbackicon.png" class="icon">Customer Feedback</button>
      <?php endif; ?>

      <?php if(isAdmin()): ?>
      <button data-page="Settings.php"><img src="IMAGES/settingsicon.png" class="icon">Settings</button>
      <?php endif; ?>
    </div>
    </div> 

<!--------------------------------------- MAIN ---------------------------------------------> 
    <div class="main">

<!--------------------------------------- TOPBAR ---------------------------------------------> 
    <div class="topbar">
      <div class="left-section">
        <button id="menu-btn">☰</button>
        <div class="search-container">
          <span>🔍</span>
          <input type="text" placeholder="Search ...">
        </div>
      </div>

    <div class="admin" onclick="toggleDropdown()">
      <img src="IMAGES/cafebella.jpg" alt="Admin">
      <div class="admin-info">
        <span class="admin-name">Admin</span>
        <span class="admin-role"><?= htmlspecialchars($current_user_details['role']) ?></span>
      </div>
      <span class="arrow">▼</span>
      <div id="adminDropdown" class="dropdown">
        <button onclick="logout()">Logout</button>
      </div>
    </div>
  </div>

<div class="content-wrapper">

<!--------------------------------------- TITLE ---------------------------------------------> 
<div class="transaction-header">
  <div class="header-left">
    <h1>Transaction History</h1>
    <p>Handle your daily transactions and payment</p>
  </div>

  <div class="header-right">
    <div class="date-box">
      <i class="fa-solid fa-calendar"></i>
      <span id="todayDate">Today: </span>
    </div>
  </div>
</div>

<!--------------------------------------- CONTAINER -------------------------------------------->
<div class="container">

    <!---------------------- KPI HEADER ----------------------------> 
    <div class="kpi-wrapper">

      <div class="kpi-card">
        <span class="kpi-label">Total Transactions</span>
        <h2 class="kpi-value"><?= $totalTransactions ?></h2>
      </div>

      <div class="kpi-card">
        <span class="kpi-label">Total Sales</span>
        <h2 class="kpi-value">₱ <?= number_format($totalSales, 2) ?></h2>
      </div>

      <div class="kpi-card">
        <span class="kpi-label">Today's Sales</span>
        <h2 class="kpi-value">₱ <?= number_format($todaySales, 2) ?></h2>
      </div>

    </div>

    <!-- DIVIDER -->
    <div class="divider-line"></div>

<!--------------------------------------- FILTER ---------------------------------------------> 
<div class="filter-bar">

    <!-- LEFT SIDE -->
  <div class="source-buttons">
    <button class="src <?= $filter === 'all' ? 'active' : '' ?>" onclick="setSource('all', this)">ALL</button>
<button class="src <?= $filter === 'pos' ? 'active' : '' ?>" onclick="setSource('POS', this)">POS</button>
<button class="src <?= $filter === 'online' ? 'active' : '' ?>" onclick="setSource('ONLINE_BOOKING', this)">ONLINE BOOKING</button>
  </div>

</div> <!-- ✅ IMPORTANT CLOSING TAG -->

<div class="table-container">

  <table class="transaction-table">

    <thead>
      <tr>
        <th>Transaction ID</th>
        <th>Date & Time</th>
        <th>Customer</th>
        <th>Source</th>
        <th>Payment</th>
        <th>Total</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
        <?php if (!empty($transactions)): ?>
            <?php foreach ($transactions as $trx): ?>
    <?php
    // ✅ Itinakda kung saan galing ang transaksyon
    $source = $trx['source'] ?? 'ONLINE_BOOKING';

    // ✅ Iba ang display ng pangalan depende sa source
    if ($source === 'POS') {
        $source_name = 'POS / ' . $trx['service_type'];
        $customer_name = !empty($trx['full_name']) ? $trx['full_name'] : 'Walk-in';
    } else {
        $source_name = $trx['service_type'] . " Booking";
        $customer_name = $trx['full_name'];
    }

    // ✅ TUGMA SA BAGONG STATUS:
    $status_badge = match($trx['status']) {
    'Accepted', 'Completed', 'Confirmed' => '<span class="badge paid">Approved</span>',
    'Pending' => '<span class="badge pending">Pending</span>',
    'Declined', 'Cancelled' => '<span class="badge cancelled">'.htmlspecialchars($trx['status']).'</span>',
    default => '<span class="badge pending">'.htmlspecialchars($trx['status'] ?? 'Pending').'</span>'
};
    ?>
    <tr data-source="<?= $source ?>">
        <td class="id"><?= htmlspecialchars($trx['receipt_code']) ?></td>
        <td><?= date("M d, Y - g:i A", strtotime($trx['created_at'])) ?></td>
        <td><?= htmlspecialchars($customer_name) ?></td>
        <td><span class="badge <?= strtolower($source) ?>"><?= htmlspecialchars($source_name) ?></span></td>
        <td><?= htmlspecialchars($trx['payment_method']) ?></td>
        <td class="amount">₱ <?= number_format($trx['total_amount'], 2) ?></td>
        <td><?= $status_badge ?></td>
        <td><button class="view-btn" onclick="viewTransaction(this, <?= $source === 'POS' ? 'true' : 'false' ?>, <?= $trx['order_id'] ?? 0 ?>)">View</button></td>
    </tr>
<?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center; padding:2rem; color:#666;">No transactions found.</td>
            </tr>
        <?php endif; ?>
      </tbody>

  </table>

</div>

<!--------------------------------------- DATE MODAL ---------------------------------------------> 
<div id="dateModal" class="date-modal">
    <div class="date-content">
        <h3>Filter by Date</h3>

        <label><input type="radio" name="dateFilter" value="7"> Last 7 Days</label>
        <label><input type="radio" name="dateFilter" value="30"> Last 30 Days</label>
        <label><input type="radio" name="dateFilter" value="90"> Last 90 Days</label>

        <div class="date-actions">
        <button onclick="applyDateFilter()">Apply Filter</button>
        <button onclick="closeDateModal()">Cancel</button>
        </div>
    </div>
</div>

<!--------------------------------------- TRANSACTION MODAL ---------------------------------------------> 
<div id="transactionModal" class="date-modal">
  <div class="date-content">
    <h3>Filter by Transaction</h3>

    <label><input type="radio" name="transactionFilter" value="Cash"> Cash</label>
    <label><input type="radio" name="transactionFilter" value="GCash"> GCash</label>

    <div class="date-actions">
      <button onclick="applyTransactionFilter()">Apply Filter</button>
      <button onclick="closeTransactionModal()">Cancel</button>
    </div>
  </div>
</div>

<!--------------------------------------- AMOUNT MODAL ---------------------------------------------> 
<div id="amountModal" class="date-modal">
  <div class="date-content">
    <h3>Filter by Amount</h3>

    <label><input type="radio" name="amountFilter" value="low"> Low → High</label>
    <label><input type="radio" name="amountFilter" value="high"> High → Low</label>

    <div class="date-actions">
      <button onclick="applyAmountFilter()">Apply Filter</button>
      <button onclick="closeAmountModal()">Cancel</button>
    </div>
  </div>
</div>

<!--------------------------------------- STATUS MODAL ---------------------------------------------> 
<div id="statusModal" class="date-modal">
  <div class="date-content">
    <h3>Filter by Status</h3>

    <label><input type="radio" name="statusFilter" value="Accepted"> Approved</label>
<label><input type="radio" name="statusFilter" value="Pending"> Pending</label>
<label><input type="radio" name="statusFilter" value="Declined"> Declined</label>

    <div class="date-actions">
      <button onclick="applyStatusFilter()">Apply Filter</button>
      <button onclick="closeStatusModal()">Cancel</button>
    </div>
  </div>
</div>
<!-- VIEW TRANSACTION MODAL -->
<div id="viewModal" class="date-modal">
  <div class="date-content">

    <div id="modalBody"></div>

    <div class="date-actions">
      <button onclick="closeModal()">Close</button>
    </div>

  </div>
</div>
</body>
</html>
<script>

/******************************** MENU BUTTON ********************************/
  const sidebarButtons = document.querySelectorAll('.sidebar .menu button');

/******************************** GET THE CURRENT PAGE ********************************/
  function getCurrentPage() {
    return window.location.pathname.split("/").pop(); 
  }

/******************************** HIGHLIGHT THE BUTTON OF THE CURRENT PAGE ********************************/
  function highlightSidebar() {
    const currentPage = getCurrentPage().toLowerCase();
    sidebarButtons.forEach(btn => {
      btn.classList.remove('active');
      if (btn.dataset.page.toLowerCase() === currentPage) {
        btn.classList.add('active');
      }
    });
  }

/******************************** BUTTON HIGHLIGHT ********************************/
  highlightSidebar();

/******************************** NAVIGATE WHEN CLICKED ********************************/
  sidebarButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetPage = btn.dataset.page;

      sidebarButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

/******************************** NAVIGATE THE TARGET PAGE ********************************/
      window.location.href = targetPage;
    });
  });

/******************************** UPDATE THE HIGHLIGHT ON THE BROWSER ********************************/
  window.addEventListener('popstate', () => {
    highlightSidebar();
  });

/******************************** ADMIN DROPDOWN ********************************/
function toggleDropdown() {
  const dropdown = document.getElementById("adminDropdown");
  dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}
function logout() {
  window.location.href = "login.html";
}

window.onclick = function(e) {
  if (!e.target.closest('.admin')) {
    document.getElementById("adminDropdown").style.display = "none";
  }
}

/******************************** SIDEBAR TOGGLE ********************************/
const menuBtn = document.getElementById("menu-btn");
const sidebar = document.querySelector(".sidebar");
const main = document.querySelector(".main");

menuBtn.onclick = function() {
  sidebar.classList.toggle("hide");
  main.classList.toggle("full");
};

/******************************** UPDATE DATE TODAY  ********************************/
function updateTodayDate() {
  const today = new Date();

  const options = {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  };

  const formattedDate = today.toLocaleDateString('en-US', options);

  document.getElementById("todayDate").textContent = "Today: " + formattedDate;
}

updateTodayDate();

/******************************** SET SOURCE (ALL/POS/ONLINE BOOKING)  ********************************/
function setSource(type, btn) {
  // ✅ I-highlight ang napiling button
  document.querySelectorAll('.src').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const rows = document.querySelectorAll('.transaction-table tbody tr');

  rows.forEach(row => {
    const source = row.getAttribute('data-source'); // ✅ Kunin ang value: "POS" o "ONLINE_BOOKING"

    if (type === 'all') {
      row.style.display = ''; // Ipakita lahat
    }
    else if (type === 'POS') {
      // ✅ Ipakita lang kung POS
      row.style.display = (source === 'POS') ? '' : 'none';
    }
    else if (type === 'ONLINE_BOOKING') {
      // ✅ Ipakita lang kung ONLINE BOOKING
      row.style.display = (source === 'ONLINE_BOOKING') ? '' : 'none';
    }
  });
}

/******************************** AUTO APPLY FILTER SA PAG-LOAD ********************************/
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const currentFilter = urlParams.get('filter') || 'all';

    // Hanapin ang tamang button at tawagin ang function
    const buttons = document.querySelectorAll('.src');
    buttons.forEach(btn => {
        const btnType = btn.textContent.trim();
        if ((currentFilter === 'all' && btnType === 'ALL') ||
            (currentFilter === 'pos' && btnType === 'POS') ||
            (currentFilter === 'online' && btnType === 'ONLINE BOOKING')) {
            setSource(currentFilter === 'online' ? 'ONLINE_BOOKING' : currentFilter.toUpperCase(), btn);
        }
    });
});

/******************************** VIEW TRANSACTION ********************************/
function viewTransaction(btn, isPOS = false, orderId = 0) {
  const row = btn.closest("tr");
  const cells = row.querySelectorAll("td");
  const status = cells[6].innerText.toLowerCase();

  // ✅ Kung POS order, kukunin ang mga detalye mula sa server
  if (isPOS && orderId > 0) {
      document.getElementById("modalBody").innerHTML = '<p style="text-align:center; padding:2rem;">Loading details...</p>';
      document.getElementById("viewModal").style.display = "flex";

      // ✅ TAMA:
      fetch('webapp_php/api.php?action=getTransactionDetails&order_id=' + orderId)
          .then(res => res.json())
          .then(data => {
              if (data.status === 'success') {
                  const order = data.order;
                  const items = data.items;

                  let itemsHtml = '';
                  items.forEach(item => {
                      itemsHtml += `
                      <tr>
                          <td>${item.product_name} ${item.variant_name ? `(${item.variant_name})` : ''}</td>
                          <td>${item.quantity}</td>
                          <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                          <td>₱${parseFloat(item.subtotal).toFixed(2)}</td>
                      </tr>`;
                  });

                  const html = `
                  <div class="txn-card">
                    <div class="txn-header">
                      <div>
                        <h2>POS Order Details</h2>
                        <span class="txn-id">${order.receipt_code}</span>
                      </div>
                      <div class="txn-status ${order.status.toLowerCase()}">
                        ${order.status}
                      </div>
                    </div>

                    <div class="txn-grid">
                      <div class="txn-item"><span class="label">Date</span><span class="value">${new Date(order.order_date).toLocaleString()}</span></div>
                      <div class="txn-item"><span class="label">Customer</span><span class="value">${order.customer_name || 'Walk-in'}</span></div>
                      <div class="txn-item"><span class="label">Type</span><span class="value">${order.order_type}</span></div>
                      <div class="txn-item"><span class="label">Payment</span><span class="value">${order.payment_method}</span></div>
                      <div class="txn-item"><span class="label">Discount</span><span class="value">${order.discount_percent > 0 ? order.discount_percent + '%' : 'None'}</span></div>
                    </div>

                    <h4 style="margin:1rem 0 0.5rem;">Items Ordered</h4>
                    <table style="width:100%; border-collapse:collapse; margin-bottom:1rem;">
                      <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
                      <tbody>${itemsHtml}</tbody>
                    </table>

                    <div class="txn-total">
                      <div style="display:flex; justify-content:space-between; margin:0.3rem 0;">
                        <span>Subtotal</span><span>₱${parseFloat(order.subtotal).toFixed(2)}</span>
                      </div>
                      ${order.discount_amount > 0 ? `
                      <div style="display:flex; justify-content:space-between; margin:0.3rem 0;">
                        <span>Discount</span><span>-₱${parseFloat(order.discount_amount).toFixed(2)}</span>
                      </div>` : ''}
                      <div style="display:flex; justify-content:space-between; margin:0.3rem 0; font-weight:bold; font-size:1.1rem;">
                        <span>Total</span><span>₱${parseFloat(order.total_amount).toFixed(2)}</span>
                      </div>
                      ${order.payment_method === 'Cash' ? `
                      <div style="display:flex; justify-content:space-between; margin:0.3rem 0;">
                        <span>Amount Received</span><span>₱${parseFloat(order.amount_received).toFixed(2)}</span>
                      </div>
                      <div style="display:flex; justify-content:space-between; margin:0.3rem 0;">
                        <span>Change</span><span>₱${parseFloat(order.change_amount).toFixed(2)}</span>
                      </div>` : ''}
                    </div>
                  </div>`;

                  document.getElementById("modalBody").innerHTML = html;
              } else {
                  document.getElementById("modalBody").innerHTML = '<p style="color:red;">Error loading details.</p>';
              }
          })
          .catch(err => {
              document.getElementById("modalBody").innerHTML = '<p style="color:red;">Connection error.</p>';
          });

      return;
  }

  // ✅ Para sa Online Booking (gaya ng dati)
  const html = `
    <div class="txn-card">
      <div class="txn-header">
        <div>
          <h2>Transaction Details</h2>
          <span class="txn-id">${cells[0].innerText}</span>
        </div>
        <div class="txn-status ${status}">
          ${cells[6].innerText}
        </div>
      </div>

      <div class="txn-grid">
        <div class="txn-item"><span class="label">Date</span><span class="value">${cells[1].innerText}</span></div>
        <div class="txn-item"><span class="label">Customer</span><span class="value">${cells[2].innerText}</span></div>
        <div class="txn-item"><span class="label">Source</span><span class="value">${cells[3].innerText}</span></div>
        <div class="txn-item"><span class="label">Payment</span><span class="value">${cells[4].innerText}</span></div>
      </div>

      <div class="txn-total">
        <span>Total</span>
        <h1>${cells[5].innerText}</h1>
      </div>
    </div>
  `;

  document.getElementById("modalBody").innerHTML = html;
  document.getElementById("viewModal").style.display = "flex";
}

/******************************* CLOSE MODAL ********************************/
function closeModal() {
  document.getElementById("viewModal").style.display = "none";
}

/******************************* MISSING MODAL FUNCTIONS ********************************/
function closeDateModal() { document.getElementById("dateModal").style.display = "none"; }
function closeTransactionModal() { document.getElementById("transactionModal").style.display = "none"; }
function closeAmountModal() { document.getElementById("amountModal").style.display = "none"; }
function closeStatusModal() { document.getElementById("statusModal").style.display = "none"; }

// Pansamantalang gumagana ang mga filter button
function applyDateFilter() { alert("Date filter applied!"); closeDateModal(); }
function applyTransactionFilter() { alert("Payment filter applied!"); closeTransactionModal(); }
function applyAmountFilter() { alert("Sort order applied!"); closeAmountModal(); }
function applyStatusFilter() { alert("Status filter applied!"); closeStatusModal(); }
</script>
