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
<title>Reports</title>

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
.report-header {
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
.report-header .header-left h1 {
  font-size: 20px;
  margin: 0;
  color: #114500;
  line-height: 1.2;
}
.report-header .header-left p {
  font-size: 13px;
  margin-top: 6px;
  color: #6b7280;
  line-height: 1.4;
}
.report-header .date-box {
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




.report-cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 18px;
  margin: 20px 25px;
}
.report-card {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 16px;
  border-radius: 16px;
  background: #ffffff;
  cursor: pointer;
  border: 1px solid #e5e7eb;
  box-shadow: 0 6px 16px rgba(0,0,0,0.06);
  transition: all 0.25s ease;
  position: relative;
  overflow: hidden;
}
.report-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.12);
}
.report-card.active {
  border: 2px solid #16a34a;
  background: linear-gradient(135deg, #ecfdf5, #ffffff);
}
.report-card h3 {
  font-size: 13px;
  color: #6b7280;
}
.report-card h2 {
  margin-top: 10px;
  font-size: 26px;
  color: #16a34a;
}
.report-card p {
  font-size: 12px;
  color: #6b7280;
}
.report-filters {
  margin: 0 20px 10px;
  display: flex;
  gap: 10px;
}
.report-filters button {
  padding: 6px 10px;
  font-size: 12px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  background: #e5e7eb;
  font-weight: 500;
  transition: 0.2s;
}
.report-filters button.active {
  background: #16a34a;
  color: white;
}
.report-table {
  margin: 0 25px 25px;
  background: white;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  overflow: hidden;
}
.report-table table {
  width: 100%;
  border-collapse: collapse;
}
.report-table thead {
  display: table;
  width: 100%;
  table-layout: fixed;
}
.report-table tbody {
  display: block;
  max-height: 300px; /* adjust mo */
  overflow-y: auto;
}
.report-table tbody tr,
.report-table thead tr {
  display: table;
  width: 100%;
  table-layout: fixed;
}
.report-table th {
  border-bottom: 1px solid #eee;
  padding: 12px;
  font-size: 13px;
  color: #16a34a;
  text-align: left;
}
.report-table td {
  padding: 12px;
  font-size: 13px;
  border-bottom: 1px solid #f1f1f1;
}
.report-table-main {
  width: 100%;
  border-collapse: collapse;
}
.report-table-main th {
  text-align: left;
  padding: 14px;
  font-size: 13px;
  color: #16a34a;
  background: white;
  border-bottom: 1px solid #eee;
}
.report-cards,
.report-tools,
.report-filters {
  flex-shrink: 0;
}
.report-card.sales .card-icon {
  background: #d1fae5;
  color: #16a34a;
}
.report-card.transactions .card-icon {
  background: #dbeafe;
  color: #2563eb;
}
.report-card.profit .card-icon {
  background: #dcfce7;
  color: #15803d;
}
.report-card.best .card-icon {
  background: #fef3c7;
  color: #d97706;
}

.status {
  padding: 5px 10px;
  border-radius: 20px;
  font-size: 12px;
  background: #dcfce7;
  color: #16a34a;
}
.report-tools {
  margin: 0 20px 10px;
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
}

.tool {
  display: flex;
  gap: 8px;
  align-items: center;
}

.tool input {
  padding: 6px 8px;
  font-size: 12px;
  border: 1px solid #ddd;
  border-radius: 10px;
  outline: none;
}

.tool button {
  padding: 6px 8px;
  font-size: 12px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  background: #16a34a;
  color: white;
  font-weight: 500;
}

.tool button:hover {
  background: #15803d;
}

.live-status {
  background: #ecfdf5;
  color: #16a34a;
  padding: 8px 12px;
  border-radius: 10px;
  font-size: 13px;
}
.tool {
  display: flex;
  gap: 8px;
  align-items: center;
  flex-wrap: wrap;
}

.tool input[type="date"] {
  padding: 8px 10px;
  border: 1px solid #ddd;
  border-radius: 10px;
  outline: none;
  font-size: 13px;
}

.tool button {
  padding: 8px 12px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
  background: #16a34a;
  color: white;
}

/* ================= TOOL HOVER EFFECTS ================= */

.tool input[type="text"],
.tool input[type="date"] {
  transition: all 0.2s ease;
  border: 1px solid #ddd;
}

/* 🔍 + 📅 INPUT HOVER */
.tool input[type="text"]:hover,
.tool input[type="date"]:hover {
  border-color: #16a34a;
  box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
  transform: translateY(-1px);
}

/* FOCUS EFFECT (when typing/clicked) */
.tool input[type="text"]:focus,
.tool input[type="date"]:focus {
  border-color: #16a34a;
  box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.25);
}

/* ================= BUTTONS ================= */

.tool button {
  transition: all 0.2s ease;
}

/* 🟢 APPLY / ALL BUTTON HOVER */
.tool button:hover {
  background: #15803d;
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

/* 📤 EXCEL BUTTON (optional highlight) */
.tool button:nth-child(1):hover {
  background: #0ea5e9;
}

/* 📄 PDF BUTTON (optional red accent) */
.tool button:nth-child(2):hover {
  background: #ef4444;
}

/* CLICK EFFECT */
.tool button:active {
  transform: scale(0.96);
}
.table-header {
  width: 100%;
  border-collapse: collapse;
  flex-shrink: 0;
}
.table-header th {
  padding: 14px;
  text-align: left;
  font-size: 13px;
  color: #16a34a;
  background: white;
  border-bottom: 1px solid #eee;
}
/* ===== MODERN TABLE HEADER ===== */
.table-head th {
  background: linear-gradient(135deg, #16a34a, #22c55e);
  color: white;
  font-size: 13px;
  font-weight: 600;
  padding: 14px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border: none;
}

/* ICON STYLE */
.table-head th i {
  margin-right: 6px;
  font-size: 12px;
  opacity: 0.9;
}

/* ROUND CORNERS */
.table-head th:first-child {
  border-top-left-radius: 12px;
}

.table-head th:last-child {
  border-top-right-radius: 12px;
}
.table-scroll {
  max-height: 350px;
  overflow-y: auto;
  display: block;
}
.table-scroll thead th {
  position: sticky;
  top: 0;
  background: white;
  z-index: 2;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.table-body {
  max-height: 280px;   /* 🔥 adjust mo dito (KEY FIX) */
  overflow-y: auto;
  display: block;
}

.table-body td {
  padding: 14px;
  font-size: 14px;
  border-bottom: 1px solid #f1f1f1;
}
.table-body table {
  width: 100%;
  border-collapse: collapse;
}
.table-body-scroll {
  max-height: 350px;   /* scroll height */
  overflow-y: auto;
  display: block;
}
.table-body-scroll table {
  width: 100%;
  border-collapse: collapse;
}
.table-body-scroll td {
  padding: 14px;
  font-size: 14px;
  border-bottom: 1px solid #f1f1f1;
}
.table-body::-webkit-scrollbar {
  width: 8px;
}

.table-body::-webkit-scrollbar-thumb {
  background: #16a34a;
  border-radius: 10px;
}

.table-body::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
}

.modal-content {
  background: white;
  margin: 8% auto;
  padding: 25px;
  width: 400px;
  border-radius: 14px;
  animation: pop 0.25s ease;
}

@keyframes pop {
  from { transform: scale(0.8); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.close {
  float: right;
  font-size: 20px;
  cursor: pointer;
}

/* ===== TOGGLE TABLE FIX ===== */
.report-table {
  display: none;
  margin: 0 25px 25px;
  background: white;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  overflow: hidden;
}

/* default visible */
#sales {
  display: block;
}

/* ACTIVE BUTTON STYLE IMPROVED */
.report-filters button.active {
  background: linear-gradient(135deg, #16a34a, #22c55e);
  color: white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

/* SMOOTH TRANSITION (optional) */
.report-table {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
.card-icon {
  font-size: 30px;
  width: 50px;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f3f4f6;
  border-radius: 12px;
}

.card-top {
  flex-direction: column;
  align-items: center;
  gap: 6px;
}
.card-top .icon {
  font-size: 26px;
}
.card-top .label {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
}
.card-text h3 {
  font-size: 14px;
  font-weight: 700;
  color: #111827;
  margin: 0;
}
.card-text p {
  font-size: 12px;
  color: #6b7280;
  margin-top: 4px;
}
.time-filters {
  display: flex;
  gap: 10px;
  margin: 10px 25px;
}

.time-filters button {
  padding: 8px 14px;
  border: none;
  border-radius: 20px;
  background: #e5e7eb;
  cursor: pointer;
  font-size: 13px;
  transition: 0.2s;
}

.time-filters button.active {
  background: linear-gradient(135deg, #16a34a, #22c55e);
  color: white;
}

.time-filters button:hover {
  transform: translateY(-2px);
}

.date-range-filter {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 25px;
  background: white;
  padding: 12px 14px;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.date-range-filter input {
  padding: 8px 10px;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  outline: none;
  font-size: 13px;
}

.date-range-filter span {
  font-size: 13px;
  color: #6b7280;
}

.date-range-filter button {
  background: #16a34a;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 10px;
  cursor: pointer;
  transition: 0.2s;
}

.date-range-filter button:hover {
  background: #15803d;
  transform: translateY(-2px);
}

.filters-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 10px 25px;
  flex-wrap: wrap;
  gap: 10px;
}

/* LEFT SIDE (Week / Month / Year) */
.quick-filters {
  display: flex;
  gap: 10px;
}

.quick-filters button {
  padding: 8px 14px;
  border: none;
  border-radius: 20px;
  background: #e5e7eb;
  cursor: pointer;
  font-size: 13px;
  transition: 0.2s;
}

.quick-filters button.active {
  background: linear-gradient(135deg, #16a34a, #22c55e);
  color: white;
}

.quick-filters button:hover {
  transform: translateY(-2px);
}

/* RIGHT SIDE (DATE RANGE) */
.date-range {
  display: flex;
  align-items: center;
  gap: 8px;
  background: white;
  padding: 10px 12px;
  border-radius: 14px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}

.date-range input {
  padding: 6px 8px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 13px;
}

.date-range span {
  font-size: 13px;
  color: #6b7280;
}

.date-range button {
  background: #16a34a;
  color: white;
  border: none;
  padding: 7px 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.2s;
}

.date-range button:hover {
  background: #15803d;
  transform: translateY(-2px);
}

.export-buttons {
  display: flex;
  gap: 10px;
}
.export-btn {
  border: none;
  padding: 8px 14px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 13px;
  color: white;
  transition: all 0.2s ease;
}
.export-btn.excel:hover {
  background: linear-gradient(135deg, #15803d, #16a34a);
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(22,163,74,0.35);
}
.export-btn.pdf:hover {
  background: linear-gradient(135deg, #15803d, #14532d);
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(22,163,74,0.35);
}
.export-btn.excel {
  background: linear-gradient(135deg, #16a34a, #22c55e);
}

.export-btn.pdf {
  background: linear-gradient(135deg, #ef4444, #dc2626);
}

.export-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 18px rgba(0,0,0,0.15);
}
.export-btn:active {
  transform: scale(0.96);
}
.export-btn:hover {
  filter: brightness(1.05);
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
<div class="report-header">
  <div class="header-left">
  <h1>Reports</h1>
  <p>Monitor sales, bookings, and system insights</p>
  </div>

  <div class="header-right">
    <div class="date-box">
      <i class="fa-solid fa-calendar"></i>
      <span id="todayDate">Today: </span>
    </div>
  </div>
</div>

<!--------------------------------------- CONTAINER -------------------------------------------->
<div class="filters-bar">

  <!-- QUICK FILTERS -->
  <div class="quick-filters">
    <button class="active" onclick="setFilter('week', this)">Week</button>
    <button onclick="setFilter('month', this)">Month</button>
    <button onclick="setFilter('year', this)">Year</button>
  </div>

  <!-- DATE RANGE -->
  <div class="date-range">
    <input type="date" id="fromDate">
    <span>to</span>
    <input type="date" id="toDate">
    <button onclick="applyDateFilter()">Apply Filter</button>
  </div>

  <div class="export-buttons">
    <button class="export-btn excel" onclick="exportExcel()">📥 Excel</button>
    <button class="export-btn pdf" onclick="exportPDF()">📄 PDF</button>
  </div>

</div>
  <!-- TOGGLE BUTTONS -->
<div class="report-cards">

  <div class="report-card active sales" onclick="showTable('sales', this)">
    <div class="card-text">
      <h3>Total Sales Table</h3>
      <p>View all sales records</p>
    </div>
  </div>

  <div class="report-card transactions" onclick="showTable('transactions', this)">
    <div class="card-text">
      <h3>Total Transactions Table</h3>
      <p>All order history</p>
    </div>
  </div>

  <div class="report-card profit" onclick="showTable('profit', this)">
    <div class="card-text">
      <h3>Total Profit Table</h3>
      <p>Revenue vs cost</p>
    </div>
  </div>

  <div class="report-card best" onclick="showTable('best', this)">
    <div class="card-text">
      <h3>Best Selling Table</h3>
      <p>Top performing products</p>
    </div>
  </div>

</div>

  <!-- ================= SALES ================= -->
  <div id="sales" class="report-table">
    <table>
      <thead class="table-head">
        <tr>
          <th>Date</th>
          <th>Order ID</th>
          <th>Menu Item</th>
          <th>Cups Sold</th>
          <th>Total Sales</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>2026-04-20</td>
          <td>ORD001</td>
          <td>Cappuccino</td>
          <td>2</td>
          <td>₱260</td>
        </tr>
        <tr>
          <td>2026-04-20</td>
          <td>ORD002</td>
          <td>Iced Latte</td>
          <td>1</td>
          <td>₱150</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- ================= TRANSACTIONS ================= -->
  <div id="transactions" class="report-table" style="display:none;">
    <table>
      <thead class="table-head">
        <tr>
          <th>Order ID</th>
          <th>Date & Time</th>
          <th>Customer Type</th>
          <th>Payment</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>ORD001</td>
          <td>2026-04-20 10:30 AM</td>
          <td>Dine-in</td>
          <td>Cash</td>
          <td><span class="status">Completed</span></td>
        </tr>
        <tr>
          <td>ORD002</td>
          <td>2026-04-20 11:00 AM</td>
          <td>Takeout</td>
          <td>GCash</td>
          <td><span class="status">Completed</span></td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- ================= PROFIT ================= -->
  <div id="profit" class="report-table" style="display:none;">
    <table>
      <thead class="table-head">
        <tr>
          <th>Date</th>
          <th>Order ID</th>
          <th>Total Revenue</th>
          <th>Ingredient Cost</th>
          <th>Profit</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>2026-04-20</td>
          <td>ORD001</td>
          <td>₱260</td>
          <td>₱120</td>
          <td>₱140</td>
        </tr>
        <tr>
          <td>2026-04-20</td>
          <td>ORD002</td>
          <td>₱150</td>
          <td>₱70</td>
          <td>₱80</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- ================= BEST SELLING ================= -->
  <div id="best" class="report-table" style="display:none;">
    <table>
      <thead class="table-head">
        <tr>
          <th>Drink</th>
          <th>Category</th>
          <th>Cups Sold</th>
          <th>Total Sales</th>
          <th>Profit Share</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Cappuccino</td>
          <td>Hot Coffee</td>
          <td>50</td>
          <td>₱6,500</td>
          <td>35%</td>
        </tr>
        <tr>
          <td>Iced Latte</td>
          <td>Cold Coffee</td>
          <td>40</td>
          <td>₱6,000</td>
          <td>30%</td>
        </tr>
      </tbody>
    </table>
  </div>

</div>
</div>


<!-- 📊 Excel Library -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<!-- 📄 PDF Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- 📄 PDF Table Plugin (IMPORTANT) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

</body>
</html>
<script>

let currentFilter = "week";
let customFromDate = null;
let customToDate = null;

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

function showTable(tableId, el) {
  const tables = document.querySelectorAll('.report-table');
  const cards = document.querySelectorAll('.report-card');

  tables.forEach(t => t.style.display = 'none');
  cards.forEach(c => c.classList.remove('active'));

  document.getElementById(tableId).style.display = 'block';
  el.classList.add('active');
}

function saveTransaction(data) {
  let transactions = JSON.parse(localStorage.getItem("transactions")) || [];
  transactions.push(data);
  localStorage.setItem("transactions", JSON.stringify(transactions));
}

saveTransaction({
  id: "TXN002",
  date: new Date().toLocaleString(),
  customer: "Walk-in",
  payment: "Cash",
  status: "Completed",
  items: "Cappuccino",
  qty: 2,
  total: 300,
  cost: 180
});

function loadReports() {
  const data = JSON.parse(localStorage.getItem("transactions")) || [];

  let totalSales = 0;
  let totalProfit = 0;

  const salesTable = document.querySelector("#sales tbody");
  const transactionTable = document.querySelector("#transactions tbody");
  const profitTable = document.querySelector("#profit tbody");

  salesTable.innerHTML = "";
  transactionTable.innerHTML = "";
  profitTable.innerHTML = "";

  data.forEach(t => {
    if (!isWithinRange(t.date)) return;

    totalSales += t.total;
    totalProfit += (t.total - t.cost);

    salesTable.innerHTML += `
      <tr>
        <td>${t.date}</td>
        <td>${t.id}</td>
        <td>${t.items}</td>
        <td>${t.qty}</td>
        <td>₱${t.total}</td>
      </tr>
    `;
  });

    transactionTable.innerHTML += `
      <tr>
        <td>${t.id}</td>
        <td>${t.date}</td>
        <td>${t.customer}</td>
        <td>${t.payment}</td>
        <td><span class="status">${t.status}</span></td>
      </tr>
    `;

    let profit = t.total - t.cost;

    profitTable.innerHTML += `
      <tr>
        <td>${t.date}</td>
        <td>${t.id}</td>
        <td>₱${t.total}</td>
        <td>₱${t.cost}</td>
        <td>₱${profit}</td>
      </tr>
    `;
  };

  // 🔥 UPDATE CARDS
  document.getElementById("salesTotal").textContent = "₱" + totalSales;
  document.getElementById("orderCount").textContent = data.length;
  document.getElementById("profitTotal").textContent = "₱" + totalProfit;


loadReports();

function loadBestSelling() {
  const data = JSON.parse(localStorage.getItem("transactions")) || [];
  const bestTable = document.querySelector("#best tbody");

  let products = {};

  data.forEach(t => {
    if (!isWithinRange(t.date)) return;

    if (!products[t.items]) {
      products[t.items] = {
        units: 0,
        sales: 0,
        profit: 0
      };
    }

    products[t.items].units += t.qty;
    products[t.items].sales += t.total;
    products[t.items].profit += (t.total - t.cost);
  });

  // 🔹 STEP 2: FIND TOP DRINK
  let topName = "-";
  let maxUnits = 0;

  Object.keys(products).forEach(name => {
    let p = products[name];

    if (p.units > maxUnits) {
      maxUnits = p.units;
      topName = name;
    }
  });

  // 👉 UPDATE CARD UI
  document.getElementById("topDrink").textContent = topName;

  // 🔹 STEP 3: DISPLAY TABLE
  bestTable.innerHTML = "";

  Object.keys(products).forEach(name => {
    let p = products[name];

    bestTable.innerHTML += `
      <tr>
        <td>${name}</td>
        <td>Coffee</td>
        <td>${p.units}</td>
        <td>₱${p.sales}</td>
        <td>₱${p.profit}</td>
      </tr>
    `;
  });
}

function setFilter(filter, el) {
  currentFilter = filter;

  document.querySelectorAll('.quick-filters button')
    .forEach(btn => btn.classList.remove('active'));

  el.classList.add('active');

  // reset custom filter
  customFromDate = null;
  customToDate = null;

  loadReports();
  loadBestSelling();
}

// 👇 ILAGAY MO DITO
function isWithinRange(dateString) {
  const now = new Date();
  const date = new Date(dateString);

  if (currentFilter === "week") {
    const weekAgo = new Date();
    weekAgo.setDate(now.getDate() - 7);
    return date >= weekAgo;
  }

  if (currentFilter === "month") {
    return date.getMonth() === now.getMonth() &&
           date.getFullYear() === now.getFullYear();
  }

  if (currentFilter === "year") {
    return date.getFullYear() === now.getFullYear();
  }

  return true;
}

function applyDateFilter() {
  const from = document.getElementById("fromDate").value;
  const to = document.getElementById("toDate").value;

  customFromDate = from ? new Date(from) : null;
  customToDate = to ? new Date(to) : null;

  loadReports();
  loadBestSelling();
}

function isWithinRange(dateString) {
  const date = new Date(dateString);

  // CUSTOM RANGE HAS PRIORITY
  if (customFromDate && customToDate) {
    return date >= customFromDate && date <= customToDate;
  }

  const now = new Date();

  if (currentFilter === "week") {
    const weekAgo = new Date();
    weekAgo.setDate(now.getDate() - 7);
    return date >= weekAgo;
  }

  if (currentFilter === "month") {
    return date.getMonth() === now.getMonth() &&
           date.getFullYear() === now.getFullYear();
  }

  if (currentFilter === "year") {
    return date.getFullYear() === now.getFullYear();
  }

  return true;
}

function exportExcel() {
  const table = document.querySelector(".report-table:not([style*='display: none']) table");
  const wb = XLSX.utils.table_to_book(table, { sheet: "Report" });
  XLSX.writeFile(wb, "coffee-report.xlsx");
}

function exportPDF() {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF();

  const table = document.querySelector(".report-table:not([style*='display: none']) table");

  let rows = [];
  let headers = [];

  table.querySelectorAll("thead th").forEach(th => {
    headers.push(th.innerText);
  });

  table.querySelectorAll("tbody tr").forEach(tr => {
    let row = [];
    tr.querySelectorAll("td").forEach(td => {
      row.push(td.innerText);
    });
    rows.push(row);
  });

  doc.text("Coffee Shop Report", 14, 15);
  doc.autoTable({
    head: [headers],
    body: rows,
    startY: 25
  });

  doc.save("coffee-report.pdf");
}
</script>
