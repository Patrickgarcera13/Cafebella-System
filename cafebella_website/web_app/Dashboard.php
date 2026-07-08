<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../website_php/auth_check.php';
require_once '../website_php/database.php';

require_admin();

if (!isAdmin()) {
    header("Location: POS.php");
    exit;
}

// Get current user's full name and role for greeting
$user_stmt = $pdo->prepare("SELECT full_name, role FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$current_user_details = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Set greeting text based on role
$greeting_role = ($current_user_details['role'] === 'Admin') ? 'Admin' : 'Staff';

date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d');

// Total Bookings
$stmt = $pdo->query("SELECT COUNT(*) FROM bookings");
$total_bookings = $stmt->fetchColumn();

// ✅ Pending Requests
$stmt = $pdo->query("SELECT COUNT(*) FROM bookings WHERE booking_status = 'Pending'");
$pending_requests = $stmt->fetchColumn();

// ✅ Scheduled Events (Accepted at darating pa)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE booking_status = 'Accepted' AND event_date >= ?");
$stmt->execute([$today]);
$scheduled_events = $stmt->fetchColumn();

// Low Stock
$stmt = $pdo->query("SELECT COUNT(*) FROM inventory WHERE stock <= reorder_level");
$low_stock = $stmt->fetchColumn();

// Sales Today
$stmt = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM bookings WHERE DATE(created_at) = ?");
$stmt->execute([$today]);
$sales_today = $stmt->fetchColumn();

$recent_stmt = $pdo->query("SELECT id, full_name, total_amount, payment_type, booking_status FROM bookings ORDER BY created_at DESC LIMIT 4");
$recent_bookings = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

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
.dashboard-header {
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
.dashboard-header .header-left h1 {
  font-size: 20px;
  margin: 0;
  color: #114500;
  line-height: 1.2;
}
.dashboard-header .header-left p {
  font-size: 13px;
  margin-top: 6px;
  color: #6b7280;
  line-height: 1.4;
}
.dashboard-header .date-box {
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

.stats {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 16px;
  margin-bottom: 24px;
  align-items: stretch;
  width: 100%;
}

.stat-card {
  background: #ffffff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  border: none;
  border-bottom: 3px solid #4CAF50;
  position: relative;
  min-height: 100px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  overflow: hidden;
}

.stat-card i {
  font-size: 20px;
  color: #2e7d32;
  margin-bottom: 8px;
}

.stat-card div:first-of-type {
  font-size: 14px;
  color: #666;
  margin-bottom: 6px;
  font-weight: 500;
}

.stat-value {
  font-size: 26px;
  font-weight: 700;
  color: #114500;
  line-height: 1.2;
  /* This makes it shrink automatically if too long */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

/* Remove the extra bottom border that was conflicting */
.stat-card::after {
  display: none;
}

.dashboard {
  /* add this */
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 16px;
  width: 100%;
}
/******************************** CHARTS ********************************/
.charts {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
.chart-card {
  background: #ffffff;
  border: 1px solid #e6eaf0;
  border-radius: 14px;
  padding: 14px;
  display: flex;
  flex-direction: column;
  height: 100%;
  box-shadow: 0 4px 14px rgba(0,0,0,0.04);
  overflow: hidden;
}

.chart-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}
.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}
.chart-header h3 {
  font-size: 14px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 2px;
}
.chart-header p {
  font-size: 11px;
  color: #6b7280;
}
.chart-actions {
  display: flex;
  align-items: center;
  gap: 6px;
}
.chart-actions select {
  font-size: 11px;
  padding: 4px 8px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  outline: none;
}
.chart-actions button {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
  background: #fff;
  cursor: pointer;
}
.chart-actions button:hover {
  background: #114500;
  color: white;
}
.chart-body {
  flex: 1;
  position: relative;
  min-height: 180px; /* 🔥 THIS FIXES THE CHART ISSUE */
}
.chart-box {
  background: #ffffff;
  padding: 20px;
  border-radius: 15px;
  height: 300px;      /* ✅ FIX HEIGHT */
  position: relative; /* important */
}
.chart-body canvas {
  width: 100% !important;
  height: 100% !important;
}
.chart-box h3 {
  color: #114500;
  margin-bottom: 15px;
}

/******************************** MIDDLE LAYOUT ********************************/
.middle {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 14px;
  align-items: stretch;
  height: 260px;
}
.bottom {
  margin-top: 20px;
}

.card, .chart-box {
  background: #ffffff;
  padding: 20px;
  border-radius: 14px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  border: 1px solid #f0f0f0;
}
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}
.card-header h3 {
  font-size: 13px;
  font-weight: 700;
  color: #111827;
}
.badge {
  font-size: 10px;
  padding: 3px 8px;
  border-radius: 999px;
  background: #f3f4f6;
  color: #374151;
}

.badge.green {
  background: #c8facc;
  color: #1b5e20;
}
.event-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #eee;
}

.event-item p {
  font-size: 12px;
  color: gray;
}
.status {
  padding: 3px 10px;
  font-size: 11px;
  border-radius: 20px;
  text-align: center;
}

.status.success {
  background: #c8facc;
  color: #1b5e20;
}

.status.pending {
  background: #ffe0b2;
  color: #e65100;
}

/* TRANSACTIONS */
.transaction {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  padding: 12px 0;
  border-bottom: 1px solid #eee;
  font-size: 14px;
}
.transactions-card {
  background: #ffffff;
  border: 1px solid #e6eaf0;
  border-radius: 12px;
  padding: 12px;
  display: flex;
  flex-direction: column;
  height: 100%;
}
.transaction-table {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.transaction-row {
  display: grid;
  grid-template-columns: 1fr 1.5fr 1fr 1fr;
  padding: 7px 8px;
  font-size: 11px;
  border-radius: 6px;
}
.transaction-row.header {
  background: #f9fafb;
  font-size: 10px;
  font-weight: 600;
  color: #6b7280;
  border-radius: 6px;
}
.transaction-row:not(.header):hover {
  background: #f3f4f6;
}
.status.success {
  color: #16a34a;
  font-weight: 600;
}
.status.pending {
  color: #ea580c;
  font-weight: 600;
}
.list {
  list-style: none;
  margin-top: 10px;
}
.list li {
  padding: 12px 10px;
  border-bottom: 1px solid #f1f1f1;
  font-size: 14px;
  display: flex;
  justify-content: space-between;
}
.list li:last-child {
  border-bottom: none;
}
.list li::before {
  content: "●";
  color: #66bb6a;
  margin-right: 8px;
}

.stat-card:hover,
.card:hover,
.chart-box:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
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
<div class="dashboard-header">
  <div class="header-left">
    <h1>Dashboard Overview</h1>
    <p>Monitor bookings, sales, and event activity in real time</p>
  </div>

  <div class="header-right">
    <div class="date-box">
      <i class="fa-solid fa-calendar"></i>
      <span id="todayDate">Today: </span>
    </div>
  </div>
</div>

<!--------------------------------------- DASHBOARD ---------------------------------------------> 
<div class="dashboard-scroll">
  <div class="dashboard">

  <!-- STATS -->
  <div class="stat-card">
  <i class="fa-solid fa-calendar-check"></i>
  <div>Total Bookings</div>
  <div class="stat-value"><?= $total_bookings ?></div>
</div>

<div class="stat-card">
  <i class="fa-solid fa-clock"></i>
  <div>Pending Requests</div>
  <div class="stat-value"><?= $pending_requests ?></div>
</div>

<div class="stat-card">
  <i class="fa-solid fa-calendar-day"></i>
  <div>Scheduled Events</div>
  <div class="stat-value"><?= $scheduled_events ?></div>
</div>

    <div class="stat-card">
  <i class="fa-solid fa-box"></i>
  <div>Low Stock</div>
  <div class="stat-value"><?= $low_stock ?></div>
</div>

   <div class="stat-card">
  <i class="fa-solid fa-peso-sign"></i>
  <div>Sales Today</div>
  <div class="stat-value">₱ <?= number_format($sales_today, 2) ?></div>
</div>
  </div>

<!-- MIDDLE -->
<div class="middle">

  <!-- BOOKING TRENDS -->
<div class="chart-card">

  <div class="chart-header">
    <div>
      <h3>Booking Trends</h3>
      <p>Track your booking performance over time</p>
    </div>

    <div class="chart-actions">
      <select>
        <option>Monthly</option>
        <option>Weekly</option>
        <option>Yearly</option>
      </select>

      <button>
        <i class="fa-solid fa-download"></i>
      </button>
    </div>
  </div>

  <div class="chart-body">
    <canvas id="lineChart"></canvas>
  </div>

</div>
<!-- BOTTOM -->
<div class="bottom">

  <!-- RECENT TRANSACTIONS -->
<!-- RECENT TRANSACTIONS -->
<div class="card transactions-card">

  <div class="card-header">
    <h3>Recent Transactions</h3>
    <span class="badge">Today</span>
  </div>

  <div class="transaction-table">

        <div class="transaction-row header">
      <span>Order ID</span>
      <span>Customer</span>
      <span>Amount</span>
      <span>Status</span>
    </div>

    <?php if (empty($recent_bookings)): ?>
      <div class="transaction-row">
        <span style="grid-column: 1 / -1; text-align:center; color:#666;">No recent transactions yet.</span>
      </div>
    <?php else: ?>
      <?php foreach ($recent_bookings as $row): ?>
      <div class="transaction-row">
        <span>#<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></span>
        <span><?= htmlspecialchars($row['full_name']) ?></span>
        <span>₱ <?= number_format($row['total_amount'], 2) ?></span>
        <span class="status <?= $row['booking_status'] === 'Accepted' ? 'success' : ($row['booking_status'] === 'Pending' ? 'pending' : 'cancelled') ?>">
          <?= htmlspecialchars($row['payment_type'] ?: 'Pending') ?>
        </span>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>

</div>
<!-- ✅ CHART LIBRARY (IMPORTANT) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

/******************************** SIDEBAR *******************************/
sidebarButtons.forEach(btn => {
   btn.addEventListener('click', () => {
    const targetPage = btn.dataset.page;

    sidebarButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

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

/******************************** LINE CHART ********************************/
const ctx = document.getElementById('lineChart');

new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr'],
    datasets: [
      {
        label: 'Bookings',
        data: [12, 19, 8, 15, 22, 10, 25],
        borderColor: '#2e7d32',
        backgroundColor: 'rgba(46,125,50,0.15)',
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointBackgroundColor: '#2e7d32'
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    },
    scales: {
      x: {
        grid: { display: false }
      },
      y: {
        beginAtZero: true,
        grid: { color: '#eee' }
      }
    }
  }
});

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
</script>

</body>
</html>
