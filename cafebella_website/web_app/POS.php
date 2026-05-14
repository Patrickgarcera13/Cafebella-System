<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../website_php/database.php';
require_once '../website_php/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>POS</title>

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
.POS-header {
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
.POS-header .header-left h1 {
  font-size: 20px;
  margin: 0;
  color: #114500;
  line-height: 1.2;
}
.POS-header .header-left p {
  font-size: 13px;
  margin-top: 6px;
  color: #6b7280;
  line-height: 1.4;
}
.POS-header .date-box {
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
  overflow: visible; /* ✅ Hayaan lang, pero naka-fix na yung kanan */
  min-height: 0;
  align-items: flex-start; /* ✅ Para hindi umunat yung kanan kasabay ng kaliwa */
}
.content-wrapper {
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}
/******************************** CATEGORIES ********************************/
.categories {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  gap: 8px;
  padding: 0;
  width: 100%;
  overflow-x: auto;
  margin-right: 0;
  flex-shrink: 0;
  margin-top: 8px;
  margin-bottom: 20px; /* ✅ Mas malaki ang layo pababa sa products */
}

.category-wrapper {
  display: flex !important;
  flex-direction: column !important;
  gap: 8px !important;
  width: 100% !important;
  align-items: flex-start !important;
}

/* ✅ EDIT / SAVE BUTTON - AYOS NA LAKI AT ITSURA */
#editMenuBtn {
  width: auto !important;
  padding: 10px 22px !important;
  font-size: 14px !important;
  font-weight: 600 !important;
  border-radius: 8px !important;
  margin-bottom: 8px !important;
  background: linear-gradient(135deg, #1b5e20, #2e7d32) !important;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
  color: white !important;
  border: none !important;
}

#editMenuBtn.save-mode {
  background: linear-gradient(135deg, #166534, #15803d) !important;
}

.categories button {
  padding: 10px 14px;
  border: none;
  background: #e0e0e0;
  cursor: pointer;
  border-radius: 8px;
  transition: 0.2s;
  text-align: center;
  font-size: 13px;
  white-space: nowrap;
}

.categories button.active {
  background: #2e7d32;
  color: white;
}
.left-panel > div:first-child { 
  margin-right: 0;
}
/******************************** PRODUCTS ********************************/
#addProductBtnFixed {
  display: none;
  margin: 10px 0 0 0;
  padding: 8px 18px;
  font-size: 13px;
  background: linear-gradient(135deg, #2e7d32, #66bb6a);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  width: fit-content;
}
#addProductBtnFixed:hover {
  transform: scale(1.03);
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
/* ✅ ADD PRODUCT MODAL STYLES */
#addProductModal .modal-content {
  width: 420px !important;
  max-height: 85vh;
  overflow-y: auto;
}
.image-preview-box {
  text-align: center;
  margin: 10px 0;
  padding: 10px;
  border: 1px dashed #ccc;
  border-radius: 8px;
}
#productImagePreview {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 6px;
  margin-bottom: 8px;
}
.btn-sm {
  padding: 4px 8px !important;
  font-size: 12px !important;
  margin: 2px;
}
.variant-row {
  display: flex;
  gap: 6px;
  align-items: center;
  margin: 6px 0;
  padding: 6px;
  background: #f8f9fa;
  border-radius: 6px;
}
.variant-row input {
  flex: 1;
  padding: 6px !important;
  margin: 0 !important;
  font-size: 13px !important;
}
/******************************** SCROLLBAR ********************************/
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-thumb {
  background: #2e7d32;
  border-radius: 10px;
}
/******************************** PRODUCTS ********************************/
.products {
  flex: 1; /* Kukunin nito ang natitirang space */
  overflow-y: auto;
  min-height: 0;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 15px;
}
.card {
  background: #fff;
  padding: 12px;
  border-radius: 14px;
  cursor: pointer;
  text-align: center;
  height: 210px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;

  box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  transition: all 0.25s ease;
  border: 1px solid #f1f1f1;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 25px rgba(0,0,0,0.15);
  border-color: #2e7d32;
}
.card img {
  width: 100%;
  height: 90px;
  object-fit: cover;
  border-radius: 10px;
}
.card h4 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;

  overflow: hidden;
  line-clamp: 2; /* future standard */
}
.card p {
  font-size: 16px;
  color: #2e7d32;
  font-weight: bold;
  margin-top: auto; /* 🔥 push to bottom */
}
.card .delete-btn {
  width: 100%;
  justify-content: center;
}

/******************************** ORDER PANEL ********************************/
.order {
  flex: 1;
  min-width: 360px;
  max-width: none; /* IMPORTANT: alisin limit */
  display: flex;
  flex-direction: column;
  background: #ffffff;
  border-radius: 18px;
  padding: 16px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
  border: 1px solid #eef0f2;
  align-self: stretch;
  margin-top: 20px;
  height: 100%;
  transition: all 0.3s ease;
}
.order-items {
  flex: 1;
  overflow-y: auto;
  min-height: 0;
  padding-right: 6px;
  position: relative;
}
/* ITEMS */
.order-item {
  display: grid;
  grid-template-columns: 1fr 220px;
  align-items: center;
  position: relative;
}
/* TOP HEADER */
.order-top {
  display: flex;
  justify-content: space-between;
  align-items: center;

  padding-bottom: 10px;
  border-bottom: 1px solid #f1f1f1;
}
.order-item-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.order-items::after {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  left: calc(100% - 200px); /* 🔥 EXACT POSITION NG DIVIDER */
  width: 2px;
  background: #ddd;
  display: none;
}
.order-item-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.order-price {
  font-weight: 600;
  font-size: 14px;
}

.order-top h2 {
  font-size: 16px;
  color: #1b5e20;
}

.order-top p {
  font-size: 12px;
  color: #888;
  margin-top: 2px;
}

.order-badge {
  width: 38px;
  height: 38px;
  border-radius: 12px;

  display: flex;
  align-items: center;
  justify-content: center;

  background: #e8f5e9;
  font-size: 18px;
}
.order-footer {
  margin-top: auto;
  padding-top: 12px;
  border-top: 1px solid #f1f1f1;
}
/******************************** QUANTITY ********************************/
.qty {
  font-weight: 700;
  color: #2e7d32; 
}
.qty-box {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin: 15px 0;
}
.qty-btn {
  width: 24px;
  height: 24px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.qty-box button:hover {
  background: #1b5e20;
}
.qty-minus {
  background: #e0e0e0;
}

.qty-plus {
  background: #2e7d32;
  color: white;
}
.qty-controls {
  display: flex;
  align-items: center;
  gap: 6px;
  width: 110pX;
  justify-content: center;
}
.qty-value {
  min-width: 18px;
  text-align: center;
  font-weight: bold;
}
.modal .qty-box button {
  width: 35px;
  height: 35px;
  border-radius: 10px;
  border: none;
  background: #2e7d32;
  color: white;
  font-size: 18px;
  cursor: pointer;
}
#qtyInput {
  width: 70px;
  height: 42px;
  text-align: center;
  font-size: 18px;
  font-weight: bold;
  border: 1px solid #ddd;
  border-radius: 10px;
  outline: none;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: textfield;
}

#qtyInput:focus {
  border-color: #2e7d32;
  box-shadow: 0 0 0 3px rgba(46,125,50,0.12);
}
/******************************** ITEM ********************************/
.item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 12px;
  margin-bottom: 8px;
  border-radius: 12px;
  background: #f9fafb;
  border: 1px solid #f1f1f1;
  transition: 0.2s;
}
.order-price {
  font-size: 13px;
  color: #555;
}
.item-name {
  font-size: 14px;
  font-weight: 500;

  white-space: normal;   /* 🔥 allow wrap */
  overflow: visible;
  text-overflow: unset;

  word-break: break-word; /* 🔥 important para mahahabang name */
}
.item-left {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 3px;
  min-width: 0;
  max-width: 100%;
  padding-right: 10px;
}
.item-right {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 220px;   /* consistent column */
  padding-left: 12px;
  border-left: 2px solid #ddd; /* 🔥 REAL DIVIDER */
  height: 100%;
}
.item button {
  width: 26px;
  height: 26px;
  border: none;
  border-radius: 8px;
  background: #fff5f5;
  color: #e53935;
  font-size: 14px;
  font-weight: bold;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;

  transition: all 0.2s ease;
}
.item button:hover {
  background: #e53935;
  color: white;
  transform: scale(1.1);
  box-shadow: 0 4px 10px rgba(229,57,53,0.3);
}

/******************************** EMPTY ********************************/
.empty-cart {
  text-align: center;
  color: #aaa;
  margin-top: 20px;
  display: none;
}
.empty-cart p {
  font-size: 14px;
}
.empty-cart span {
  font-size: 12px;
}


.summary-box {
  background: linear-gradient(135deg, #2e7d32, #66bb6a);
  padding: 12px;
  border-radius: 14px;
  color: white;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* CHECKOUT BUTTON */
.checkout-btn {
  width: 100%;
  margin-top: 10px;

  padding: 14px;

  border: none;
  border-radius: 14px;

  background: #1b5e20;
  color: white;

  font-weight: bold;

  cursor: pointer;

  display: flex;
  justify-content: center;
  gap: 8px;

  transition: 0.2s;
}

.checkout-btn:hover {
  background: #144d18;
  transform: translateY(-2px);
}


/******************************** TOTAL ********************************/
.total {
  font-weight: bold;
  font-size: 18px;
  margin-top: 10px;
}
.total-box {
  margin-top: 10px;
  padding: 12px;
  background: linear-gradient(135deg, #2e7d32, #66bb6a);
  color: white;
  border-radius: 14px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: bold;
}
button.action {
  padding: 10px 16px; /* ✅ Pinaliit */
  border: none;
  color: white;
  background: linear-gradient(135deg, #2e7d32, #66bb6a);
  cursor: pointer;
  border-radius: 8px; /* ✅ Mas maliit na kurba */
  font-weight: 500; /* ✅ Hindi masyadong matapang */
  font-size: 14px;
  transition: 0.2s;
}
button.action:hover {
  transform: scale(1.03);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
.left-panel {
  display: flex;
  flex-direction: row;
  flex: 3;
  min-height: 0;
  padding: 20px 30px;
  align-items: flex-start; /* Para nasa taas */
}


.content-wrapper-inside {
  display: flex;
  flex: 1;
}
/******************************** REMOVE BUTTON ********************************/
.remove-btn {
  font-size: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.2s ease;
  border: none;
  border-radius: 6px;
  background: #fff5f5;
  color: #e53935;
  cursor: pointer;
  width: 26px;
  height: 26px;
  margin-left: 6px;
}

.remove-btn:hover {
  background: #e53935;
  color: white;
  transform: scale(1.1);
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
  background: rgba(0,0,0,0.6);
  backdrop-filter: blur(6px);

  justify-content: center;
  align-items: center;
}

.modal-content {
  background: #ffffff;
  padding: 25px;
  width: 360px;
  border-radius: 18px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.25);
  transform: scale(0.9);
  animation: popIn 0.2s ease forwards;
}

@keyframes popIn {
  to {
    transform: scale(1);
  }
}
.modal-items {
  max-height: 150px;
  overflow-y: auto;
  margin-bottom: 10px;
}

.section {
  margin-top: 10px;
}

.modal-content select,
.modal-content input {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border-radius: 10px;
  border: 1px solid #ddd;
}
.modal-content .modal-actions {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.modal-content .modal-actions button {
  flex: 1;              /* 🔥 equal width */
  padding: 12px;
  border-radius: 10px;
  font-weight: 600;
}
.cancel-btn {
  background: #e74c3c !important;
}

.cancel-btn:hover {
  background: #c0392b !important;
}
/******************************** VOUCHER ********************************/
.voucher-box {
  margin-top: 10px;
  padding: 10px;
  border: 1px solid #eee;
  border-radius: 6px;
  background: #fafafa;
}
/******************************** DELETE BUTTON ********************************/
.delete-btn {
  margin-top: 8px;
  padding: 6px 10px;
  border: 1px solid #ffdddd;
  background: #fff5f5;
  color: #e74c3c;
  font-size: 12px;
  border-radius: 8px;
  cursor: pointer;

  display: inline-flex;
  align-items: center;
  gap: 6px;

  transition: all 0.2s ease;
}

.delete-btn:hover {
  background: #e74c3c;
  color: white;
  border-color: #e74c3c;
  transform: translateY(-1px);
  box-shadow: 0 6px 12px rgba(231, 76, 60, 0.2);
}

.delete-btn:active {
  transform: scale(0.96);
}
/******************************** BUTTON iMPROVEMENT ********************************/
.action {
  padding: 12px;
  border: none;
  color: white;
  background: linear-gradient(135deg, #2e7d32, #66bb6a);
  cursor: pointer;
  border-radius: 12px;
  font-weight: bold;
  transition: 0.2s;
}

.action:hover {
  transform: scale(1.04);
  box-shadow: 0 8px 18px rgba(0,0,0,0.2);
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
    <div class="POS-header">
      <div class="header-left">
        <h1>Point of Sale</h1>
        <p>Handle your daily transactions and payment</p>
      </div>

      <div class="header-right">
        <div class="date-box">
          <i class="fa-solid fa-calendar"></i>
          <span id="todayDate">Today: </span>
        </div>
      </div>
    </div>


    <div class="content">

      <!-- LEFT COLUMN -->
      <div class="left-panel">

          <!-- EDIT BUTTON AT CATEGORIES NAGKASAMA SA ISANG COLUMN -->
          <div class="category-wrapper">
                  <?php if(isAdmin()): ?>
                  <button id="editMenuBtn" class="action">✏️ Edit Menu</button>
                  <?php endif; ?>
              <div class="categories" id="categories"></div>

                <button id="addProductBtnFixed" onclick="addProduct()">+ Add Product</button>
          </div>

                    <!-- PRODUCTS -->
              <div class="products" id="products"></div>
    
      </div>

      <!-- RIGHT COLUMN (ORDER PANEL FULL HEIGHT) -->
      <div class="order">

        <!-- TOP HEADER -->
        <div class="order-top">
          <div>
            <h2>Order Summary</h2>
            <p id="itemCount">0 items</p>
          </div>

          <div class="order-badge">
            🛒
          </div>
        </div>

        <!-- ITEMS -->
        <div id="orderItems" class="order-items"></div>

        <!-- EMPTY STATE -->
        <div class="empty-cart" id="emptyCart">
          <p>🛒 Your cart is empty</p>
          <span>Add items to start order</span>
        </div>

        <!-- FOOTER -->
        <div class="order-footer">

          <div class="summary-box">
            <div class="summary-row">
              <span>Total</span>
              <strong id="total">₱0.00</strong>
            </div>
          </div>

          <button id="placeOrderBtn" class="checkout-btn">
            <i class="fas fa-cash-register"></i>
            Place Order
          </button>

        </div>

      </div>

    </div>

</div>

<!-- PAYMENT MODAL -->
<div id="paymentModal" class="modal">
  <div class="modal-content">

    <h3>Payment</h3>

    <div id="modalOrderItems" class="modal-items"></div>

    <p id="subtotalText">Subtotal: ₱0</p>
    <p id="totalText">Total: ₱0</p>

    <!-- DISCOUNT -->
    <div class="section">
      <label>Discount</label>
      <select id="discountType">
        <option value="0">None</option>
        <option value="20">Senior (20%)</option>
        <option value="10">PWD (10%)</option>
      </select>

      <input type="number" id="customDiscount" placeholder="Custom %" />
    </div>

    <!-- PAYMENT METHOD -->
    <div class="section">
      <label>Payment Method</label>
      <select id="paymentMethod" onchange="handlePaymentMethod()">
        <option value="">Select</option>
        <option value="Cash">Cash</option>
        <option value="GCash">GCash</option>
      </select>
    </div>

    <!-- CASH -->
    <div id="cashBox" class="section" style="display:none;">
      <input type="number" id="amountInput" placeholder="Enter amount" />
      <p id="changeText">Change: ₱0</p>
    </div>

    <!-- GCASH -->
    <div id="gcashBox" class="section" style="display:none;">
      <p>Scan QR to pay</p>
    </div>

    <!-- BUTTONS -->
    <div class="modal-actions">
      <button onclick="confirmPayment()" class="action">Confirm</button>
      <button onclick="closeModal()" class="action cancel-btn">Cancel</button>
    </div>

  </div>
</div>

    <!-- POS MODALS -->
     <!-- ADD CATEGORY MODAL -->
<div id="addCategoryModal" class="modal">
  <div class="modal-content" style="width: 400px;">
    <h3 style="margin-bottom: 20px; color: #1b5e20;">Add New Category</h3>

    <div class="section">
      <label for="newCategoryName">Category Name</label>
      <input 
        type="text" 
        id="newCategoryName" 
        placeholder="Enter category name..."
        style="width: 100%; padding: 12px; margin-top: 8px; border-radius: 10px; border: 1px solid #ddd;"
      >
    </div>

    <div class="modal-actions" style="margin-top: 25px;">
      <button onclick="submitAddCategory()" class="action">✅ Add Category</button>
      <button onclick="closeAddCategoryModal()" class="action cancel-btn">❌ Cancel</button>
    </div>
  </div>
</div>

<!-- PRODUCT MODAL -->
<div id="productModal" class="modal">
  <div class="modal-content" style="width:300px; text-align:center;">
    <!-- ADD THIS LINE FOR THE IMAGE -->
    <img id="modalProductImage" src="" alt="Product Image" style="max-width:100px; max-height:100px; margin-bottom: 10px; border-radius: 5px;">

    <h3 id="modalProductName"></h3>
    <p id="modalProductPrice" style="color:#2e7d32; font-weight:bold;"></p>
    <p id="modalProductSize" style="font-size: 0.9em; color: #666;"></p> <!-- Add this for size -->

    <div class="qty-box">
      <button onclick="changeQty(-1)">−</button>

    <input
      type="text"
      id="qtyInput"
      value="1"
      inputmode="numeric"
      oninput="manualQtyInput()"
      onblur="fixQtyInput()"
    >

      <button onclick="changeQty(1)">+</button>
    </div>

    <div class="modal-actions">
      <button onclick="addToCartFromModal()" class="action">
        Add
      </button>

      <button onclick="closeProductModal()" class="action cancel-btn">
        Cancel
      </button>
    </div>
  </div>
</div>

<!-- ✅ ADD PRODUCT MODAL -->
<div id="addProductModal" class="modal">
  <div class="modal-content">
    <h3 style="margin-bottom: 15px; color: #1b5e20;">Add New Product</h3>

    <!-- IMAGE -->
<div class="section">
  <label>Product Image</label>
  <div class="image-preview-box">
    <!-- ✅ DEFAULT IMAGE AGAD ANG NAKALAGAY -->
    <img id="productImagePreview" src="IMAGES/POS_image/foodpic.jpg" alt="Preview">
    <div>
      <button type="button" class="action btn-sm" onclick="document.getElementById('productImageInput').click()">📁 Choose Image</button>
      <button type="button" class="action btn-sm" onclick="useDefaultImage()">🔄 Default Image</button>
      <input type="file" id="productImageInput" accept="image/*" style="display: none;" onchange="previewImage(event)">
    </div>
    <!-- ✅ ITATAGO NATIN ANG PATH NA GAGAMITIN SA SAVING -->
    <input type="hidden" id="productImagePath" value="IMAGES/POS_image/foodpic.jpg">
  </div>
</div>

    <!-- NAME -->
    <div class="section">
      <label>Product Name</label>
      <input type="text" id="newProductName" placeholder="e.g. Fried Rice" style="width:100%; padding:8px; margin-top:5px; border-radius:6px; border:1px solid #ddd;">
    </div>

    <!-- BASE PRICE -->
    <div class="section">
      <label>Base Price (₱)</label>
      <input type="number" id="newProductPrice" step="0.01" min="0" placeholder="0.00" style="width:100%; padding:8px; margin-top:5px; border-radius:6px; border:1px solid #ddd; transition: all 0.2s;">
      <small id="basePriceNote" style="color:#888; font-size:11px; display:none; margin-top:4px;">⚠️ Naka-disable: Presyo ay nakabase na sa mga Variant/Size</small>
    </div>

    <!-- VARIANTS / SIZES -->
    <div class="section">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
        <label>Variants / Sizes</label>
        <button type="button" class="action btn-sm" onclick="addVariantRow()">+ Add Variant</button>
      </div>
      <div id="variantsContainer">
        <p style="color:#888; font-size:12px;">Optional: Add sizes or variants with different prices</p>
      </div>
    </div>

    <!-- BUTTONS -->
    <div class="modal-actions" style="margin-top:20px;">
      <button onclick="submitAddProduct()" class="action">✅ Save Product</button>
      <button onclick="closeAddProductModal()" class="action cancel-btn">❌ Cancel</button>

    </div>
    </div>
  </div>
</div>

</body>
</html>
<script>
let isAdmin = <?php echo isAdmin() ? 'true' : 'false'; ?>;

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
    if (btn.dataset.page.toLowerCase() === 'pos.php') {
      btn.classList.add('active');
    }
  });
}

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
};

/******************************** GLOBAL VARIABLES ********************************/
let menu = {}; 
let cart = [];
let editMode = false;
let currentCategory = { id: null, name: null }; 
let allCategories = []; 
const API_URL = '../website_php/api.php';

/******************************** EDIT MENU BUTTON ********************************/
const editBtn = document.getElementById("editMenuBtn");
if(editBtn){
  editBtn.onclick = function(){
    editMode = !editMode;
    this.innerText = editMode ? "💾 Save Changes" : "✏️ Edit Menu";
    this.classList.toggle("save-mode", editMode);
    renderCategories();
    renderCurrentCategory();
  };
}

/******************************** CATEGORY RENDER ********************************/
function renderCategories(){
  const container = document.getElementById("categories");
  container.innerHTML = "";

  // ALL BUTTON
  const allBtn = document.createElement("button");
  allBtn.innerText = "All";
  allBtn.classList.toggle("active", currentCategory === "all" || currentCategory.id === null);
  allBtn.onclick = () => {
    currentCategory = "all";
    renderCategories();
    renderAllProducts();
  };
  container.appendChild(allBtn);

  allCategories.forEach(cat => {
    const btn = document.createElement("button");
    btn.innerText = cat.category_name;
    btn.classList.toggle("active", currentCategory.id === cat.category_id);

    btn.onclick = () => {
      currentCategory = { id: cat.category_id, name: cat.category_name };
      renderCategories();
      renderCurrentCategory();
    };

    if(editMode && isAdmin){
      const del = document.createElement("span");
      del.innerText = " ❌";
      del.style.color = "red";
      del.style.marginLeft = "5px";
      del.onclick = (e) => {
        e.stopPropagation();
        deleteCategory(cat.category_id);
      };
      btn.appendChild(del);
    }

    container.appendChild(btn);
  });

  // ADD CATEGORY BUTTON
  if(editMode && isAdmin){
    const addCatBtn = document.createElement("button");
    addCatBtn.innerText = "+ Add Category";
    addCatBtn.className = "action";
    addCatBtn.style.padding = "8px 14px";
    addCatBtn.style.fontSize = "13px";
    addCatBtn.onclick = addCategory;
    container.appendChild(addCatBtn);
  }

  // SHOW / HIDE ADD PRODUCT BUTTON
  const addProductBtn = document.getElementById("addProductBtnFixed");
  addProductBtn.style.display = (editMode && isAdmin && currentCategory !== "all" && currentCategory.id) ? "block" : "none";
}

/******************************** RENDER CURRENT CATEGORY ********************************/
function renderCurrentCategory(){
  const container = document.getElementById("products");
  container.innerHTML = "";

  if(currentCategory === "all") { 
    renderAllProducts(); 
    return; 
  }

  if(!currentCategory.id || !menu[currentCategory.id]) {
      container.innerHTML = "<p style='padding:50px; text-align:center; color:#888;'>❌ WALANG PRODUKTO DITO</p>";
      return;
  }

  const products = menu[currentCategory.id] || [];
  
  // ✅ I-GROUP ANG PRODUKTO AYON SA PANGALAN
  const grouped = groupProductsByName(products);

  const productGrid = document.createElement("div");
  productGrid.style.display = "grid";
  productGrid.style.gridTemplateColumns = "repeat(5, 1fr)";
  productGrid.style.gap = "15px";
  productGrid.style.width = "100%";

  // ✅ I-RENDER BAWAT GROUP
  Object.keys(grouped).forEach(prodName => {
    const items = grouped[prodName];
    const mainProduct = items[0];

    const div = document.createElement('div');
    div.className = "card";
    div.style.position = "relative";

    // ✅ ITAMA ANG LARAWAN
    let imagePath = mainProduct.product_image;
    if(imagePath === null || imagePath === "" || imagePath === undefined || imagePath === "null") {
        imagePath = 'IMAGES/POS_image/foodpic.jpg';
    }

    // ✅ IPAKITA LAHAT NG VARIANT + PRESYO
    let variantsHTML = `<div style="margin-top:8px; display:flex; flex-direction:column; gap:4px;">`;
    items.forEach(p => {
      let label = "";
      let price = 0;

      if(p.variants && p.variants.length > 0){
        p.variants.forEach(v => {
          variantsHTML += `<button style="background:#1b5e20; color:white; border:none; padding:4px; border-radius:4px; font-size:12px; cursor:pointer;" 
          onclick="openProductModal(${JSON.stringify(p).replace(/"/g, '&quot;')})">
            ${v.variant_name} — ₱${parseFloat(v.price).toFixed(2)}
          </button>`;
        });
      } else {
        variantsHTML += `<button style="background:#1b5e20; color:white; border:none; padding:4px; border-radius:4px; font-size:12px; cursor:pointer;" 
        onclick="openProductModal(${JSON.stringify(p).replace(/"/g, '&quot;')})">
          Regular — ₱${parseFloat(p.base_price).toFixed(2)}
        </button>`;
      }
    });
    variantsHTML += `</div>`;

    div.innerHTML = `
      <img src="${imagePath}" style="width:100%; height:120px; object-fit:cover; border-radius:8px 8px 0 0;">
      <div style="padding:10px; text-align:center;">
        <div style="font-weight:bold; font-size:14px; margin-bottom:5px;">${prodName}</div>
        ${variantsHTML}
      </div>
    `;

    productGrid.appendChild(div);
  });

  container.appendChild(productGrid);
}

/******************************** RENDER ALL PRODUCTS ********************************/
function renderAllProducts(){
  let allProducts = [];
  Object.values(menu).forEach(list => allProducts = allProducts.concat(list));

  // ✅ I-GROUP AYON SA PANGALAN
  const grouped = groupProductsByName(allProducts);

  const container = document.getElementById("products");
  container.innerHTML = "";

  const productGrid = document.createElement("div");
  productGrid.style.display = "grid";
  productGrid.style.gridTemplateColumns = "repeat(5, 1fr)";
  productGrid.style.gap = "15px";
  productGrid.style.width = "100%";

  Object.keys(grouped).forEach(prodName => {
    const items = grouped[prodName];
    const mainProduct = items[0];

    const div = document.createElement('div');
    div.className = "card";
    div.style.position = "relative";

    let imagePath = mainProduct.product_image;
    if(imagePath === null || imagePath === "" || imagePath === undefined || imagePath === "null") {
        imagePath = 'IMAGES/POS_image/foodpic.jpg';
    }

    let variantsHTML = `<div style="margin-top:8px; display:flex; flex-direction:column; gap:4px;">`;
    items.forEach(p => {
      if(p.variants && p.variants.length > 0){
        p.variants.forEach(v => {
          variantsHTML += `<button style="background:#1b5e20; color:white; border:none; padding:4px; border-radius:4px; font-size:12px; cursor:pointer;" 
          onclick="openProductModal(${JSON.stringify(p).replace(/"/g, '&quot;')})">
            ${v.variant_name} — ₱${parseFloat(v.price).toFixed(2)}
          </button>`;
        });
      } else {
        variantsHTML += `<button style="background:#1b5e20; color:white; border:none; padding:4px; border-radius:4px; font-size:12px; cursor:pointer;" 
        onclick="openProductModal(${JSON.stringify(p).replace(/"/g, '&quot;')})">
          Regular — ₱${parseFloat(p.base_price).toFixed(2)}
        </button>`;
      }
    });
    variantsHTML += `</div>`;

    div.innerHTML = `
      <img src="${imagePath}" style="width:100%; height:120px; object-fit:cover; border-radius:8px 8px 0 0;">
      <div style="padding:10px; text-align:center;">
        <div style="font-weight:bold; font-size:14px; margin-bottom:5px;">${prodName}</div>
        ${variantsHTML}
      </div>
    `;

    productGrid.appendChild(div);
  });

  container.appendChild(productGrid);
}
/******************************** HELPER: GROUP PRODUCTS BY NAME ********************************/
function groupProductsByName(productsArray){
  return productsArray.reduce((groups, product) => {
    let name = product.product_name.trim();
    if(!groups[name]) groups[name] = [];
    groups[name].push(product);
    return groups;
  }, {});
}
/******************************** DELETE FUNCTIONS ********************************/
function deleteCategory(catId){
  if(!confirm(`Delete this category?`)) return;
  delete menu[catId];
  allCategories = allCategories.filter(c => c.category_id !== catId);
  const keys = Object.keys(menu);
  if(keys.length === 0){
    currentCategory = { id: null, name: null };
    document.getElementById("products").innerHTML = "<p>No categories available</p>";
    renderCategories();
    return;
  }
  if(catId === currentCategory.id){
    const firstCat = allCategories[0];
    currentCategory = { id: firstCat.category_id, name: firstCat.category_name };
  }
  renderCategories();
  renderCurrentCategory();
}

function deleteProduct(catId, productName){
  if(!confirm(`Delete "${productName}"?`)) return;
  menu[catId] = menu[catId].filter(item => item.product_name !== productName);
  renderCurrentCategory();
}

/******************************** ADD CATEGORY ********************************/
function addCategory(){
  document.getElementById("addCategoryModal").style.display = "flex";
  document.getElementById("newCategoryName").value = "";
  document.getElementById("newCategoryName").focus();
}

async function submitAddCategory(){
  const name = document.getElementById("newCategoryName").value.trim();
  if(!name){ alert("Please enter category name!"); return; }

  try {
    const response = await fetch(API_URL + '?action=addCategory', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `category_name=${encodeURIComponent(name)}`
    });
    const result = await response.json();
    if(result.status === 'success'){
      allCategories.push(result.data);
      menu[result.data.category_id] = [];
      currentCategory = { id: result.data.category_id, name: result.data.category_name };
      closeAddCategoryModal();
      renderCategories();
      renderCurrentCategory();
      alert("Category saved successfully!");
    } else { alert("Error: " + result.message); }
  } catch (error) { console.error(error); alert("Failed to connect to server."); }
}

function closeAddCategoryModal(){
  document.getElementById("addCategoryModal").style.display = "none";
}
document.getElementById("addCategoryModal").addEventListener('click', e => { if(e.target === this) closeAddCategoryModal(); });

/******************************** PRODUCT MODAL ********************************/
let selectedProduct = null;
let selectedQty = 1;

function openProductModal(item){
  selectedProduct = item;
  selectedQty = 1;

  // ✅ ILAGAY ANG PANGALAN NG PRODUKTO
  document.getElementById("modalProductName").innerText = item.product_name;

  // ✅ PINAKA-IMPORTANTE: ITAMA ANG LARAWAN
  let imagePath = p.product_image;
if (!imagePath || imagePath === "null" || imagePath === "") {
    imagePath = 'IMAGES/POS_image/foodpic.jpg';
}
  document.getElementById("modalProductImage").src = imagePath;
  
  let priceDisplay = "";
  const oldSel = document.getElementById("variantSelector");
  if(oldSel) oldSel.remove();

  // ✅ KUNG MAY VARIANTS (SOLO / DOUBLE)
  if(item.variants && item.variants.length > 0){
    priceDisplay = "Select size/variant:";
    const sel = document.createElement("select");
    sel.id = "variantSelector";
    sel.style.width = "100%";
    sel.style.padding = "8px";
    sel.style.margin = "8px 0";
    sel.style.borderRadius = "6px";
    document.querySelector("#productModal .modal-content").insertBefore(sel, document.querySelector(".qty-box"));

    item.variants.forEach(v => {
      const opt = document.createElement("option");
      opt.value = v.price;
      opt.text = `${v.variant_name} - ₱${parseFloat(v.price).toFixed(2)}`;
      sel.appendChild(opt);
    });
  } 
  // ✅ KUNG WALANG VARIANT — GAMITIN ANG BASE_PRICE
  else {
    let presyo = parseFloat(item.base_price || 0);
    priceDisplay = "₱" + presyo.toFixed(2);
  }

  document.getElementById("modalProductPrice").innerText = priceDisplay;
  document.getElementById("qtyInput").value = selectedQty;
  document.getElementById("productModal").style.display = "flex";
}

function addToCartFromModal(){
  let finalPrice = parseFloat(selectedProduct.base_price);
  let productName = selectedProduct.product_name;

  if(selectedProduct.variants && selectedProduct.variants.length > 0){
    const sel = document.getElementById("variantSelector");
    finalPrice = parseFloat(sel.value);
    productName = `${selectedProduct.product_name} [${sel.options[sel.selectedIndex].text.split(' - ')[0]}]`;
  }

  let existing = cart.find(i => i.name === productName);
  if(existing){ 
    existing.qty += selectedQty; 
  } else { 
    cart.push({ 
      name: productName, 
      price: finalPrice, 
      qty: selectedQty 
    }); 
  }

  renderCart();
  closeProductModal();
}

function closeProductModal(){
  document.getElementById("productModal").style.display = "none";
  const oldSel = document.getElementById("variantSelector");
  if(oldSel) oldSel.remove();
}

document.getElementById("productModal").addEventListener("click", function(e){
  if(e.target === this){
    closeProductModal();
  }
});

/******************************** CHANGE QUANTITY ( - / + ) ********************************/
function changeQty(val){
  selectedQty = Math.max(1, selectedQty + val);
  document.getElementById("qtyInput").value = selectedQty;
}

/******************************** CHANGE QUANTITY ( manual ) ********************************/
function manualQtyInput(){
  let input = document.getElementById("qtyInput").value;
  input = input.replace(/[^0-9]/g, "");
  document.getElementById("qtyInput").value = input;
  if(input !== "" && parseInt(input) >= 1){
    selectedQty = parseInt(input);
  }
}

/******************************** QUANTITY INPUT ( manual ) ********************************/
function fixQtyInput(){
  const inputField = document.getElementById("qtyInput");
  if(inputField.value === "" || parseInt(inputField.value) < 1){
    inputField.value = 1;
    selectedQty = 1;
  }
}

function selectVariant(productName, variantName, price){
  cart.push({ 
    name: `${productName} [${variantName}]`, 
    price: price, 
    qty: 1 
  });
  renderCart();
}

/******************************** ORDER CART FUNCTIONS ********************************/
function renderCart(){
  const container = document.getElementById("orderItems");
  container.innerHTML = "";
  const empty = document.getElementById("emptyCart");

  if(cart.length === 0){
    empty.style.display = "block";
    document.getElementById("total").innerText = "₱0.00";
    return;
  } else {
    empty.style.display = "none";
  }

  let total = 0;
  cart.forEach((item, index) => {
    total += item.price * item.qty;

    const div = document.createElement("div");
    div.className = "item";
    div.innerHTML = `
      <div class="order-item">
        <div class="item-left">
          <span class="item-name">${item.name}</span>
          <span class="order-price">₱${(item.price * item.qty).toFixed(2)}</span>
        </div>
        <div class="item-right">
          <div class="qty-controls">
            <button onclick="decreaseQty(${index})" class="qty-btn qty-minus">−</button>
            <span class="qty-value">${item.qty}</span>
            <button onclick="increaseQty(${index})" class="qty-btn qty-plus">+</button>
          </div>
          <button onclick="removeItem(${index})" class="remove-btn">✕</button>
        </div>
      </div>
    `;
    container.appendChild(div);
  });

  document.getElementById("total").innerText = "₱" + total.toFixed(2);
}

function increaseQty(index){
  cart[index].qty += 1;
  renderCart();
}

function decreaseQty(index){
  cart[index].qty -= 1;
  if(cart[index].qty <= 0){
    cart.splice(index, 1);
  }
  renderCart();
}

function removeItem(index){
  cart.splice(index, 1);
  renderCart();
}

/******************************** ADD PRODUCT MODAL & FUNCTIONS ********************************/
let selectedProductImage = "IMAGES/POS_image/foodpic.jpg";

function addProduct(){
  if(!currentCategory.id || currentCategory === "all"){
    alert("⚠️ Please select a specific category first before adding product!");
    return;
  }
  document.getElementById("newProductName").value = "";
  document.getElementById("newProductPrice").value = "";
  document.getElementById("newProductPrice").disabled = false;
  document.getElementById("newProductPrice").style.background = "#fff";
  document.getElementById("newProductPrice").style.color = "#000";
  document.getElementById("basePriceNote").style.display = "none";
  selectedProductImage = "IMAGES/POS_image/foodpic.jpg";
  document.getElementById("productImagePreview").src = selectedProductImage;
  document.getElementById("variantsContainer").innerHTML = '<p style="color:#888; font-size:12px;">Optional: Add sizes or variants with different prices</p>';
  document.getElementById("addProductModal").style.display = "flex";
}

function previewImage(event) {
  const reader = new FileReader();
  reader.onload = function() {
    const output = document.getElementById('productImagePreview');
    output.src = reader.result;
    document.getElementById('productImagePath').value = 'custom'; // tanda na may upload
  };
  reader.readAsDataURL(event.target.files[0]);
}

function useDefaultImage() {
  document.getElementById('productImagePreview').src = 'IMAGES/POS_image/foodpic.jpg';
  document.getElementById('productImagePath').value = 'IMAGES/POS_image/foodpic.jpg';
  document.getElementById('productImageInput').value = "";
}

function addVariantRow(){
  const container = document.getElementById("variantsContainer");
  if(container.querySelector("p")) container.innerHTML = "";

  const row = document.createElement("div");
  row.className = "variant-row";
  row.innerHTML = `
    <input type="text" placeholder="Size / Variant Name e.g. Small, Large" class="variant-name">
    <input type="number" step="0.01" min="0" placeholder="Price ₱" class="variant-price">
    <button type="button" class="action btn-sm cancel-btn" onclick="removeVariantRow(this)">✕</button>
  `;
  container.appendChild(row);

  toggleBasePriceState();
}

function removeVariantRow(btn){
  btn.parentElement.remove();
  toggleBasePriceState();
}

function toggleBasePriceState(){
  const variantRows = document.querySelectorAll(".variant-row");
  const basePriceInput = document.getElementById("newProductPrice");
  const note = document.getElementById("basePriceNote");

  if(variantRows.length > 0){
    basePriceInput.disabled = true;
    basePriceInput.style.background = "#f1f1f1";
    basePriceInput.style.color = "#888";
    basePriceInput.value = ""; 
    note.style.display = "block";
  } else {
    basePriceInput.disabled = false;
    basePriceInput.style.background = "#fff";
    basePriceInput.style.color = "#000";
    note.style.display = "none";
  }
}

async function submitAddProduct() {
  const name = document.getElementById('newProductName').value.trim();
  const catId = currentCategory.id;
  const basePrice = document.getElementById('newProductPrice').value || '0.00';
  
  // ✅ DEFAULT IMAGE AGAD
  let finalImage = 'IMAGES/POS_image/foodpic.jpg';
  const fileInput = document.getElementById('productImageInput');

  // ✅ KUNG MAY PINILING FILE → PALITAN ANG PATH
  if (fileInput.files.length > 0) {
    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    const uploadRes = await fetch('api.php?action=uploadImage', { method: 'POST', body: formData });
    const uploadData = await uploadRes.json();
    if (uploadData.status === 'success') {
      finalImage = uploadData.path; // ← GAGAMITIN ANG BAGONG LITRATO
    }
  } 
  // ✅ KUNG WALA / DEFAULT LANG → IWAN SA DEFAULT (papasok na NULL sa DB dahil sa API)

  // ✅ KUNIN ANG VARIANTS
  const variants = [];
  document.querySelectorAll('.variant-row').forEach(row => {
    const vName = row.querySelector('.variant-name').value.trim();
    const vPrice = row.querySelector('.variant-price').value.trim();
    if ( vName && vPrice ) variants.push({ name: vName, price: vPrice });
  });

  // ✅ IPASA LAHAT SA API
  const res = await fetch('api.php?action=addProduct', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      category_id: catId,
      product_name: name,
      price: basePrice,
      product_image: finalImage, // ← IPAPASA: DEFAULT O CUSTOM
      variants: JSON.stringify(variants)
    })
  });

  const data = await res.json();
  alert(data.message);
  if (data.status === 'success') {
    closeAddProductModal();
    loadMenuFromDatabase(); // ← BINALI KO PARA MABASA ULIT AGAD
  }
}

async function saveProductToDB(category_id, name, price, image = "IMAGES/POS_image/foodpic.jpg", variants = "[]", has_variant = 0){
  if (image === "IMAGES/POS_image/foodpic.jpg" || image.startsWith('data:image')) {
    image = null;
  }

  try {
    const response = await fetch(API_URL + '?action=addProduct', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `category_id=${category_id}&product_name=${encodeURIComponent(name)}&price=${price}&product_image=${encodeURIComponent(image)}&variants=${encodeURIComponent(variants)}&has_variant=${has_variant}`
    });
    const res = await response.json();
    if(res.status === 'success'){
      console.log("✅ Product saved");
    } else {
      alert("❌ Error: " + res.message);
    }
  } catch (err) {
    console.error("⚠️ Error", err);
  }
}

function closeAddProductModal(){
  document.getElementById("addProductModal").style.display = "none";
}
document.getElementById("addProductModal").addEventListener("click", e => {
  if(e.target === this) closeAddProductModal();
});

/******************************** PAYMENT MODAL & LOGIC ********************************/
const modal = document.getElementById("paymentModal");

document.getElementById("placeOrderBtn").addEventListener("click", () => {
  if(cart.length === 0){
    alert("No items in cart!");
    return;
  }
  openModal();
});

function openModal(){
  modal.style.display = "flex";
  const container = document.getElementById("modalOrderItems");
  container.innerHTML = "";
  let subtotal = 0;

  cart.forEach(item => {
    subtotal += item.price * item.qty;
    container.innerHTML += `
      <div style="display:flex; justify-content:space-between;">
        <span>${item.name} (x${item.qty})</span>
        <span>₱${(item.price * item.qty).toFixed(2)}</span>
      </div>
    `;
  });

  document.getElementById("subtotalText").innerText = "Subtotal: ₱" + subtotal.toFixed(2);
  document.getElementById("totalText").innerText = "Total: ₱" + subtotal.toFixed(2);
  updateTotals();
}

function closeModal(){
  modal.style.display = "none";
}

document.getElementById("discountType").addEventListener("change", updateTotals);
document.getElementById("customDiscount").addEventListener("input", updateTotals);

function updateTotals(){
  let subtotal = 0;
  cart.forEach(item => subtotal += item.price * item.qty);
  let discount = parseFloat(document.getElementById("discountType").value);
  let custom = parseFloat(document.getElementById("customDiscount").value) || 0;
  let finalDiscount = discount > 0 ? discount : custom;
  let total = subtotal - (subtotal * finalDiscount / 100);
  document.getElementById("totalText").innerText = "Total: ₱" + total.toFixed(2);
}

document.getElementById("amountInput").addEventListener("input", computeChange);

function computeChange(){
  let totalText = document.getElementById("totalText").innerText;
  let total = parseFloat(totalText.replace("Total: ₱", "")) || 0;
  let amount = parseFloat(document.getElementById("amountInput").value);

  if(!amount || amount <= 0){
    document.getElementById("changeText").innerText = "Change: ₱0";
    return;
  }
  let change = amount - total;
  document.getElementById("changeText").innerText = "Change: ₱" + change.toFixed(2);
}

function confirmPayment(){
  const method = document.getElementById("paymentMethod").value;
  const amount = parseFloat(document.getElementById("amountInput").value);
  const totalText = document.getElementById("totalText").innerText;
  const total = parseFloat(totalText.replace("Total: ₱", "")) || 0;

  if(method === ""){
    alert("Select payment method!");
    return;
  }
  if(method === "Cash"){
    if(isNaN(amount) || amount <= 0){
      alert("Please enter a valid cash amount!");
      return;
    }
    if(amount < total){
      alert("The entered amount is insufficient to complete the transaction.");
      return;
    }
  }
  if(method === "GCash"){
    alert("Redirect to GCash QR / Payment (Simulation only)");
  }

  alert("Payment Successful!");
  cart = [];
  renderCart();
  closeModal();
}

function handlePaymentMethod(){
  const method = document.getElementById("paymentMethod").value;
  document.getElementById("gcashBox").style.display = "none";
  document.getElementById("cashBox").style.display = "none";
  if(method === "GCash") document.getElementById("gcashBox").style.display = "block";
  if(method === "Cash") document.getElementById("cashBox").style.display = "block";
}

/******************************** SIDEBAR TOGGLE ********************************/
const menuBtn = document.getElementById("menu-btn");
const sidebar = document.querySelector(".sidebar");
const main = document.querySelector(".main");

menuBtn.onclick = function() {
  sidebar.classList.toggle("hide");
  main.classList.toggle("full");
};

/******************************** DATE & INITIAL LOAD ********************************/
function updatePOSDate(){
  const el = document.getElementById("todayDate");
  const now = new Date();
  el.textContent = "Today: " + now.toLocaleDateString('en-US', {
    month: 'long', day: 'numeric', year: 'numeric'
  });
}
async function loadMenuFromDatabase() {
  try {
    console.log("🔄 KUMUKUHA NG CATEGORIES...");
    const categoriesRes = await fetch(API_URL + '?action=getCategories');
    const categoriesText = await categoriesRes.text(); // makita kung ano talaga ang sagot
    console.log("📩 RAW RESPONSE:", categoriesText);

    const categoriesData = JSON.parse(categoriesText);

    if (categoriesData.status === 'success' && categoriesData.data.length > 0) {
      allCategories = categoriesData.data;
      menu = {};

      console.log("✅ NAKUHA ANG CATEGORIES:", allCategories);

      for (const cat of allCategories) {
        console.log(`🔄 KUMUKUHA NG PRODUKTO PARA SA: ${cat.category_name}`);
        const productsRes = await fetch(API_URL + `?action=getProducts&cat_id=${cat.category_id}`);
        const productsText = await productsRes.text();
        console.log(`📩 PRODUKTO PARA SA ${cat.category_name}:`, productsText);

        const productsData = JSON.parse(productsText);

        if (productsData.status === 'success' && Array.isArray(productsData.data)) {
          menu[cat.category_id] = productsData.data;
        } else {
          menu[cat.category_id] = [];
        }
      }

      currentCategory = {
        id: allCategories[0].category_id,
        name: allCategories[0].category_name
      };

    } else {
      allCategories = [];
      menu = {};
      currentCategory = { id: null, name: null };
    }

    renderCategories();
    renderCurrentCategory();
    renderCart();

  } catch (e) {
    console.error("❌ ERROR SA PAGKUHA:", e);
    alert("Hindi makonekta / Mali ang sagot: " + e.message);
    allCategories = [];
    menu = {};
    currentCategory = { id: null, name: null };
    renderCategories();
    renderCurrentCategory();
    renderCart();
  }
}

document.addEventListener("DOMContentLoaded", () => {
  updatePOSDate(); 
  loadMenuFromDatabase(); 
});
</script>