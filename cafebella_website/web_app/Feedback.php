<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../website_php/auth_check.php'; 
// ... rest of your code
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Feedback</title>

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
  transition: all 0.2s ease-in-out;
}
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background: #f4f6f9;
}
:root {
  --primary: #2e7d32;
  --primary-dark: #1b5e20;
  --bg: #f5f7fa;
  --card-bg: #ffffff;
  --text: #2b2b2b;
  --subtext: #777;
  --border: #e5e7eb;
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
  margin-left: 270px;
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
  padding: 0;
  background: #f4f6f9;
}
.main.full {
  margin-left: 0;
}

/******************************** TOP BAR ********************************/
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #e9e9e9;
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

/******************************** BOOKING HEADER ********************************/
.feedback-header {
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
.feedback-header .header-left h1 {
  font-size: 20px;
  margin: 0;
  color: #114500;
  line-height: 1.2;
}
.feedback-header .header-left p {
  font-size: 13px;
  margin-top: 6px; 
  color: #6b7280;
  line-height: 1.4;
}
.feedback-header .date-box {
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

/******************************** CONTENT ********************************/
.content {
  display: flex;
  flex: 1;
  gap: 20px;
  overflow: hidden;
  min-height: 0; 
}
.content-wrapper{
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  padding: 0; 
}

/* TABLE */
.table-container {
  margin: 0 25px 30px 25px;
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0,0,0,0.06);
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: #114500;
  color: white;
  padding: 10px;
  font-size: 13px;
}

td {
  padding: 10px;
  font-size: 13px;
  border-bottom: 1px solid #eee;
}

.status {
  padding: 4px 8px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
}

.Pending { background: orange; color: white; }
.Reviewed { background: blue; color: white; }
.Resolved { background: green; color: white; }

.view-toggle {
  margin: 0 25px 10px 25px;
}

.view-toggle button {
  background: #114500;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
}

.view-toggle button:hover {
  background: #0d3400;
}
#feedbackTableContainer {
  display: block;
}

.action-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: all 0.25s ease;
}

/* ICON */
.action-btn i {
  font-size: 12px;
}

/* STATUS COLORS */
.action-btn.Pending {
  background: #fff7ed;
  color: #c2410c;
}

.action-btn.Reviewed {
  background: #eff6ff;
  color: #1d4ed8;
}

.action-btn.Resolved {
  background: #ecfdf5;
  color: #047857;
}

/* HOVER EFFECT */
.action-btn:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 6px 14px rgba(0,0,0,0.12);
}

/* OPTIONAL: subtle border for cleaner look */
.action-btn.Pending { border: 1px solid #fed7aa; }
.action-btn.Reviewed { border: 1px solid #bfdbfe; }
.action-btn.Resolved { border: 1px solid #bbf7d0; }

.modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.65);
  justify-content: center;
  align-items: center;
  z-index: 999;
  backdrop-filter: blur(6px);
}
.modal-box {
  width: 520px;
  max-width: 92%;
  border-radius: 18px;
  overflow: hidden;
  background: linear-gradient(145deg, #ffffff, #f3f4f6);
  box-shadow: 0 30px 70px rgba(0,0,0,0.25);
  animation: pop 0.25s ease;
}

.modal-body {
  padding: 22px 24px;
  background: linear-gradient(to bottom, #ffffff, #f9fafb);
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 18px 22px;
  background: linear-gradient(135deg, #114500, #1b5e20);
  color: white;
}
.modal-header h2 {
  font-size: 16px;
  font-weight: 600;
  letter-spacing: 0.3px;
}
.modal-grid {
  padding: 20px 22px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.modal-grid .row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 10px 12px;
  background: #f9fafb;
  border: 1px solid #eef0f3;
  border-radius: 10px;
}
.modal-grid .row.full {
  flex-direction: column;
  align-items: flex-start;
}
.modal-grid label {
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.modal-grid span {
  font-size: 14px;
  color: #111827;
  font-weight: 500;
  text-align: right;
}
.modal-grid .row.full span {
  margin-top: 6px;
  text-align: left;
  color: #374151;
  line-height: 1.5;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}
.status-Pending { background: #fff7ed; color: #c2410c; }
.status-Reviewed { background: #eff6ff; color: #1d4ed8; }
.status-Resolved { background: #ecfdf5; color: #047857; }

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}
.info-card {
  background: linear-gradient(145deg, #ffffff, #f1f5f9);
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 14px 16px;
  transition: 0.25s;
}
.info-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(0,0,0,0.08);
}
.info-card.full {
  grid-column: span 2;
}
.info-card label {
  font-size: 11px;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.info-card p {
  font-size: 14px;
  font-weight: 500;
  color: #111827;
}

.close {
  cursor: pointer;
  font-size: 22px;
  opacity: 0.8;
  transition: 0.2s;
}
.close:hover {
  opacity: 1;
  transform: scale(1.1);
}

/* STARS */
.stars {
  color: #f59e0b;
  font-size: 16px;
}

/* CATEGORY BADGE STYLE */
.category {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  width: fit-content;
}

.category:contains("Positive") {
  background: #ecfdf5;
  color: #047857;
}

.status-wrapper {
  position: relative;
  display: inline-block;
  width: 100%;
}

#d_status {
  width: 100%;
  padding: 10px 36px 10px 14px;
  border-radius: 999px;
  border: 1px solid #e5e7eb;
  font-size: 13px;
  font-weight: 600;
  outline: none;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  background: #f9fafb;
}

/* Custom dropdown arrow */
.status-wrapper::after {
  content: "▾";
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 12px;
  color: #6b7280;
  pointer-events: none;
}

/* STATUS COLORS */
#d_status.Pending {
  background: #fff7ed;
  color: #c2410c;
  border-color: #fed7aa;
}

#d_status.Reviewed {
  background: #eff6ff;
  color: #1d4ed8;
  border-color: #bfdbfe;
}

#d_status.Resolved {
  background: #ecfdf5;
  color: #047857;
  border-color: #bbf7d0;
}

/* HOVER */
#d_status:hover {
  transform: scale(1.05);
}

.view-btn.Pending {
  background: #fff7ed;
  color: #c2410c;
}

.view-btn.Reviewed {
  background: #eff6ff;
  color: #1d4ed8;
}

.view-btn.Resolved {
  background: #ecfdf5;
  color: #047857;
}

.status-legend-bar {
  margin: 0 25px 10px 25px;
  padding: 10px 14px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 12px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

/* TITLE */
.legend-bar {
  margin: 0 25px 12px 25px;
  padding: 14px 16px;
  background: linear-gradient(145deg, #ffffff, #f9fafb);
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  box-shadow: 0 6px 14px rgba(0,0,0,0.05);
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* 🔥 HEADER (THIS FIXES YOUR “Legends:” LOOK) */
.legend-header {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 13px;
  color: #111827;
  letter-spacing: 0.3px;
}

.legend-header i {
  color: #2e7d32;
  font-size: 14px;
}

/* ITEMS */
.legend-items {
  display: flex;
  gap: 10px;
}

/* PILL STYLE */
.legend {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 5px 10px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
}

/* DOT */
.legend .dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

/* COLORS */
.legend.pending {
  background: #fff7ed;
  color: #c2410c;
}
.legend.pending .dot {
  background: #f97316;
}

.legend.reviewed {
  background: #eff6ff;
  color: #1d4ed8;
}
.legend.reviewed .dot {
  background: #3b82f6;
}

.legend.resolved {
  background: #ecfdf5;
  color: #047857;
}
.legend.resolved .dot {
  background: #10b981;
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
<div class="feedback-header">
  <div class="header-left">
    <h1>Customer Feedback</h1>
    <p>Review customer feedback on bookings</p>
  </div>

  <div class="header-right">
    <div class="date-box">
      <i class="fa-solid fa-calendar"></i>
      <span id="todayDate">Today: </span>
    </div>
  </div>
</div>

<div class="legend-bar">
  <div class="legend-header">
    <i class="fa-solid fa-layer-group"></i>
    <span>Legend</span>
  </div>

  <div class="legend-items">
    <div class="legend pending">
      <span class="dot"></span>
      Pending
    </div>

    <div class="legend reviewed">
      <span class="dot"></span>
      Reviewed
    </div>

    <div class="legend resolved">
      <span class="dot"></span>
      Resolved
    </div>
  </div>
</div>
<!-- TABLE -->
<div class="table-container" id="feedbackTableContainer">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Transaction</th>
        <th>Branch</th>
        <th>Rating</th>
        <th>Service</th>
        <th>Category</th>
        <th>Comment</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="feedbackTable"></tbody>
  </table>
</div>

<div id="modal" class="modal">
  <div class="modal-box">
    <div class="modal-header">
      <h2>Feedback Details</h2>
      <span class="close" onclick="closeModal()">×</span>
    </div>

    <div class="modal-body">

      <div class="info-grid">

        <div class="info-card">
          <label>Name</label>
          <p id="d_name"></p>
        </div>

        <div class="info-card">
          <label>Transaction</label>
          <p id="d_transaction"></p>
        </div>

        <div class="info-card">
          <label>Branch</label>
          <p id="d_branch"></p>
        </div>

        <div class="info-card">
          <label>Rating</label>
          <p id="d_rating" class="stars"></p>
        </div>

        <div class="info-card">
          <label>Service</label>
          <p id="d_service"></p>
        </div>

        <div class="info-card">
          <label>Category</label>
          <p id="d_category" class="category"></p>
        </div>

        <div class="info-card full">
          <label>Comment</label>
          <p id="d_comment"></p>
        </div>

        <div class="info-card">
          <label>Date</label>
          <p id="d_date"></p>
        </div>

        <div class="info-card">
          <label>Status</label>
          <div class="status-wrapper">
            <select id="d_status" onchange="updateStatus(); applyStatusStyle();">
              <option value="Pending">Pending</option>
              <option value="Reviewed">Reviewed</option>
              <option value="Resolved">Resolved</option>
            </select>
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

/******************************** SIDEBAR ********************************/
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

let currentIndex = null;

let feedbacks = JSON.parse(localStorage.getItem("feedbacks")) || [
  {
    name: "Juan Dela Cruz",
    transaction: "TXN-2001",
    branch: "Imus Branch",
    rating: 5,
    service: "Excellent Service",
    category: "Positive",
    comment: "Super smooth transaction, very satisfied!",
    date: "4/21/2026 10:15 AM",
    status: "Reviewed"
  },
  {
    name: "Maria Santos",
    transaction: "TXN-2002",
    branch: "Bacoor Branch",
    rating: 3,
    service: "Average",
    category: "Neutral",
    comment: "Okay lang service pero medyo mabagal",
    date: "4/21/2026 11:05 AM",
    status: "Pending"
  },
  {
    name: "Carlo Reyes",
    transaction: "TXN-2003",
    branch: "Dasmariñas Branch",
    rating: 2,
    service: "Poor",
    category: "Negative",
    comment: "Matagal waiting time at hindi attentive staff",
    date: "4/21/2026 11:40 AM",
    status: "Resolved"
  }
];

function renderFeedback() {
  const table = document.getElementById("feedbackTable");
  table.innerHTML = "";

  feedbacks.forEach((fb, index) => {
    table.innerHTML += `
      <tr>
        <td>${fb.name}</td>
        <td>${fb.transaction}</td>
        <td>${fb.branch}</td>
        <td>${"★".repeat(fb.rating)}</td>
        <td>${fb.service}</td>
        <td>${fb.category}</td>
        <td>
          ${fb.comment.length > 40 
            ? fb.comment.substring(0, 40) + "....." 
            : fb.comment}
        </td>
        <td>${fb.date}</td>

        <td>
          <button class="action-btn ${fb.status}" onclick="viewDetails(${index})">
            <i class="fa-solid fa-eye"></i>
            View
          </button>
        </td>
      </tr>
    `;
  });
}

function toggleStatus(index) {
  const states = ["Pending", "Reviewed", "Resolved"];
  let current = feedbacks[index].status;
  let next = states[(states.indexOf(current) + 1) % states.length];

  feedbacks[index].status = next;

  localStorage.setItem("feedbacks", JSON.stringify(feedbacks));
  renderFeedback();
}

renderFeedback();

function updateStatus() {
  if (currentIndex === null) return;

  const newStatus = document.getElementById("d_status").value;

  feedbacks[currentIndex].status = newStatus;

  localStorage.setItem("feedbacks", JSON.stringify(feedbacks));

  renderFeedback();
}

function toggleTableView() {
  const table = document.querySelector(".table-container");

  if (table.style.display === "block") {
    table.style.display = "none";
  } else {
    table.style.display = "block";
  }
}

function viewDetails(index) {
  const fb = feedbacks[index];

  document.getElementById("d_name").innerText = fb.name;
  document.getElementById("d_transaction").innerText = fb.transaction;
  document.getElementById("d_branch").innerText = fb.branch;
  document.getElementById("d_rating").innerHTML =
    `<span style="color:#f59e0b; font-size:16px;">${"★".repeat(fb.rating)}</span>`;
  document.getElementById("d_service").innerText = fb.service;
  document.getElementById("d_category").innerText = fb.category;
  document.getElementById("d_comment").innerText = fb.comment;
  document.getElementById("d_date").innerText = fb.date;
  document.getElementById("d_status").value = fb.status;
  currentIndex = index;

  const modal = document.getElementById("modal");

  modal.style.display = "flex";

  // 🔥 FIX: prevent page content bleed
  document.body.style.overflow = "hidden";
}

function applyStatusStyle() {
  const status = document.getElementById("d_status");
  const value = status.value;

  status.classList.remove("Pending", "Reviewed", "Resolved");
  status.classList.add(value);
}

// call when modal opens
function viewDetails(index) {
  const fb = feedbacks[index];

  document.getElementById("d_name").innerText = fb.name;
  document.getElementById("d_transaction").innerText = fb.transaction;
  document.getElementById("d_branch").innerText = fb.branch;
  document.getElementById("d_rating").innerHTML =
    `<span style="color:#f59e0b;">${"★".repeat(fb.rating)}</span>`;
  document.getElementById("d_service").innerText = fb.service;
  document.getElementById("d_category").innerText = fb.category;
  document.getElementById("d_comment").innerText = fb.comment;
  document.getElementById("d_date").innerText = fb.date;
  document.getElementById("d_status").value = fb.status;
  applyStatusStyle(); // ✅ ADD THIS LINE

  currentIndex = index;

  applyStatusStyle(); // 🔥 ADD THIS

  document.getElementById("modal").style.display = "flex";
  document.body.style.overflow = "hidden";
}

// also update when changed
document.getElementById("d_status").addEventListener("change", applyStatusStyle);


function closeModal() {
  document.getElementById("modal").style.display = "none";
  document.body.style.overflow = "auto";
}
const modal = document.getElementById("modal");

modal.addEventListener("click", function(e) {
  // kapag yung mismong background (modal overlay) ang na-click
  if (e.target === modal) {
    closeModal();
  }
});
</script>
