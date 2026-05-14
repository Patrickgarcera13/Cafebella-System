<?php
if (session_status() == PHP_SESSION_NONE) {
}
require_once '../website_php/auth_check.php';
// ... rest of your code
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
        <h2>Hello, Admin!</h2>
      </div>

<!--------------------------------------- MENU SIDEBAR ---------------------------------------------> 
    <div class="menu">
      <button data-page="Dashboard.php"><img src="IMAGES/dashboardpic.png" class="icon">Dashboard</button>
      <button data-page="Calendar.php"><img src="IMAGES/calendaricon.png" class="icon">Calendar</button>
      <button data-page="POS.php"><img src="IMAGES/POSicon.png" class="icon">Point of Sale</button>
      <button data-page="Transactionhistory.php"><img src="IMAGES/transactionhistoryicon.png" class="icon">Transaction History</button>
      <button data-page="Reports.php"><img src="IMAGES/reporticon.png" class="icon">Reports</button>
      <button data-page="Bookingrequest.php"><img src="IMAGES/Bookingicon.png" class="icon">Booking Request</button>
      <button data-page="Eventmanagement.php"><img src="IMAGES/eventmanagementicon.png" class="icon">Event Management</button>
      <button data-page="Inventory.php"><img src="IMAGES/inventoryicon.png" class="icon">Inventory</button>
      <button data-page="Feedback.php"><img src="IMAGES/feedbackicon.png" class="icon">Customer Feedback</button>
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
        <span class="admin-role">Administrator</span>
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
        <h2 class="kpi-value" id="totalTx">0</h2>
      </div>

      <div class="kpi-card">
        <span class="kpi-label">Total Sales</span>
        <h2 class="kpi-value" id="totalSales">₱0.00</h2>
      </div>

      <div class="kpi-card">
        <span class="kpi-label">Today's Sales</span>
        <h2 class="kpi-value" id="todaySales">₱0.00</h2>
      </div>

    </div>

    <!-- DIVIDER -->
    <div class="divider-line"></div>

<!--------------------------------------- FILTER ---------------------------------------------> 
<div class="filter-bar">

    <!-- LEFT SIDE -->
    <div class="source-buttons">
        <button class="src active" onclick="setSource('all', this)">ALL</button>
        <button class="src" onclick="setSource('POS', this)">POS</button>
        <button class="src" onclick="setSource('ONLINE_BOOKING', this)">ONLINE BOOKING</button>
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

      <!-- ================= POS TRANSACTIONS ================= -->

      <tr data-source="POS">
        <td class="id">TXN-001</td>
        <td>Apr 18, 2026 - 2:30 PM</td>
        <td>Juan Dela Cruz</td>
        <td><span class="badge pos">POS</span></td>
        <td>GCash</td>
        <td class="amount">₱180.00</td>
        <td><span class="badge paid">Paid</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

      <tr data-source="POS">
        <td class="id">TXN-002</td>
        <td>Apr 18, 2026 - 3:10 PM</td>
        <td>Maria Santos</td>
        <td><span class="badge pos">POS</span></td>
        <td>Cash</td>
        <td class="amount">₱250.00</td>
        <td><span class="badge paid">Paid</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

      <tr data-source="POS">
        <td class="id">TXN-003</td>
        <td>Apr 18, 2026 - 4:05 PM</td>
        <td>Mark Reyes</td>
        <td><span class="badge pos">POS</span></td>
        <td>GCash</td>
        <td class="amount">₱320.00</td>
        <td><span class="badge pending">Pending</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

      <!-- ================= ONLINE BOOKING: COFFEE ================= -->

<tr data-source="ONLINE_BOOKING">
        <td class="id">TXN-004</td>
        <td>Apr 18, 2026 - 5:20 PM</td>
        <td>Anna Lopez</td>
        <td><span class="badge online">Coffee Booking</span></td>
        <td>GCash</td>
        <td class="amount">₱5,000</td>
        <td><span class="badge paid">Paid</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

<tr data-source="ONLINE_BOOKING">
        <td class="id">TXN-005</td>
        <td>Apr 18, 2026 - 6:00 PM</td>
        <td>Kevin Cruz</td>
        <td><span class="badge online">Coffee Booking</span></td>
        <td>Cash</td>
        <td class="amount">₱5,000</td>
        <td><span class="badge pending">Pending</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

<tr data-source="ONLINE_BOOKING">
        <td class="id">TXN-006</td>
        <td>Apr 18, 2026 - 6:45 PM</td>
        <td>Sarah Lim</td>
        <td><span class="badge online">Coffee Booking</span></td>
        <td>GCash</td>
        <td class="amount">₱5,000</td>
        <td><span class="badge paid">Paid</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

      <!-- ================= ONLINE BOOKING: MATCHA ================= -->

<tr data-source="ONLINE_BOOKING">
        <td class="id">TXN-007</td>
        <td>Apr 18, 2026 - 7:10 PM</td>
        <td>Yuki Tan</td>
        <td><span class="badge online">Matcha Booking</span></td>
        <td>GCash</td>
        <td class="amount">₱9,000</td>
        <td><span class="badge paid">Paid</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

<tr data-source="ONLINE_BOOKING">
        <td class="id">TXN-008</td>
        <td>Apr 18, 2026 - 7:40 PM</td>
        <td>Nicole Garcia</td>
        <td><span class="badge online">Matcha Booking</span></td>
        <td>Cash</td>
        <td class="amount">₱9,000</td>
        <td><span class="badge pending">Pending</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

<tr data-source="ONLINE_BOOKING">
        <td class="id">TXN-009</td>
        <td>Apr 18, 2026 - 8:15 PM</td>
        <td>James Park</td>
        <td><span class="badge online">Matcha Booking</span></td>
        <td>GCash</td>
        <td class="amount">₱9,000</td>
        <td><span class="badge paid">Paid</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

      <!-- ================= ONLINE BOOKING: TATTOO ================= -->

<tr data-source="ONLINE_BOOKING">
        <td class="id">TXN-010</td>
        <td>Apr 18, 2026 - 9:00 PM</td>
        <td>Daniel Rivera</td>
        <td><span class="badge online">Tattoo Booking</span></td>
        <td>GCash</td>
        <td class="amount">₱1,000</td>
        <td><span class="badge pending">Pending</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

<tr data-source="ONLINE_BOOKING">
        <td class="id">TXN-011</td>
        <td>Apr 18, 2026 - 9:30 PM</td>
        <td>Michael Tan</td>
        <td><span class="badge online">Tattoo Booking</span></td>
        <td>Cash</td>
        <td class="amount">₱1,000</td>
        <td><span class="badge paid">Paid</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

<tr data-source="ONLINE_BOOKING">
        <td class="id">TXN-012</td>
        <td>Apr 18, 2026 - 10:10 PM</td>
        <td>Chris Gomez</td>
        <td><span class="badge online">Tattoo Booking</span></td>
        <td>GCash</td>
        <td class="amount">₱1,000</td>
        <td><span class="badge cancelled">Cancelled</span></td>
        <td><button class="view-btn" onclick="viewTransaction(this)">View</button></td>
      </tr>

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

    <label><input type="radio" name="statusFilter" value="Paid"> Paid</label>
    <label><input type="radio" name="statusFilter" value="Pending"> Pending</label>
    <label><input type="radio" name="statusFilter" value="Cancelled"> Cancelled</label>

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

  // active button UI
  document.querySelectorAll('.src').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const rows = document.querySelectorAll('.transaction-table tbody tr');

  rows.forEach(row => {
    const source = row.getAttribute('data-source');

    if (type === 'all') {
      row.style.display = '';
    }
    else if (type === 'POS') {
      row.style.display = (source === 'POS') ? '' : 'none';
    }
    else if (type === 'ONLINE_BOOKING') {
      row.style.display = (source === 'ONLINE_BOOKING') ? '' : 'none';
    }
  });
}

/******************************** VIEW TRANSACTION ********************************/
function viewTransaction(btn) {
  const row = btn.closest("tr");
  const cells = row.querySelectorAll("td");

const status = cells[6].innerText.toLowerCase();

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

      <div class="txn-item">
        <span class="label">Date</span>
        <span class="value">${cells[1].innerText}</span>
      </div>

      <div class="txn-item">
        <span class="label">Customer</span>
        <span class="value">${cells[2].innerText}</span>
      </div>

      <div class="txn-item">
        <span class="label">Source</span>
        <span class="value">${cells[3].innerText}</span>
      </div>

      <div class="txn-item">
        <span class="label">Payment</span>
        <span class="value">${cells[4].innerText}</span>
      </div>

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
</script>
