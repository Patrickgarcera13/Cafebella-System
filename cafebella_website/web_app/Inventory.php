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
<title>Inventory</title>

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

/* ================= CONTAINER ================= */
.inventory-actions {
  margin: 0 25px 10px 25px;
  display: flex;
  justify-content: flex-end;
}

.inventory-container {
  margin: 20px 25px;
  background: #ffffff;
  border-radius: 16px;
  padding: 0; /* 🔥 remove padding para flush */
  box-shadow: 0 8px 25px rgba(0,0,0,0.05);
  border: 1px solid #eef2f7;
  height: 500px; /* 🔥 fixed height = smooth scroll */
  overflow: auto;
  scroll-behavior: smooth; /* 🔥 smooth scroll */
  scrollbar-width: thin;
  scrollbar-color: #16a34a #f1f5f9;
}

/* ================= TABLE ================= */
.inventory-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}
.inventory-table tbody {
  position: relative;
  z-index: 1;
}
.inventory-table tbody td {
  padding: 16px;
}
/* ================= HEADER ================= */
.inventory-table thead th {
  position: sticky;
  top: 0;
  z-index: 50;
  background: #ffffff;
  background-image: linear-gradient(135deg, #114500, #1b5e20);
  color: #fff;
  padding: 16px;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  font-weight: 600;
  text-align: center;
  white-space: nowrap;
}
.inventory-table thead th i {
  margin-left: 6px;
  font-size: 10px;
  opacity: 0.8;
  cursor: pointer; /* 👈 important */
  user-select: none;
}
.inventory-table thead th::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: -1px;
  width: 100%;
  height: 10px;
  background: linear-gradient(to bottom, rgba(0,0,0,0.15), transparent);
}
.inventory-table thead th:hover {
  background: linear-gradient(135deg, #166534, #14532d);
}
.inventory-table tbody tr:last-child td {
  border-bottom: none;
}
/* ================= CELLS ================= */
.inventory-table td {
  padding: 16px;
  font-size: 14px;
  text-align: center;
  border-bottom: 1px solid #eef2f7;
  vertical-align: middle;
}

/* ================= ROW ================= */
.inventory-row {
  transition: 0.2s ease;
}

.inventory-row:hover {
  background: #f9fbfd;
}
.inventory-container::-webkit-scrollbar {
  width: 10px;
}

.inventory-container::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.inventory-container::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #16a34a, #15803d);
  border-radius: 10px;
}

.inventory-container::-webkit-scrollbar-thumb:hover {
  background: #166534;
}
.inventory-table td:nth-child(7),
.inventory-table td:nth-child(8) {
  text-align: center;
  vertical-align: middle;
}
/* ================= TEXT ================= */
.item-name {
  font-weight: 600;
  color: #0f172a;
}

.supplier {
  color: #475569;
  font-size: 13px;
}

.stock {
  font-weight: 700;
  color: #111827;
}

/* ================= CATEGORY BADGE ================= */
.badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 500;
}

.badge.category {
  background: #e0f2fe;
  color: #0369a1;
}

/* ================= STATUS ================= */
.status {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
}

/* STATUS COLORS */
.status.low {
  background: #fee2e2;
  color: #b91c1c;
}

.status.ok {
  background: #dcfce7;
  color: #166534;
}

.status.out {
    background: #fecdd3;
    color: #991b1b;
}
/* ================= ACTIONS ================= */
.actions {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
}

/* ================= BUTTONS ================= */
.btn {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: none;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: 0.2s ease;
}

/* BUTTON COLORS */
.btn.edit {
  background: #e0f2fe;
  color: #0284c7;
}

.btn.delete {
  background: #fecdd3;
  color: #dc2626;
}


/* HOVER EFFECT */
.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}
.btn:hover i {
  transform: scale(1.2);
  transition: 0.2s;
}
.btn:active {
  transform: scale(0.95);
}

.filter-dropdown {
  display: none;
  position: absolute;
  top: 100%;
  left: 0;
  background: white;
  color: black;
  min-width: 150px;
  max-height: 200px;
  overflow-y: auto;
  border-radius: 10px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  z-index: 200;
  padding: 8px;
}

.filter-dropdown div {
  padding: 8px;
  cursor: pointer;
  border-radius: 6px;
}

.filter-dropdown div:hover {
  background: #16a34a;
  color: white;
}
.add-btn {
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: 0.2s ease;
}

.add-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.15);
}

.add-btn:active {
  transform: scale(0.95);
}

/* ✅ DELETE CONFIRMATION MODAL */
#deleteModal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}
#deleteModal .modal-content {
  background: white;
  width: 350px;
  border-radius: 12px;
  padding: 25px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  animation: pop 0.2s ease;
}
#deleteModal h3 {
  color: #dc2626;
  margin-bottom: 10px;
}
#deleteModal p {
  color: #333;
  margin-bottom: 20px;
}
#deleteModal .modal-buttons {
  display: flex;
  gap: 10px;
  justify-content: center;
}
#deleteModal .btn-cancel {
  background: #e5e7eb;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
}
#deleteModal .btn-confirm {
  background: #dc2626;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
}

/* ================= MODAL ================= */
.modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: white;
  width: 420px;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  animation: pop 0.2s ease;
}
@keyframes pop {
  from { transform: scale(0.8); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.modal-header h2 {
  font-size: 18px;
  color: #114500;
}
.modal.show {
  display: flex;
}
.close {
  font-size: 24px;
  cursor: pointer;
}

.modal-body {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.modal-body input {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 8px;
  outline: none;
}

.modal-footer {
  margin-top: 15px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

.cancel-btn {
  background: #e5e7eb;
  border: none;
  padding: 10px 14px;
  border-radius: 8px;
  cursor: pointer;
}

.save-btn {
  background: linear-gradient(135deg, #16a34a, #15803d);
  color: white;
  border: none;
  padding: 10px 14px;
  border-radius: 8px;
  cursor: pointer;
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
    <h1>Inventory Management</h1>
    <p>Track and manage your stock and items</p>
  </div>

  <div class="header-right">
    <div class="date-box">
      <i class="fa-solid fa-calendar"></i>
      <span id="todayDate">Today: </span>
    </div>
  </div>
</div>

<!-- ADD ITEM BUTTON -->
<div class="inventory-actions">
  <button class="add-btn" onclick="openModal()">
    <i class="fa-solid fa-plus"></i> Add Item
  </button>
</div>
<!-- INVENTORY TABLE -->
<div class="inventory-container">

  <table class="inventory-table">
    <thead>
      <tr>
        <th data-col="0" class="filterable">
          Item Name <i class="fa-solid fa-chevron-down"></i>
          <div class="filter-dropdown"></div>
        </th>

        <th data-col="1" class="filterable">
          Category <i class="fa-solid fa-chevron-down"></i>
          <div class="filter-dropdown"></div>
        </th>

        <th data-col="2" class="filterable">
          Supplier <i class="fa-solid fa-chevron-down"></i>
          <div class="filter-dropdown"></div>
        </th>

        <th data-col="3" class="filterable">
          Unit <i class="fa-solid fa-chevron-down"></i>
          <div class="filter-dropdown"></div>
        </th>

        <th data-col="4" class="filterable">
          Stock <i class="fa-solid fa-chevron-down"></i>
          <div class="filter-dropdown"></div>
        </th>

        <th data-col="4" class="filterable">
          Cost (₱) <i class="fa-solid fa-chevron-down"></i>
          <div class="filter-dropdown"></div>
        </th>

        <th data-col="4" class="filterable">
         Total Value <i class="fa-solid fa-chevron-down"></i>
          <div class="filter-dropdown"></div>
        </th>

        <th data-col="5" class="filterable">
          Reorder Level <i class="fa-solid fa-chevron-down"></i>
          <div class="filter-dropdown"></div>
        </th>

        <th data-col="6" class="filterable">
          Status <i class="fa-solid fa-chevron-down"></i>
          <div class="filter-dropdown"></div>
        </th>

        <th data-col="7">Actions</th>
      </tr>
    </thead>

    <tbody id="inventoryBody">

    </tbody>
  </table>
</div>
</div>
<!-- ================= ADD ITEM MODAL ================= -->
<div id="addModal" class="modal">
  <div class="modal-content">

    <div class="modal-header">
      <h2>Add New Item</h2>
      <span class="close" onclick="closeModal()">&times;</span>
    </div>

    <div class="modal-body">

      <input type="text" id="itemName" placeholder="Item Name">
      <input type="text" id="category" placeholder="Category">
      <input type="text" id="supplier" placeholder="Supplier">
      <input type="text" id="unit" placeholder="Unit (kg, pcs, etc)">
      <input type="number" id="stock" placeholder="Stock">
      <input type="number" id="cost" placeholder="Cost">
      <input type="number" id="reorder" placeholder="Reorder Level">

    </div>

    <div class="modal-footer">
      <button class="cancel-btn" onclick="closeModal()">Cancel</button>
      <button class="save-btn" onclick="addItem()">Save Item</button>
    </div>

  </div>
</div>

<!-- ✅ DELETE CONFIRMATION MODAL HTML -->
<div id="deleteModal">
  <div class="modal-content">
    <h3>⚠️ Confirm Delete</h3>
    <p>Are you sure you want to delete this item?<br><small>This action cannot be undone.</small></p>
    <div class="modal-buttons">
      <button class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
      <button class="btn-confirm" onclick="confirmDelete()">Yes, Delete</button>
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

/******************************** INVENTORY SYSTEM ********************************/

function loadInventory() {
  fetch("../website_php/inventory_api.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "action=fetch"
  })
  .then(res => res.json())
  .then(data => {
    let tbody = document.getElementById("inventoryBody");
    tbody.innerHTML = "";

    data.forEach(item => {

      // ✅ BAGONG KONDISYON: Out of Stock > Low Stock > In Stock
      let status;
      if (item.stock == 0) {
        status = `<span class="status out">Out of Stock</span>`;
      } else if (item.stock <= item.reorder_level) {
        status = `<span class="status low">Low Stock</span>`;
      } else {
    status = `<span class="status ok">In Stock</span>`;
  }

      tbody.innerHTML += `
        <tr data-id="${item.id}" class="inventory-row">
          <td class="item-name">${item.item_name}</td>
          <td><span class="badge category">${item.category}</span></td>
          <td>${item.supplier}</td>
          <td>${item.unit}</td>
          <td class="stock">${item.stock}</td>
          <td class="cost">₱${item.cost}</td>
          <td class="total-value">₱${(item.stock * item.cost).toFixed(2)}</td>
          <td>${item.reorder_level}</td>
          <td>${status}</td>
          <td class="actions">
            <button class="btn edit" onclick="editItem(this)">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button class="btn delete" onclick="deleteItem(this)">
              <i class="fa-solid fa-trash"></i>
            </button>
          </td>
        </tr>
      `;
    });
  });
}

loadInventory();

function updateStatus() {
  let rows = document.querySelectorAll("#inventoryBody tr");

  rows.forEach(row => {
    let stock = parseInt(row.querySelector(".stock").innerText);
    let status = row.querySelector(".status");
    let reorder = parseInt(row.children[7].innerText); // siguraduhing tama ang index ng reorder level

    // ✅ BAGONG KONDISYON
    if (stock === 0) {
      status.innerText = "Out of Stock";
      status.className = "status out";
    } else if (stock <= reorder) {
      status.innerText = "Low Stock";
      status.className = "status low";
    } else {
      status.innerText = "In Stock";
      status.className = "status ok";
    }
  });
}

function editItem(btn) {
  let row = btn.closest("tr");

  // kung naka-edit na, balik save mode
  if (row.classList.contains("editing")) {
    saveRow(row, btn);
    return;
  }

  row.classList.add("editing");

  let cells = {
    name: row.querySelector(".item-name"),
    category: row.children[1],
    supplier: row.children[2],
    unit: row.children[3],
    stock: row.querySelector(".stock"),
    cost: row.querySelector(".cost"),
    reorder: row.children[7]
  };

  cells.name.innerHTML = `<input value="${cells.name.innerText}">`;
  cells.category.innerHTML = `<input value="${cells.category.innerText}">`;
  cells.supplier.innerHTML = `<input value="${cells.supplier.innerText}">`;
  cells.unit.innerHTML = `<input value="${cells.unit.innerText}">`;
  cells.stock.innerHTML = `<input type="number" value="${cells.stock.innerText}">`;
  cells.cost.innerHTML = `<input type="number" value="${cells.cost.innerText.replace('₱','')}">`;
  cells.reorder.innerHTML = `<input type="number" value="${cells.reorder.innerText}">`;

  btn.innerHTML = `<i class="fa-solid fa-check"></i>`;
}

function saveRow(row, btn) {
  let name = row.querySelector(".item-name input").value;
  let category = row.children[1].querySelector("input").value;
  let supplier = row.children[2].querySelector("input").value;
  let unit = row.children[3].querySelector("input").value;
  let stock = parseFloat(row.querySelector(".stock input").value);
  let cost = parseFloat(row.querySelector(".cost input").value);
  let reorder = parseFloat(row.children[7].querySelector("input").value);

  // update cells back to text
  row.querySelector(".item-name").innerText = name;
  row.children[1].innerText = category;
  row.children[2].innerText = supplier;
  row.children[3].innerText = unit;
  row.querySelector(".stock").innerText = stock;
  row.querySelector(".cost").innerText = "₱" + cost.toFixed(2);
  row.children[7].innerText = reorder;

  // TOTAL VALUE
  let total = stock * cost;
  let totalCell = row.querySelector(".total-value");
  if (totalCell) {
    totalCell.innerText = "₱" + total.toFixed(2);
  }

  // STATUS UPDATE
let status = row.querySelector(".status");

if (stock === 0) {
  status.innerText = "Out of Stock";
  status.className = "status out";
} else if (stock <= reorder) {
  status.innerText = "Low Stock";
  status.className = "status low";
} else {
  status.innerText = "In Stock";
  status.className = "status ok";
}

  // reset edit state
  row.classList.remove("editing");
  btn.innerHTML = `<i class="fa-solid fa-pen"></i>`;

  let id = row.getAttribute("data-id");

fetch("../website_php/inventory_api.php", {
  method: "POST",
  headers: {"Content-Type": "application/x-www-form-urlencoded"},
  body: new URLSearchParams({
    action: "update",
    id,
    item_name: name,
    category,
    supplier,
    unit,
    stock,
    cost,
    reorder_level: reorder
  })
});

}

updateStatus();

// ================= SORTING =================
let sortDirection = {}; // track per column

document.querySelectorAll(".inventory-table thead th").forEach((header) => {
  header.addEventListener("click", () => {
    let colIndex = header.getAttribute("data-col");

    if (colIndex === null) return;

    let tbody = document.getElementById("inventoryBody");
    let rows = Array.from(tbody.querySelectorAll("tr"));

    sortDirection[colIndex] = !sortDirection[colIndex];

    rows.sort((a, b) => {
      let A = a.children[colIndex].innerText.trim();
      let B = b.children[colIndex].innerText.trim();

      let numA = parseFloat(A);
      let numB = parseFloat(B);

      if (!isNaN(numA) && !isNaN(numB)) {
        return sortDirection[colIndex] ? numA - numB : numB - numA;
      }

      return sortDirection[colIndex]
        ? A.localeCompare(B)
        : B.localeCompare(A);
    });

    rows.forEach(row => tbody.appendChild(row));

    updateArrows(header, sortDirection[colIndex]);
  });
});

document.querySelectorAll(".filterable").forEach((header) => {
  let colIndex = header.getAttribute("data-col");
  let dropdown = header.querySelector(".filter-dropdown");

  let values = new Set();

  document.querySelectorAll("#inventoryBody tr").forEach(row => {
    values.add(row.children[colIndex].innerText.trim());
  });

  let allOption = document.createElement("div");
  allOption.innerText = "All";
  allOption.onclick = () => filterTable(colIndex, "All");
  dropdown.appendChild(allOption);

  values.forEach(val => {
    let option = document.createElement("div");
    option.innerText = val;
    option.onclick = () => filterTable(colIndex, val);
    dropdown.appendChild(option);
  });

  header.addEventListener("click", (e) => {
    e.stopPropagation();

    document.querySelectorAll(".filter-dropdown")
      .forEach(d => d.style.display = "none");

    dropdown.style.display =
      dropdown.style.display === "block" ? "none" : "block";
  });
});

function filterTable(colIndex, value) {
  let rows = document.querySelectorAll("#inventoryBody tr");

  rows.forEach(row => {
    let cell = row.children[colIndex].innerText.trim();

    row.style.display =
      (value === "All" || cell === value) ? "" : "none";
  });
}

window.addEventListener("click", () => {
  document.querySelectorAll(".filter-dropdown")
    .forEach(d => d.style.display = "none");
});

function updateArrows(activeHeader, isAsc) {
  document.querySelectorAll(".inventory-table thead th i").forEach(icon => {
    icon.classList.remove("fa-chevron-up");
    icon.classList.add("fa-chevron-down");
  });

  let icon = activeHeader.querySelector("i");
  if (icon) {
    icon.classList.remove("fa-chevron-down");
    icon.classList.add(isAsc ? "fa-chevron-up" : "fa-chevron-down");
  }
}

function addItem() {
  let data = new URLSearchParams({
    action: "add",
    item_name: document.getElementById("itemName").value,
    category: document.getElementById("category").value,
    supplier: document.getElementById("supplier").value,
    unit: document.getElementById("unit").value,
    stock: document.getElementById("stock").value,
    cost: document.getElementById("cost")?.value || 0,
    reorder_level: document.getElementById("reorder").value
  });

  if (!data.get("item_name") || !data.get("stock") || !data.get("reorder_level")) {
    alert("Please complete required fields!");
    return;
  }

  fetch("../website_php/inventory_api.php", {
  method: "POST",
  headers: { "Content-Type": "application/x-www-form-urlencoded" },
  body: data
})
  .then(res => res.json())
  .then(() => {
    closeModal();
    loadInventory();
  });
}

function openModal() {
  document.getElementById("addModal").classList.add("show");
}

function closeModal() {
  document.getElementById("addModal").classList.remove("show");
}

function updateTotalValue(row) {
  let stock = parseFloat(row.querySelector(".stock").innerText);
  let costText = row.querySelector(".cost").innerText.replace("₱", "");
  let cost = parseFloat(costText);
  let total = stock * cost;

  let totalCell = row.querySelector(".total-value");
  if (totalCell) {
    totalCell.innerText = "₱" + total.toFixed(2);
  }
}

function updateAllTotalValues() {
  document.querySelectorAll("#inventoryBody tr").forEach(row => {
    updateTotalValue(row);
  });
}

updateAllTotalValues();

// ✅ UPDATED DELETE FUNCTION WITH MODAL
let rowToDelete = null; // para matandaan kung aling row ang buburahin

function deleteItem(btn) {
  rowToDelete = btn.closest("tr"); // kunin ang row na pipindutin
  document.getElementById("deleteModal").style.display = "flex"; // ipakita ang modal
}

function closeDeleteModal() {
  document.getElementById("deleteModal").style.display = "none"; // itago ang modal
  rowToDelete = null; // i-clear
}

function confirmDelete() {
  if (!rowToDelete) return;

  let id = rowToDelete.getAttribute("data-id");

  fetch("../website_php/inventory_api.php", {
    method: "POST",
    headers: {"Content-Type": "application/x-www-form-urlencoded"},
    body: "action=delete&id=" + id
  })
  .then(() => {
    rowToDelete.remove();
    closeDeleteModal();
  });
}
</script>
