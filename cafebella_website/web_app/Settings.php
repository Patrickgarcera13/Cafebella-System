<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../website_php/auth_check.php';

require_admin();

$admin_count_stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'Admin' AND is_banned = 0 AND is_approved = 1");
$active_admin_count = $admin_count_stmt->fetchColumn();

if (!isAdmin()) {
    header("Location: ../login.html?error=unauthorized_access");
    exit();
}

// Get current user's full name and role for greeting
$user_stmt = $pdo->prepare("SELECT full_name, role FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$current_user_details = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Set greeting text based on role
$greeting_role = ($current_user_details['role'] === 'Admin') ? 'Admin' : 'Staff';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Settings</title>

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
.settings-header {
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
.settings-header .header-left h1 {
  font-size: 20px;
  margin: 0;
  color: #114500;
  line-height: 1.2;
}
.settings-header .header-left p {
  font-size: 13px;
  margin-top: 6px; 
  color: #6b7280;
  line-height: 1.4;
}
.settings-header .date-box {
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

/******************************** SETTINGS LAYOUT ********************************/
.settings-container {
  display: flex;
  gap: 25px;
  padding: 0 25px 25px;
}
.settings-sidebar {
  width: 240px;
  background: #ffffff;
  border-radius: 16px;
  padding: 12px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}
.settings-sidebar button {
  padding: 12px 14px;
  border: none;
  background: transparent;
  text-align: left;
  border-radius: 10px;
  cursor: pointer;
  font-size: 14px;
  color: #444;
  font-weight: 500;
}
.settings-sidebar button:hover {
  background: #f1f5f9;
}
.settings-sidebar button.active {
  background: linear-gradient(135deg, #66bb6a, #43a047);
  color: white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
.settings-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
/* CARD */
.settings-card {
  background: #ffffff;
  border-radius: 16px;
  padding: 25px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.05);
  border: 1px solid #f0f0f0;
}

/* TITLE */
.settings-card h3 {
  font-size: 18px;
  margin-bottom: 5px;
  color: #114500;
}

.settings-card p {
  font-size: 13px;
  color: #777;
  margin-bottom: 20px;
}


/* INPUTS */
.settings-content input,
.settings-content select {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

.settings-content button {
  background: #2e7d32;
  color: white;
  border: none;
  padding: 10px 15px;
  border-radius: 6px;
  cursor: pointer;
}

.settings-content button:hover {
  background: #1b5e20;
}
/* TABS */
.tab {
  display: none;
}

.tab.active {
  display: block;
}
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}
.input-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.input-group label {
  font-size: 12px;
  color: #555;
  font-weight: 500;
}

.input-group input {
  padding: 11px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fafafa;
}

.input-group input:focus {
  border-color: #2e7d32;
  background: white;
  outline: none;
}

/* FILE INPUT */
.file-box {
  border: 2px dashed #ccc;
  padding: 15px;
  text-align: center;
  border-radius: 10px;
  font-size: 13px;
  color: #777;
  cursor: pointer;
}

.file-box:hover {
  border-color: #2e7d32;
}

/* SAVE BUTTON */
.save-btn {
  margin-top: 20px;
  padding: 12px 18px;
  border: none;
  border-radius: 10px;
  background: linear-gradient(135deg, #2e7d32, #1b5e20);
  color: white;
  font-weight: 600;
  cursor: pointer;
  width: fit-content;
}

.save-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

/******************************** USER MANAGEMENT ********************************/

/* ROLE SELECT */
.role-select {
  padding: 11px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fafafa;
}

/* USER LIST */
/* USER LIST */
.user-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 15px;
  /* NEW: Para maging scrollable */
  max-height: 400px; /* Adjust this value as needed based on your layout */
  overflow-y: auto; /* Adds a vertical scrollbar when content exceeds max-height */
  padding-right: 10px; /* Add some padding so scrollbar doesn't touch content */
}

/* USER ITEM */
.user-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9fafb;
  padding: 12px 15px;
  border-radius: 10px;
  border: 1px solid #eee;
  flex-wrap: wrap; /* Allows items to wrap on smaller screens */
  margin-bottom: 10px; /* Add some spacing between user items */
}

/* LEFT SIDE */
.user-info {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-grow: 1; /* Allow info to take up available space */
  /* Remove margin-bottom if you want it tighter, or adjust as needed */
}

/* AVATAR */
.avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: var(--primary); /* Using CSS variable for consistency */
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  flex-shrink: 0; /* Prevents the avatar from shrinking when space is tight */
  font-size: 16px; /* Adjust font size if needed */
}
/* ROLE BADGES */
.role {
  display: inline-block;
  font-size: 11px;
  padding: 3px 8px;
  border-radius: 6px;
  margin-right: 5px; /* Space between role and status badge */
  margin-bottom: 3px; /* Ensure spacing if wrapping */
}

.user-actions {
  display: flex;
  gap: 8px; /* Space between buttons/select */
  flex-wrap: wrap; /* Allows buttons/select to wrap on smaller screens */
  justify-content: flex-end; /* Align to the right */
  margin-top: 10px; /* Add some space on top when wrapping to new line */
  align-items: center; /* Vertically align items */
}

@media (min-width: 768px) { /* Adjust for larger screens to remove top margin */
  .user-actions {
    margin-top: 0;
  }
}

/* NEW: Styling for the buttons within user-actions */
.user-actions .btn {
  padding: 6px 10px; /* Smaller padding for buttons */
  font-size: 12px; /* Smaller font for buttons */
  border-radius: 6px; /* Match other rounded elements */
  /* If you are using Bootstrap, these classes (btn, btn-sm, btn-success, etc.)
     should already be styled. If not, define them here.
     Example if not using Bootstrap:
     background-color: var(--primary);
     color: white;
     border: none;
     cursor: pointer;
  */
}

/* NEW: Styling for the role change select dropdown */
.user-actions .change-role-select {
  padding: 6px 10px; /* Match button padding */
  font-size: 12px; /* Match button font size */
  border-radius: 6px; /* Match button border-radius */
  border: 1px solid #ddd;
  background: #fafafa;
  min-width: 80px; /* Ensure dropdown is readable */
  height: calc(1.5em + .75rem + 2px); /* Tries to match height with buttons */
}

/* NEW: Styling for the status badges */
.badge {
  display: inline-block;
  padding: .35em .65em;
  font-size: .75em; /* Smaller font size for badges */
  font-weight: 700;
  line-height: 1;
  color: #fff;
  text-align: center;
  white-space: nowrap;
  vertical-align: middle; /* Align middle with text */
  border-radius: .25rem;
  margin-left: 5px; /* Space from other text/badges */
}
.badge.bg-danger { background-color: #dc3545!important; } /* Red for Banned */
.badge.bg-warning { background-color: #ffc107!important; color: #212529!important; } /* Yellow for Pending, added color for text */
.badge.bg-success { background-color: #28a745!important; } /* Green for Approved */
.badge.bg-info { background-color: #17a2b8!important; } /* Blue for Unban/Info */

/* Adjusting the default .delete-btn to fit the new user-actions buttons */
.delete-btn {
  background: #dc3545; /* Changed to red, similar to Bootstrap's btn-danger */
  color: white;
  border: none;
  padding: 6px 10px; /* Match new button sizes */
  border-radius: 6px;
  cursor: pointer;
}

.delete-btn:hover {
  background: #b91c1c;
}

.role.admin {
  background: #e3f2fd;
  color: #1976d2;
}

.role.staff {
  background: #f3e5f5;
  color: #7b1fa2;
}

/* DELETE BUTTON */
.delete-btn {
  background: #fee2e2;
  color: #b91c1c;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  cursor: pointer;
}

.delete-btn:hover {
  background: #fecaca;
}

/******************************** PAYMENT UI ********************************/

.payment-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 15px;
}

/* CARD */
.payment-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9fafb;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 15px;
  transition: 0.2s;
}

.payment-card.active {
  border-color: #2e7d32;
  background: #f1f8f4;
}

.payment-card:hover {
  transform: translateY(-2px);
}

/* LEFT */
.payment-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.payment-left i {
  font-size: 18px;
  color: #2e7d32;
}

.payment-left p {
  font-size: 12px;
  color: #777;
  margin-top: 3px;
}

/* TOGGLE SWITCH */
.switch {
  position: relative;
  display: inline-block;
  width: 42px;
  height: 22px;
}

.switch input {
  display: none;
}

.switch span {
  position: absolute;
  cursor: pointer;
  background: #ccc;
  border-radius: 20px;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  transition: 0.3s;
}

.switch span:before {
  content: "";
  position: absolute;
  height: 16px;
  width: 16px;
  left: 3px;
  top: 3px;
  background: white;
  border-radius: 50%;
  transition: 0.3s;
}

.switch input:checked + span {
  background: #2e7d32;
}

.switch input:checked + span:before {
  transform: translateX(20px);
}

/******************************** RECEIPT UI ********************************/

.receipt-layout {
  display: flex;
  gap: 25px;
  flex-wrap: wrap;
}

/* LEFT */
.receipt-form {
  flex: 1;
  min-width: 250px;
}

/* TOGGLE ROW */
.toggle-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 15px;
  padding: 12px;
  border-radius: 10px;
  background: #f9fafb;
  border: 1px solid #eee;
}

.toggle-row p {
  font-size: 12px;
  color: #777;
}

/* RIGHT PREVIEW */
.receipt-preview {
  width: 260px;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

/* RECEIPT PAPER STYLE */
.receipt-paper {
  width: 100%;
  background: #fff;
  border: 1px dashed #ccc;
  padding: 15px;
  font-family: monospace;
  font-size: 13px;
  text-align: center;
  border-radius: 8px;
}

.receipt-paper hr {
  border: none;
  border-top: 1px dashed #ccc;
  margin: 8px 0;
}

.header-preview {
  font-weight: bold;
}

.footer-preview {
  margin-top: 10px;
  font-size: 12px;
}

/******************************** TAX & DISCOUNT UI ********************************/

.tax-layout {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-top: 15px;
}

/* BOX */
.tax-box {
  flex: 1;
  min-width: 250px;
  background: #f9fafb;
  padding: 15px;
  border-radius: 12px;
  border: 1px solid #eee;
}

/* TITLE */
.tax-box h4 {
  margin-bottom: 10px;
  font-size: 14px;
  color: #114500;
}

/* INPUT */
.tax-box input {
  width: 100%;
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #ddd;
  background: #fff;
}

/* SPACING */
.tax-box .input-group {
  margin-bottom: 12px;
}

/******************************** INVENTORY UI ********************************/

.inventory-grid {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-top: 15px;
}

/* BOX STYLE */
.inventory-box {
  flex: 1;
  min-width: 250px;
  background: #f9fafb;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 15px;
}

/* TITLE */
.inventory-box h4 {
  margin-bottom: 10px;
  font-size: 14px;
  color: #114500;
}

/* SMALL TEXT */
.inventory-box small {
  font-size: 11px;
  color: #777;
}

/******************************** SYSTEM UI ********************************/

.system-grid {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-top: 15px;
}

/* BOX */
.system-box {
  flex: 1;
  min-width: 250px;
  background: #f9fafb;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 15px;
}

/* TITLE */
.system-box h4 {
  margin-bottom: 10px;
  font-size: 14px;
  color: #114500;
}

/******************************** NOTIFICATIONS UI ********************************/

.notif-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 15px;
}

/* CARD */
.notif-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9fafb;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 15px;
  transition: 0.2s;
}

.notif-card:hover {
  transform: translateY(-2px);
}

/* LEFT SIDE */
.notif-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.notif-left i {
  font-size: 18px;
  color: #2e7d32;
}

.notif-left p {
  font-size: 12px;
  color: #777;
  margin-top: 3px;
}

/******************************** SECURITY UI ********************************/

.security-grid {
  display: flex;
  gap: 20px;
  flex-wrap: wrap;
  margin-top: 15px;
}

/* BOX */
.security-box {
  flex: 1;
  min-width: 250px;
  background: #f9fafb;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 15px;
}

/* SUB TEXT */
.sub {
  font-size: 12px;
  color: #777;
  margin-bottom: 10px;
}

/* DANGER BUTTON (Backup) */
.danger-btn {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 8px;
  background: #dc2626;
  color: white;
  margin-bottom: 10px;
  cursor: pointer;
}

.danger-btn:hover {
  background: #b91c1c;
}

/* SECONDARY BUTTON (Restore) */
.secondary-btn {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 8px;
  background: #e5e7eb;
  color: #111;
  cursor: pointer;
}

.secondary-btn:hover {
  background: #d1d5db;
}

/******************************** INTEGRATIONS UI ********************************/

.integration-grid {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 15px;
}

/* CARD */
.integration-card {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f9fafb;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 15px;
  transition: 0.2s;
}

.integration-card:hover {
  transform: translateY(-2px);
}

/* LEFT SIDE */
.integration-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.integration-left p {
  font-size: 12px;
  color: #777;
  margin-top: 3px;
}

/* ICON BOX */
.icon-box {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: #e8f5e9;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #2e7d32;
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
<div class="settings-header">
  <div class="header-left">
    <h1>Settings</h1>
    <p>Manage system preferences and configurations</p>
  </div>

  <div class="header-right">
    <div class="date-box">
      <i class="fa-solid fa-calendar"></i>
      <span id="todayDate">Today: </span>
    </div>
  </div>
</div>
<div class="settings-container">

  <!-- LEFT SETTINGS MENU -->
  <div class="settings-sidebar">
    <button onclick="showTab('store')" class="active">Store</button>
    <button onclick="showTab('users')">Users</button>
    <button onclick="showTab('payments')">Payments</button>
    <button onclick="showTab('receipt')">Receipt</button>
    <button onclick="showTab('tax')">Tax</button>
    <button onclick="showTab('inventory')">Inventory</button>
    <button onclick="showTab('system')">System</button>
    <button onclick="showTab('notifications')">Notifications</button>
    <button onclick="showTab('security')">Security</button>
    <button onclick="showTab('integrations')">Integrations</button>
  </div>

  <!-- RIGHT CONTENT -->
  <div class="settings-content">

    <!-- STORE -->
<div id="store" class="tab active">

  <div class="settings-card">
    <h3>Store Settings</h3>
    <p>Update your business information and tax configuration</p>

    <div class="form-grid">

      <div class="input-group">
        <label>Business Name</label>
        <input type="text" placeholder="Cafe Bella">
      </div>

      <div class="input-group">
        <label>Contact Number</label>
        <input type="text" placeholder="09123456789">
      </div>

      <div class="input-group">
        <label>Address</label>
        <input type="text" placeholder="Imus, Cavite">
      </div>

      <div class="input-group">
        <label>VAT (%)</label>
        <input type="text" placeholder="12%">
      </div>

    </div>

    <div class="input-group" style="margin-top:15px;">
      <label>Business Logo</label>
      <div class="file-box">
        Click to upload logo
        <input type="file" style="display:none;">
      </div>
    </div>

    <button class="save-btn">Save Changes</button>

  </div>

</div>

<div id="users" class="tab">

  <!-- USER LIST PREVIEW -->
  <div class="settings-card">
    <h3>Existing Users</h3>

    <div class="user-list" id="userList">
      <?php
      // Query to fetch all users from the database
      try {
          $stmt = $pdo->query("SELECT id, full_name, email, role, status, is_banned FROM users ORDER BY id DESC");
          $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

          if ($users) {
              foreach ($users as $user) {
                  $user_status_badge = '';
                  if ($user['is_banned'] == 1) {
                      $user_status_badge = '<span class="badge bg-danger">Banned</span>';
                  } elseif (isset($user['status']) && $user['status'] == 'Pending') {
                      $user_status_badge = '<span class="badge bg-warning text-dark">Pending Approval</span>';
                  } else {
                      $user_status_badge = '<span class="badge bg-success">Approved</span>';
                  }

                  // Determine role class for styling
                  $role_class = strtolower($user['role']); // e.g., 'admin', 'staff'
                  if ($role_class == 'cashier') $role_class = 'cashier'; // if you have specific style for cashier

                  ?>

                  <div class="user-item d-flex align-items-center mb-3 p-2 border rounded" data-user-id="<?= $user['id'] ?>">
                      <div class="user-info">
                          <div class="avatar"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></div>
                          <div>
                              <strong><?= htmlspecialchars($user['full_name']) ?></strong>
                              <p class="mb-0 small text-muted"><?= htmlspecialchars($user['email']) ?></p>
                              <span class="role <?= $role_class ?>"><?= htmlspecialchars($user['role']) ?></span>
                              <?= $user_status_badge ?>
                          </div>
                      </div>
                      <div class="user-actions">
                          <?php if ($user['status'] == 'Pending'): ?>
                              <button class="btn btn-sm btn-success approve-btn" data-user-id="<?= $user['id'] ?>">Approve</button>
                          <?php endif; ?>

                          <?php if ($user['is_banned'] == 0): ?>
                              <button class="btn btn-sm btn-warning ban-btn" 
                                data-user-id="<?= $user['id'] ?>" 
                                data-user-role="<?= $user['role'] ?>" 
                                data-active-admins="<?= $active_admin_count ?>">
                                Ban
                              </button>
                          <?php else: ?>
                              <button class="btn btn-sm btn-info unban-btn" data-user-id="<?= $user['id'] ?>">Unban</button>
                          <?php endif; ?>

                          <?php
                        // Ipapakita lang ang dropdown kung ang user ay approved at hindi banned
                        if ($user['status'] == 'Approved' && $user['is_banned'] == 0) : ?>
                          <select class="form-select form-select-sm d-inline-block w-auto change-role-select" 
                            data-user-id="<?= $user['id'] ?>" 
                            data-original-role="<?= $user['role'] ?>"> <!-- ITO ANG DAGDAG -->
                              <option value="Admin" <?= ($user['role'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                              <option value="Staff" <?= ($user['role'] == 'Staff') ? 'selected' : '' ?>>Staff</option>
                          </select>
                        <?php endif; ?>
                      </div>
                  </div>
                  <?php
              }
          } else {
              echo "<p>No users found.</p>";
          }
      } catch (PDOException $e) {
          echo "<p>Error loading users: " . $e->getMessage() . "</p>";
      }
      ?>
    </div>

  </div>

</div>

<div id="payments" class="tab">

  <div class="settings-card">
    <h3>Payment Settings</h3>
    <p>Select which payment methods are available in your POS</p>

    <div class="payment-grid">

      <!-- CASH -->
      <div class="payment-card active">
        <div class="payment-left">
          <i class="fa-solid fa-money-bill-wave"></i>
          <div>
            <strong>Cash</strong>
            <p>Accept physical cash payments</p>
          </div>
        </div>
        <label class="switch">
          <input type="checkbox" checked>
          <span></span>
        </label>
      </div>

      <!-- GCASH -->
      <div class="payment-card active">
        <div class="payment-left">
          <i class="fa-solid fa-wallet"></i>
          <div>
            <strong>GCash</strong>
            <p>Accept digital wallet payments</p>
          </div>
        </div>
        <label class="switch">
          <input type="checkbox" checked>
          <span></span>
        </label>
      </div>

    </div>

  </div>

</div>

<div id="receipt" class="tab">

  <div class="settings-card">
    <h3>Receipt Settings</h3>
    <p>Customize how your receipts appear to customers</p>

    <div class="receipt-layout">

      <!-- LEFT FORM -->
      <div class="receipt-form">

        <div class="input-group">
          <label>Header Text</label>
          <input type="text" placeholder="Cafe Bella - Thank you!">
        </div>

        <div class="input-group">
          <label>Footer Text</label>
          <input type="text" placeholder="Visit us again!">
        </div>

        <div class="toggle-row">
          <div>
            <strong>Auto Print</strong>
            <p>Automatically print receipt after payment</p>
          </div>

          <label class="switch">
            <input type="checkbox">
            <span></span>
          </label>
        </div>

        <button class="save-btn">Save Changes</button>

      </div>

      <!-- RIGHT PREVIEW -->
      <div class="receipt-preview">
        <div class="receipt-paper">
          <p class="header-preview">Cafe Bella</p>
          <hr>
          <p>Latte x1 ........ ₱120</p>
          <p>Cake x1 ......... ₱90</p>
          <hr>
          <p><strong>Total: ₱210</strong></p>
          <hr>
          <p class="footer-preview">Thank you!</p>
        </div>
      </div>

    </div>

  </div>

</div>

<div id="tax" class="tab">

  <div class="settings-card">
    <h3>Tax & Discounts</h3>
    <p>Configure tax rates and customer discount policies</p>

    <div class="tax-layout">

      <!-- TAX SECTION -->
      <div class="tax-box">
        <h4>Tax Settings</h4>

        <div class="input-group">
          <label>VAT Rate (%)</label>
          <input type="number" placeholder="12">
        </div>

        <div class="toggle-row">
          <div>
            <strong>Apply VAT to all items</strong>
            <p>Automatically include VAT in every transaction</p>
          </div>

          <label class="switch">
            <input type="checkbox" checked>
            <span></span>
          </label>
        </div>
      </div>

      <!-- DISCOUNT SECTION -->
      <div class="tax-box">
        <h4>Discount Settings</h4>

        <div class="input-group">
          <label>Senior / PWD Discount (%)</label>
          <input type="number" placeholder="20">
        </div>

        <div class="input-group">
          <label>Promo Discount (%)</label>
          <input type="number" placeholder="10">
        </div>

        <div class="toggle-row">
          <div>
            <strong>Allow stacking discounts</strong>
            <p>Apply multiple discounts in one transaction</p>
          </div>

          <label class="switch">
            <input type="checkbox">
            <span></span>
          </label>
        </div>
      </div>

    </div>

    <button class="save-btn">Save Changes</button>

  </div>

</div>

<div id="inventory" class="tab">

  <div class="settings-card">
    <h3>Inventory Settings</h3>
    <p>Manage stock monitoring and product tracking rules</p>

    <div class="inventory-grid">

      <!-- LOW STOCK -->
      <div class="inventory-box">
        <h4>Stock Monitoring</h4>

        <div class="input-group">
          <label>Low Stock Alert Threshold</label>
          <input type="number" placeholder="10">
          <small>Get notified when stock falls below this number</small>
        </div>

        <div class="toggle-row">
          <div>
            <strong>Enable Alerts</strong>
            <p>Receive automatic low stock warnings</p>
          </div>

          <label class="switch">
            <input type="checkbox" checked>
            <span></span>
          </label>
        </div>
      </div>

      <!-- BARCODE -->
      <div class="inventory-box">
        <h4>Barcode Settings</h4>

        <div class="input-group">
          <label>Barcode Format</label>
          <select>
            <option>Auto Generate</option>
            <option>EAN-13</option>
            <option>Code 128</option>
            <option>Custom Format</option>
          </select>
        </div>

        <div class="input-group">
          <label>Prefix Code (optional)</label>
          <input type="text" placeholder="CB-">
          <small>Example: CB-001, CB-002</small>
        </div>
      </div>

    </div>

    <button class="save-btn">Save Changes</button>

  </div>

</div>

<div id="system" class="tab">

<div class="settings-card">

  <h2>System Preferences</h2>
  <p>Configure system-wide behavior and display settings</p>

  <div class="setting-group">
    <h4>General</h4>

    <label>Currency</label>
    <select>
      <option>PHP (₱)</option>
    </select>

    <label>Time Format</label>
    <select>
      <option>24 Hour</option>
      <option>12 Hour</option>
    </select>
  </div>

  <div class="setting-group">
    <h4>Display Settings</h4>

    <!-- DARK MODE -->
    <div class="toggle-row">
      <div>
        <strong>Dark Mode</strong>
        <p>Switch system theme appearance</p>
      </div>
      <label class="switch">
        <input type="checkbox" id="darkToggle">
        <span class="slider"></span>
      </label>
    </div>

    <!-- COMPACT MODE -->
    <div class="toggle-row">
      <div>
        <strong>Compact Mode</strong>
        <p>Reduce spacing for more data view</p>
      </div>
      <label class="switch">
        <input type="checkbox" id="compactToggle">
        <span class="slider"></span>
      </label>
    </div>

  </div>

  <button onclick="applySettings()" class="apply-btn">
    Apply Settings
  </button>

</div>

</div>

<div id="notifications" class="tab">

  <div class="settings-card">
    <h3>Notifications</h3>
    <p>Manage system alerts and automatic updates</p>

    <div class="notif-grid">

      <!-- LOW STOCK -->
      <div class="notif-card">
        <div class="notif-left">
          <i class="fa-solid fa-boxes-stacked"></i>
          <div>
            <strong>Low Stock Alert</strong>
            <p>Get notified when items are running low</p>
          </div>
        </div>

        <label class="switch">
          <input type="checkbox" checked>
          <span></span>
        </label>
      </div>

      <!-- SALES REPORTS -->
      <div class="notif-card">
        <div class="notif-left">
          <i class="fa-solid fa-chart-line"></i>
          <div>
            <strong>Sales Reports</strong>
            <p>Daily and weekly sales summary alerts</p>
          </div>
        </div>

        <label class="switch">
          <input type="checkbox" checked>
          <span></span>
        </label>
      </div>

    </div>

  </div>

</div>

<div id="security" class="tab">

  <div class="settings-card">
    <h3>Backup & Security</h3>
    <p>Protect your system and manage data recovery options</p>

    <div class="security-grid">

      <!-- BACKUP -->
      <div class="security-box">
        <h4>Data Backup</h4>
        <p class="sub">Create or restore system data</p>

        <button class="danger-btn">
          <i class="fa-solid fa-download"></i> Backup Data
        </button>

        <button class="secondary-btn">
          <i class="fa-solid fa-upload"></i> Restore Data
        </button>
      </div>

      <!-- PASSWORD -->
      <div class="security-box">
        <h4>Password Security</h4>
        <p class="sub">Update your admin credentials</p>

        <div class="input-group">
          <label>New Password</label>
          <input type="password" placeholder="Enter new password">
        </div>

        <div class="input-group">
          <label>Confirm Password</label>
          <input type="password" placeholder="Confirm password">
        </div>

        <button class="save-btn">Update Password</button>
      </div>

    </div>

  </div>

</div>

<div id="integrations" class="tab">

  <div class="settings-card">
    <h3>Integrations</h3>
    <p>Connect your POS system with external services</p>

    <div class="integration-grid">

      <!-- ACCOUNTING -->
      <div class="integration-card">
        <div class="integration-left">
          <div class="icon-box">
            <i class="fa-solid fa-calculator"></i>
          </div>

          <div>
            <strong>Accounting Software</strong>
            <p>Sync sales with accounting tools</p>
          </div>
        </div>

        <label class="switch">
          <input type="checkbox">
          <span></span>
        </label>
      </div>

      <!-- ONLINE ORDERS -->
      <div class="integration-card">
        <div class="integration-left">
          <div class="icon-box">
            <i class="fa-solid fa-cart-shopping"></i>
          </div>

          <div>
            <strong>Online Orders</strong>
            <p>Enable online order syncing</p>
          </div>
        </div>

        <label class="switch">
          <input type="checkbox" checked>
          <span></span>
        </label>
      </div>

    </div>

  </div>

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

/******************************** SETTINGS TAB ********************************/
function showTab(id) {
  const tabs = document.querySelectorAll('.tab');
  const buttons = document.querySelectorAll('.settings-sidebar button');

  tabs.forEach(tab => tab.classList.remove('active'));
  buttons.forEach(btn => btn.classList.remove('active'));

  document.getElementById(id).classList.add('active');

  buttons.forEach(btn => {
    if (btn.textContent.toLowerCase() === id) {
      btn.classList.add('active');
    }
  });
}

/******************************** PAYMENT TOGGLE UI ********************************/
document.querySelectorAll('.switch input').forEach(toggle => {
  toggle.addEventListener('change', function () {
    const card = this.closest('.payment-card');
    
    if (this.checked) {
      card.classList.add('active');
    } else {
      card.classList.remove('active');
    }
  });
});

/******************************** USER MANAGEMENT ACTIONS ********************************/
function sendUserAction(userId, action, newRole = null) {
    let sendData = { user_id: userId };

    if (action === 'ban') {
        sendData.is_banned = 1;
    } else if (action === 'unban') {
        sendData.is_banned = 0;
    } else if (action === 'approve') {
        sendData.is_approved = 1;
    } else if (action === 'change_role') {
        sendData.role = newRole;
    }

    fetch('webapp_php/update_user.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(sendData)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
        if (data.success) location.reload();
    });
}

// Approve Button
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('approve-btn')) {
        const userId = event.target.dataset.userId;
        if (confirm('Approve this user?')) {
            sendUserAction(userId, 'approve');
        }
    }
});

// Ban Button (Tamang proteksyon)
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('ban-btn')) {
        const userId = event.target.dataset.userId;
        const userRole = event.target.dataset.userRole;
        const activeAdmins = parseInt(event.target.dataset.activeAdmins);

        if (userRole === 'Admin' && activeAdmins <= 1) {
            alert('❌ Hindi pwedeng i-ban ang huling aktibong Admin!');
            return;
        }

        if (confirm('Sigurado ka bang i-ban ang user na ito?')) {
            sendUserAction(userId, 'ban');
        }
    }
});

// Unban Button
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('unban-btn')) {
        const userId = event.target.dataset.userId;
        if (confirm('I-unban na ba ang user na ito?')) {
            sendUserAction(userId, 'unban');
        }
    }
});

// Change Role Dropdown
document.addEventListener('change', function(event) {
    if (event.target.classList.contains('change-role-select')) {
        const userId = event.target.dataset.userId;
        const newRole = event.target.value;
        const originalRole = event.target.dataset.originalRole;

        if (confirm('Palitan ba ang role ng user na ito maging ' + newRole + '?')) {
            sendUserAction(userId, 'change_role', newRole);
        } else {
            event.target.value = originalRole; // Bumalik sa dati kung kanselahin
        }
    }
});
</script>
