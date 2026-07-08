<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../website_php/database.php';
require_once '../website_php/auth_check.php';

if (!isAdmin()) {
    header("Location: ../login.html?error=unauthorized_access");
    exit;
}

$admin_count_stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Admin' AND is_banned = 0 AND is_approved = 1");
$active_admin_count = $admin_count_stmt->fetchColumn();

$user_stmt = $pdo->prepare("SELECT full_name, role FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$current_user_details = $user_stmt->fetch(PDO::FETCH_ASSOC);
$greeting_role = ($current_user_details['role'] === 'Admin') ? 'Admin' : 'Staff';

$bookings_stmt = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC");
$bookings = $bookings_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Request</title>
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
.booking-header {
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
.booking-header .header-left h1 {
  font-size: 20px;
  margin: 0;
  color: #114500;
  line-height: 1.2;
}
.booking-header .header-left p {
  font-size: 13px;
  margin-top: 6px; 
  color: #6b7280;
  line-height: 1.4;
}
.booking-header .date-box {
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

/******************************** BOOKING TABLE  ********************************/
.booking-table {
  width: 100%;
  border-collapse: collapse; /* IMPORTANT */
}
.booking-table thead th {
  background: linear-gradient(135deg, #1e8e3e, #0f6b2d); /* smooth gradient */
  color: #ffffff;
  font-size: 13px;
  font-weight: 600;
  padding: 16px;
  text-align: left;
  border: none; /* REMOVE divider */
}
.booking-table thead th:last-child {
  border-right: none;
}
.booking-table thead th i {
  margin-right: 8px;
  font-size: 12px;
  color: rgba(255,255,255,0.9);
  opacity: 0.9;
  transition: 0.2s ease;
}
.booking-table thead th {
  position: relative;
}
.booking-table tbody tr {
  background: #ffffff;
  border-bottom: 1px solid #e5e7eb;
}
.booking-table td {
  padding: 14px 16px;
  font-size: 13px;
  color: #374151;
  vertical-align: middle;
}
.booking-table tbody tr td:first-child {
  border-radius: 16px 0 0 16px;
}
.booking-table tbody tr td:last-child {
  border-radius: 0 16px 16px 0;
}

/******************************** USER / CUSTOMER NAME BLOCK  ********************************/
.user {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.user strong {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}
.user .email {
  font-size: 12px;
  color: #6b7280;
  letter-spacing: 0.2px;
}
.user strong {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}

/******************************** MUTED ********************************/
.muted {
  font-size: 12px;
  color: #9ca3af;
}

/******************************** BADGE ********************************/
.badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.badge.paid {
  background: linear-gradient(135deg, #dcfce7, #bbf7d0);
  color: #166534;
  box-shadow: 0 0 0 4px rgba(34,197,94,0.08);
}
.badge.pending {
  background: linear-gradient(135deg, #fef9c3, #fde68a);
  color: #854d0e;
  box-shadow: 0 0 0 4px rgba(250,204,21,0.10);
}
.badge.declined {
  background: linear-gradient(135deg, #fee2e2, #fecaca);
  color: #991b1b;
  box-shadow: 0 0 0 4px rgba(239,68,68,0.10);
}

/******************************** ACTIONS ********************************/
.actions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.actions button {
  padding: 7px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  border: none;
  cursor: pointer;
  transition: 0.2s ease;
}
.actions .accept {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: white;
  box-shadow: 0 6px 15px rgba(34,197,94,0.25);
}
.actions .accept:hover {
  transform: scale(1.06);
  box-shadow: 0 10px 22px rgba(34,197,94,0.35);
}
.actions .decline {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
  box-shadow: 0 6px 15px rgba(239,68,68,0.25);
}
.actions .decline:hover {
  transform: scale(1.06);
  box-shadow: 0 10px 22px rgba(239,68,68,0.35);
}
.actions .view {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  box-shadow: 0 6px 15px rgba(59,130,246,0.25);
}

.actions .view:hover {
  transform: scale(1.06);
  box-shadow: 0 10px 22px rgba(59,130,246,0.35);
}

/******************************** NO BOOKING ********************************/
.no-booking {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 200px; 
  color: #555;
  font-size: 16px;
  font-weight: 600;
  gap: 10px;
  border: 2px dashed #ccc;
  border-radius: 10px;
  background: #f9f9f9;
  margin-top: 20px;
}
.no-booking img {
  filter: grayscale(50%);
}

/******************************** INSIDE THE CARD ********************************/
.info-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px 15px;
  margin-top: 10px;
}
.full-width {
  grid-column: span 2;
}
.icon-box {
  width: 36px;
  height: 36px;
  min-width: 28px;
  background: #f5f5f5;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.icon-box img {
  width: 22px;
  height: 22px;
  object-fit: contain;
}

/******************************** MODAL ********************************/
.modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.4);
  backdrop-filter: blur(4px);
}
.modal-content {
  background: #ffffff;
  width: 460px;
  margin: 7% auto;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 25px 60px rgba(0,0,0,0.25);
}

@keyframes pop {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.modal-header {
  background: #ffffff;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;

  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-header h2 {
  font-size: 20px;
  font-weight: 700;
  color: #14532d; /* dark green */
}
.modal-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 20px;
  max-height: 70vh;
  overflow-y: auto;
  background: #f3f4f6; /* light gray bg like screenshot */
}
.modal-body .full {
  grid-column: span 2;
}
.modal-body::-webkit-scrollbar {
  width: 6px;
}
.modal-body::-webkit-scrollbar-thumb {
  background: #16a34a;
  border-radius: 10px;
}
.modal-body::-webkit-scrollbar-track {
  background: #f3f4f6;
}
.close {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: rgba(255,255,255,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  cursor: pointer;
  transition: 0.2s ease;
}

/******************************** DETAILS ********************************/
.detail {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  padding: 10px 12px;
  border-radius: 10px;
}
.detail label {
  display: block;
  font-size: 11px;
  color: #6b7280;
  margin-bottom: 3px;
}
.detail span {
  font-weight: 600;
  color: #111827;
}
.details-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.detail-card {
  background: #e5e7eb; /* softer gray */
  border-radius: 14px;
  padding: 14px 16px;
  border: none;
}
.detail-card label {
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 4px;
  display: block;
}
.detail-card span {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
}
/******************************** STATUS ********************************/
.status-badge {
  display: inline-block;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}
.status-pending {
  background: #fde68a;
  color: #92400e;
}
.status-paid {
  background: #dcfce7;
  color: #166534;
}
.status-declined {
  background: #fee2e2;
  color: #991b1b;
}
.status-center {
  display: flex;
  justify-content: center;
  margin-top: 10px;
}
.section {
  background: #ffffff;
  border-radius: 16px;
  padding: 18px;
  border: 1px solid #e5e7eb;
}
.section-title {
  font-size: 18px;
  font-weight: 700;
  color: #14532d;
  margin-bottom: 12px;
}
.tag {
  display: inline-block;
  padding: 8px 14px;
  border-radius: 999px;
  background: #d1fae5;
  color: #166534;
  font-size: 13px;
  font-weight: 600;
}
.payment-box {
  background: #e5e7eb;
  padding: 14px;
  border-radius: 12px;
  text-align: center;
}
.payment-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}
.payment-box label {
  font-size: 12px;
  color: #6b7280;
}
.payment-box span {
  display: block;
  margin-top: 6px;
  font-weight: 700;
  font-size: 16px;
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
<div class="booking-header">
  <div class="header-left">
    <h1>Booking Requests</h1>
    <p>Manage and review event booking requests</p>
  </div>

  <div class="header-right">
    <div class="date-box">
      <i class="fa-solid fa-calendar"></i>
      <span id="todayDate">Today: </span>
    </div>
  </div>
</div>

<!--------------------------------------- BOOKING CONTAINER ---------------------------------------------> 
 <div class="booking-table-container">

  <table class="booking-table">

  <thead>
    <tr>
      <th><i class="fa-solid fa-user"></i> Customer</th>
      <th><i class="fa-solid fa-calendar-check"></i> Event</th>
      <th><i class="fa-regular fa-clock"></i> Schedule</th>
      <th><i class="fa-solid fa-phone"></i> Contact</th>
      <th><i class="fa-solid fa-location-dot"></i> Location</th>
      <th><i class="fa-solid fa-credit-card"></i> Payment</th>
      <th><i class="fa-solid fa-circle-info"></i> Status</th>
      <th><i class="fa-solid fa-gear"></i> Actions</th>
    </tr>
  </thead>

    <tbody>
  <?php if (empty($bookings)): ?>
    <tr>
      <td colspan="8" style="text-align:center; padding:30px; color:#666;">No booking requests found.</td>
    </tr>
  <?php else: ?>
    <?php foreach ($bookings as $booking): ?>
    <tr data-id="<?= $booking['id'] ?>">
      <td>
        <div>
          <strong><?= htmlspecialchars($booking['full_name']) ?></strong>
          <div class="muted email"><?= htmlspecialchars($booking['email']) ?></div>
        </div>
      </td>

      <td><?= strtolower(str_replace(' ', '_', $booking['service_type'])) ?></td>

      <td>
        <?= date("M d, Y", strtotime($booking['event_date'])) ?> • 
        <?= htmlspecialchars($booking['event_time']) ?>
      </td>

      <td><?= htmlspecialchars($booking['contact_number']) ?></td>

      <td><?= htmlspecialchars($booking['city']) ?>, <?= htmlspecialchars($booking['province']) ?></td>

      <td><?= strtolower(htmlspecialchars($booking['payment_method'])) ?></td>

      <td>
        <?php 
          $current_status = $booking['booking_status'] ?? $booking['status'] ?? 'Pending';
          $badge_color = match($current_status) {
            'Pending' => 'background:#fff3cd; color:#856404;',
            'Accepted' => 'background:#d4edda; color:#155724;',
            'Declined' => 'background:#f8d7da; color:#721c24;',
            default => 'background:#e2e3e5; color:#383d41;'
          };
        ?>
        <select class="status-select" style="<?= $badge_color ?> border:none; border-radius:6px; padding:6px 10px; font-size:13px; font-weight:500; cursor:pointer;" onchange="openConfirmModal(<?= $booking['id'] ?>, this.value, '<?= htmlspecialchars($booking['full_name']) ?>')">
          <!-- ✅ Value ay Accepted, Text ay Approved -->
          <option value="Pending" <?= $current_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
          <option value="Accepted" <?= $current_status == 'Accepted' ? 'selected' : '' ?>>Approved</option>
          <option value="Declined" <?= $current_status == 'Declined' ? 'selected' : '' ?>>Declined</option>
        </select>
      </td>

      <td class="actions">
        <button class="view" style="background:#22c55e; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer;" onclick='viewDetails(<?= json_encode($booking) ?>)'>View</button>
      </td>
    </tr>
    <?php endforeach; ?>
  <?php endif; ?>
</tbody>

  </table>
<div id="bookingModal" class="modal">
  <div class="modal-content">

    <div class="modal-header">
      <h2>Booking Details</h2>
      <span class="close" onclick="closeModal()">&times;</span>
    </div>

    <div class="modal-body" id="modalBody">
    </div>

  </div>
</div>
</div>

<!-- ✅ CONFIRM MODAL PARA SA ACCEPT (ALL CHECKBOXES REQUIRED) -->
<div id="acceptConfirmModal" class="modal">
  <div class="modal-content" style="max-width:420px; border-radius:12px; overflow:hidden;">
    <div class="modal-header" style="background:#22c55e; color:white; padding:15px 20px; display:flex; justify-content:space-between; align-items:center;">
      <h2 style="margin:0; font-size:18px;">Confirm Status Change</h2>
      <span class="close" onclick="closeAllModals()" style="font-size:24px; cursor:pointer; line-height:1;">&times;</span>
    </div>
    <div class="modal-body" style="padding:20px;">
      <p style="margin-bottom:15px; font-size:15px;">Are you sure you want to <strong>APPROVE</strong> the booking of <span id="acceptClientName"></span>?</p>
      <p style="font-weight:500; margin-bottom:12px; font-size:14px;">Before approving, please ensure:</p>
      <div style="font-size:14px; line-height:1.8;">
        <label style="display:flex; gap:8px; align-items:center; margin-bottom:8px; cursor:pointer;">
          <input type="checkbox" id="chkPayment"> Payment / Proof of payment is verified
        </label>
        <label style="display:flex; gap:8px; align-items:center; margin-bottom:8px; cursor:pointer;">
          <input type="checkbox" id="chkSchedule"> Date and Time are available
        </label>
        <label style="display:flex; gap:8px; align-items:center; margin-bottom:8px; cursor:pointer;">
          <input type="checkbox" id="chkLocation"> Location is serviceable
        </label>
        <label style="display:flex; gap:8px; align-items:center; margin-bottom:8px; cursor:pointer;">
          <input type="checkbox" id="chkRequirements"> Requirements are complete
        </label>
      </div>
      <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:25px;">
        <button style="padding:8px 18px; border:1px solid #ddd; border-radius:6px; cursor:pointer; background:#f5f5f5;" onclick="closeAllModals()">Cancel</button>
        <button id="confirmAcceptBtn" style="background:#22c55e; color:white; border:none; padding:8px 18px; border-radius:6px; cursor:not-allowed; opacity:0.6;" disabled>Confirm</button>
      </div>
    </div>
  </div>
</div>

<!-- ✅ CONFIRM MODAL PARA SA DECLINE -->
<div id="declineConfirmModal" class="modal">
  <div class="modal-content" style="max-width:480px;">
    <div class="modal-header" style="background:#ef4444; color:white;">
      <h2>Confirm Status Change</h2>
      <span class="close" onclick="closeAllModals()">&times;</span>
    </div>
    <div class="modal-body" style="padding:20px;">
      <p style="margin-bottom:15px;">Are you sure you want to <strong>DECLINED</strong> the booking of <span id="declineClientName"></span>?</p>
      <p style="font-weight:500; margin-bottom:10px;">Please select reason for declining:</p>
      <div style="font-size:13px; line-height:1.8; margin-bottom:15px;">
        <label style="display:flex; gap:8px; align-items:center; margin-bottom:5px;"><input type="radio" name="decline_reason" value="Schedule Conflict"> Schedule Conflict</label>
        <label style="display:flex; gap:8px; align-items:center; margin-bottom:5px;"><input type="radio" name="decline_reason" value="Location not serviceable"> Location not serviceable</label>
        <label style="display:flex; gap:8px; align-items:center; margin-bottom:5px;"><input type="radio" name="decline_reason" value="Incomplete Requirements"> Incomplete Requirements</label>
        <label style="display:flex; gap:8px; align-items:center; margin-bottom:10px;"><input type="radio" name="decline_reason" value="Others"> Others: <input type="text" id="other_reason" placeholder="Type here..." style="flex:1; padding:4px; border-radius:4px; border:1px solid #ccc;"></label>
      </div>
      <div style="display:flex; gap:10px; justify-content:flex-end;">
        <button style="padding:8px 16px; border:none; border-radius:6px; cursor:pointer;" onclick="closeAllModals()">Cancel</button>
        <button id="confirmDeclineBtn" style="background:#ef4444; color:white; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">Confirm</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>
<script>
/******************************** MENU & SIDEBAR ********************************/
const sidebarButtons = document.querySelectorAll('.sidebar .menu button');
function getCurrentPage() { return window.location.pathname.split("/").pop(); }
function highlightSidebar() {
  const currentPage = getCurrentPage().toLowerCase();
  sidebarButtons.forEach(btn => {
    btn.classList.remove('active');
    if (btn.dataset.page.toLowerCase() === currentPage) btn.classList.add('active');
  });
}
highlightSidebar();
sidebarButtons.forEach(btn => {
  btn.addEventListener('click', () => {
    sidebarButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    window.location.href = btn.dataset.page;
  });
});
window.addEventListener('popstate', highlightSidebar);

/******************************** ADMIN DROPDOWN ********************************/
function toggleDropdown() {
  const dropdown = document.getElementById("adminDropdown");
  dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}
function logout() { window.location.href = "login.html"; }
window.addEventListener('click', e => {
  if (!e.target.closest('.admin')) document.getElementById("adminDropdown").style.display = "none";
});

/******************************** SIDEBAR TOGGLE ********************************/
document.getElementById("menu-btn").addEventListener('click', () => {
  document.querySelector(".sidebar").classList.toggle("hide");
  document.querySelector(".main").classList.toggle("full");
});

/******************************** DATE ********************************/
function updateTodayDate() {
  const today = new Date();
  const formatted = today.toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
  document.getElementById("todayDate").textContent = "Today: " + formatted;
}
updateTodayDate();

// ==================================================
// ✅ BAGONG FUNCTIONS — WALANG DOBLE, SIGURADONG GUMAGANA
// ==================================================
let pendingBookingId = null;
let pendingNewStatus = null;

// ✅ Buksan ang confirmation modal — EKSATONG GAGANA
function openConfirmModal(bookingId, newStatus, clientName) {
  pendingBookingId = bookingId;
  pendingNewStatus = newStatus;

  // Kung Pending, diretso update
  if (newStatus === 'Pending') {
    updateDatabaseStatus(bookingId, newStatus);
    return;
  }

  if (newStatus === 'Accepted') {
    document.getElementById('acceptClientName').textContent = clientName;
    document.querySelectorAll('#acceptConfirmModal input[type=checkbox]').forEach(cb => cb.checked = false);
    document.getElementById('confirmAcceptBtn').disabled = true;
    document.getElementById('acceptConfirmModal').style.display = 'block';
  } else if (newStatus === 'Declined') {
    document.getElementById('declineClientName').textContent = clientName;
    document.querySelectorAll('#declineConfirmModal input[type=radio]').forEach(rb => rb.checked = false);
    document.getElementById('other_reason').value = '';
    document.getElementById('declineConfirmModal').style.display = 'block';
  }
}

// ✅ Enable confirm button kapag lahat na-check
// ✅ VALIDATION: LAHAT NG CHECKBOX AY KAILANGAN NAKA-CHECK
document.addEventListener('change', function(e){
  if (e.target.closest('#acceptConfirmModal')) {
    // Kunin ang lahat ng checkbox
    const chk1 = document.getElementById('chkPayment').checked;
    const chk2 = document.getElementById('chkSchedule').checked;
    const chk3 = document.getElementById('chkLocation').checked;
    const chk4 = document.getElementById('chkRequirements').checked;
    const confirmBtn = document.getElementById('confirmAcceptBtn');

    // Kapag LAHAT AY NAKA-CHECK — pwede nang pindutin
    if (chk1 && chk2 && chk3 && chk4) {
      confirmBtn.disabled = false;
      confirmBtn.style.opacity = '1';
      confirmBtn.style.cursor = 'pointer';
    } else {
      // Kapag may kulang — naka-disable pa rin
      confirmBtn.disabled = true;
      confirmBtn.style.opacity = '0.6';
      confirmBtn.style.cursor = 'not-allowed';
    }
  }
});

// ✅ Confirm buttons
document.getElementById('confirmAcceptBtn').addEventListener('click', () => {
  updateDatabaseStatus(pendingBookingId, 'Accepted');
});
document.getElementById('confirmDeclineBtn').addEventListener('click', () => {
  let reason = '';
  const sel = document.querySelector('input[name="decline_reason"]:checked');
  if (sel) reason = sel.value === 'Others' ? 'Others: ' + document.getElementById('other_reason').value : sel.value;
  updateDatabaseStatus(pendingBookingId, 'Declined', reason);
});

// ✅ I-update sa database at i-refresh ang page
async function updateDatabaseStatus(bookingId, newStatus, reason = '') {
  try {
    const formData = new FormData();
    formData.append('id', bookingId);
    formData.append('status', newStatus);
    formData.append('reason', reason);

    const res = await fetch('webapp_php/update_booking_status.php', {
      method: 'POST',
      body: formData // ✅ FormData na ang gamit, hindi string
    });

    const data = await res.json();
    if (data.status === 'success') {
      alert(`✅ Booking status changed to **${newStatus}** successfully!`);
      closeAllModals();
      location.reload();
    } else {
      alert("❌ Error: " + data.message);
      location.reload();
    }
  } catch (err) {
    alert("❌ Connection error! Please try again.");
    console.error(err);
    location.reload();
  }
}

// ✅ Isara lahat ng modal
function closeAllModals() {
  document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
}
window.addEventListener("click", e => {
  if (e.target.classList.contains('modal')) closeAllModals();
});

// ✅ View Details — pareho pa rin ang design
function viewDetails(booking) {
  const html = `
  <div class="details-grid">
    <div class="detail-card"><label>Client</label><span>${booking.full_name}</span></div>
    <div class="detail-card"><label>Email</label><span>${booking.email}</span></div>
    <div class="detail-card"><label>Contact</label><span>${booking.contact_number}</span></div>
    <div class="detail-card"><label>Date</label><span>${new Date(booking.event_date).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' })} • ${booking.event_time}</span></div>
    <div class="detail-card"><label>Guests</label><span>${booking.guest_count}</span></div>
    <div class="detail-card"><label>Location</label><span>${booking.city}, ${booking.province}</span></div>
  </div>
  <div class="section"><div class="section-title">Service</div><span class="tag">${booking.service_type}</span></div>
  <div class="section">
    <div class="section-title">Payment Summary</div>
    <div class="payment-grid">
      <div class="payment-box"><label>Total Amount</label><span>₱${parseFloat(booking.total_amount).toLocaleString()}</span></div>
      <div class="payment-box"><label>Payment Type</label><span>${booking.payment_type}</span></div>
      <div class="payment-box"><label>Method</label><span>${booking.payment_method}</span></div>
    </div>
    <div class="status-center">
      <span class="status-badge">${booking.payment_type === 'Full Payment' ? 'Fully Paid' : booking.payment_type === 'Reservation Fee Only' ? 'Partial' : 'Pending'}</span>
    </div>
  </div>
  <div class="section"><div class="section-title">Notes</div><span>${booking.additional_notes || 'No additional notes provided'}</span></div>
  `;
  document.getElementById("modalBody").innerHTML = html;
  document.getElementById("bookingModal").style.display = "block";
}
function closeModal() { document.getElementById("bookingModal").style.display = "none"; }
</script>
