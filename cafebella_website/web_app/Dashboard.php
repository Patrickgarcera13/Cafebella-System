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
<title>Event Management</title>

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
.event-header {
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
.event-header .header-left h1 {
  font-size: 20px;
  margin: 0;
  color: #114500;
  line-height: 1.2;
}
.event-header .header-left p {
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

/******************************** EVENT LAYOUT FIX ********************************/
.event-layout {
  display: grid;
  grid-template-columns: 320px 1fr; /* LEFT fixed, RIGHT flexible */
  gap: 20px;
  padding: 0 25px;
  align-items: stretch;
}

/* LEFT SIDE CARD */
.event-card.modern-event-card {
  height: fit-content;
}

/* RIGHT SIDE CARD */
.event-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 18px;
  padding: 20px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.06);
  display: flex;
  flex-direction: column;
  min-height: 500px;
}
.event-title {
  margin-bottom: 15px;
}

.event-title h4 {
  font-size: 18px;
  font-weight: 700;
  color: #114500;
}

.event-title span {
  font-size: 13px;
  color: #6b7280;
}

.event-grid-modern {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
/******************************** STATS (MODERN CARDS) ********************************/
.stats{
  display:grid;
  grid-template-columns: repeat(4, 1fr);
  gap:15px;
  padding: 0 25px;
  margin-bottom:10px;
}
.stat-box{
  background:#ffffff;
  padding:20px;
  border-radius:16px;
  box-shadow:0 6px 15px rgba(0,0,0,0.06);
  display:flex;
  flex-direction:column;
  gap:8px;
  position:relative;
  overflow:hidden;
}
.stat-box::after{
  display: none;
}
.stat-box p{
  font-size:13px;
  color:#6b7280;
  font-weight:500;
}
.stat-box h2{
  font-size:28px;
  color:#114500;
  font-weight:700;
}

/******************************** MINI TABLE ********************************/
.mini-table{
  background:#ffffff;
  box-shadow:0 6px 15px rgba(0,0,0,0.06);
  display:flex;
  flex-direction:column;
  min-height:0;
  margin: 20px 25px;
  border-radius: 18px;
  padding: 22px;
}
.mini-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 16px;
  cursor: pointer;
  transition: 0.2s;
}
.mini-card:hover {
  background: linear-gradient(135deg, #1b5e20, #43a047);
  transform: translateY(-6px) scale(1.02);
  box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}
.mini-card:hover h4 {
  color: #2e7d32;
}
.mini-card:hover p {
  color: #4b5563;
}
.mini-card h4 {
  font-size: 14px;
  font-weight: 700;
  color: #114500;
  transition: all 0.3s ease;
}
.mini-card p {
  font-size: 13px;
  color: #6b7280;
  transition: all 0.3s ease;
}
.mini-card::before {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top left, rgba(255,255,255,0.15), transparent 60%);
  opacity: 0;
  transition: 0.3s ease;
  display: none;
}
.mini-card:hover {
  background: #f0fdf4; /* light green */
  border-color: #66bb6a;
  box-shadow: 0 6px 12px rgba(0,0,0,0.08);
  transform: translateY(-2px);
}
.mini-card:hover::before {
  opacity: 1;
}
.mini-card:active {
  transform: scale(0.98);
}
.mini-card::after {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top left, rgba(255,255,255,0.25), transparent 60%);
  opacity: 0;
  transition: 0.3s;
  display: none;
}
.mini-card:hover::after {
  opacity: 1;
}
/******************************** TABLE ********************************/
.table-header{
  display: grid;
  grid-template-columns: 2.2fr 2.2fr 1.6fr 1fr 1.6fr 1.4fr 1.6fr;
  align-items: center;
  gap: 12px;
  padding: 14px;
  border-radius: 12px;
  background: linear-gradient(135deg, #1b5e20, #43a047);
  color: white;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.5px;
  position: sticky;
  top: 0;
  z-index: 10;
}
.table-body{
  flex: 1;
  min-height: 0;
  overflow-y: auto;
  padding-right: 5px;
}
.table-row{
  background: #ffffff;
  border: 1px solid #f1f1f1;
  border-radius: 14px;
  padding: 14px 12px;
  margin-top: 10px;
  font-size: 13px;
  transition: 0.2s ease;
  display: grid;
  grid-template-columns: 2.2fr 2.2fr 1.6fr 1fr 1.6fr 1.4fr 1.6fr;
  align-items: center;
  gap: 12px;
}
.table-row strong{
  color: #111827;
  font-weight: 700;
}
.table-row span{
  font-size: 13px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 0;
}
.table-wrap{
  display: flex;
  flex-direction: column;
  flex: 1;
  min-height: 0;
  overflow: hidden;
}
.table-body::-webkit-scrollbar{
  width:6px;
}
.table-body::-webkit-scrollbar-thumb{
  background:#bbb;
  border-radius:10px;
}
.table-body::-webkit-scrollbar{
  width:6px;
}
.table-body::-webkit-scrollbar-thumb{
  background:#ccc;
  border-radius:10px;
}

/******************************** STATUS ********************************/
.status-badge{
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}
.status-badge.upcoming{
  background: #e0f2fe;
  color: #0369a1;
}
.status-badge.completed{
  background: #dcfce7;
  color: #166534;
}
.status-select{
  border: 1px solid #e5e7eb;
  border-radius: 999px;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  outline: none;
  cursor: pointer;
  transition: 0.2s;
  width: 100%;
  max-width: 130px;
  white-space: nowrap;
}
.status-select.completed{
  background: #dcfce7;
  color: #166534;
}
.status-select.upcoming{
  background: #e0f2fe;
  color: #0369a1;
}
.status-select.cancelled{
  background: #fee2e2;
  color: #991b1b;
}
.status-legend{
  display: flex;
  gap: 18px;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  margin: 20px 25px;
}
.status-legend span{
  display: flex;
  align-items: center;
  gap: 6px;
}
.status-legend span::before{
  content: "●";
  font-size: 14px;
}
.status-legend span:nth-child(1)::before{ color:#16a34a; }
.status-legend span:nth-child(2)::before{ color:#2563eb; }
.status-legend span:nth-child(3)::before{ color:#dc2626; }

/******************************** ACTION BUTTON ********************************/
.actions {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}
.actions button{
  border:none;
  background:#fdecea;
  color:#c62828;
  padding:8px;
  border-radius:10px;
  cursor:pointer;
  transition:0.2s;
}
.actions button:hover{
  background:#c62828;
  color:white;
}

/******************************** SECTIONS ********************************/
.section{
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 6px 16px rgba(0,0,0,0.05);
}
.section:hover {
  transform: translateY(-3px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.08);
}
.section h4{
  font-size: 14px;
  font-weight: 700;
  color: #114500;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.section p{
  font-size: 13px;
  color: #4b5563;
  margin: 6px 0;
}
.section-box{
  background: #fff;
  border: 1px solid #eee;
  border-radius: 12px;
  padding: 12px;
}
.section-box h3{
  font-size: 13px;
  color: #114500;
  margin-bottom: 8px;
}

/******************************** ADD BUTTONS ********************************/
.add-btn {
  background: linear-gradient(135deg, #66bb6a, #43a047);
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 10px;
  cursor: pointer;
  font-size: 13px;
  font-weight: 600;
}
.add-btn:hover {
  opacity: 0.9;
}
.add-staff-btn {
  margin-top: 10px;
  font-size: 12px;
  background: transparent;
  border: 1px dashed #66bb6a;
  color: #2e7d32;
  padding: 6px 10px;
  border-radius: 8px;
  cursor: pointer;
}
.add-staff-btn:hover {
  background: #e8f5e9;
}

/******************************** BOX ********************************/
.box{
  display: flex;
  flex-direction: column;
  gap: 4px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  padding: 16px;
  margin-top: 12px;
  transition: 0.25s ease;
}
.box:hover{
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.08);
  border-color: #c7e8ca;
}
.box h4{
  font-size: 14px;
  font-weight: 700;
  color: #114500;
  display: flex;
  align-items: center;
  gap: 8px;
}
.box p{
  font-size: 13px;
  color: #4b5563;
}
@keyframes fadeIn{
  from{
    opacity:0;
    transform: translateY(10px) scale(0.98);
  }
  to{
    opacity:1;
    transform: translateY(0) scale(1);
  }
}

/******************************** MODAL ********************************/
.modal{
  position:fixed;
  top:0;left:0;
  width:100%;height:100%;
  background:rgba(0,0,0,0.5);
  backdrop-filter: blur(6px); /* 🔥 glass effect */
  display:none;
  align-items:center;
  justify-content:center;
  z-index:999;
}
.modal-box{
  background: #ffffff;
  border-radius: 20px;
  width: 450px;
  max-height: 80vh;   
  padding: 25px;
  box-shadow: 0 20px 50px rgba(0,0,0,0.15);
  animation: fadeIn 0.25s ease;
  display: flex;
  flex-direction: column;
  overflow: hidden; 
}
.modal-box h3{
  font-size: 18px;
  font-weight: 700;
  color: #114500;
  margin-bottom: 15px;
}
.close{
  position: absolute;
  right: 20px;
  top: 18px;
  font-size: 16px;
  cursor: pointer;
  color: #ef4444;
  transition: 0.2s;
}
.close:hover{
  transform: rotate(90deg);
  color: #b91c1c;
}

/******************************** EVENT CARD ********************************/
.modern-event-card{
  background: linear-gradient(180deg, #ffffff, #f9fafb);
  border-radius: 22px;
  padding: 26px;
  position: relative;
  overflow: hidden;
}
.all-events-scroll{
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 0;
}

/******************************** STAFF ********************************/
.staff-group {
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  padding: 16px;
  margin-bottom: 14px;
  transition: 0.25s;
}
.staff-group:hover {
  border-color: #66bb6a;
  box-shadow: 0 8px 18px rgba(0,0,0,0.06);
}
.staff-header {
  display: flex;
  align-items: center;
  gap: 10px;
  font-weight: 700;
  color: #114500;
  margin-bottom: 10px;
}
.staff-header i {
  background: #e8f5e9;
  color: #2e7d32;
  padding: 6px;
  border-radius: 8px;
  font-size: 12px;
}
.staff-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.staff-chip {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 12px;
  display: flex;
  align-items: center;
  gap: 6px;
}
.staff-chip input {
  border: none;
  outline: none;
  font-size: 12px;
  width: 90px;
}
.staff-chip button {
  background: #fee2e2;
  border: none;
  color: #b91c1c;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  cursor: pointer;
  font-size: 10px;
}
.staff-name{
  font-weight: 500;
  color: #1f2937;
}
.staff-chip{
  overflow: visible;
}
.staff-group{
  overflow: visible;
}
.staff-grid p{
  font-size: 13px;
  margin: 4px 0;
  color: #374151;
}

/******************************** ASSIGN ********************************/
.assign-dropdown{
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 4px 6px;
  font-size: 11px;
  background: #f9fafb;
  cursor: pointer;
  position: relative;
  z-index: 999;
}
.assign-dropdown:hover{
  border-color: #66bb6a;
}


/******************************** VIEW ********************************/
.view-btn{
  background: #e8f5e9;
  color: #1b5e20;
  border: 1px solid #c8e6c9;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
  padding: 6px 10px;
  font-size: 12px;
  display: inline-block;
  border-radius: 8px;
  width: 100%;
  max-width: 130px;
  white-space: nowrap;
}
.view-btn:hover{
  background: #1b5e20;
  color: white;
}

/******************************** DETAILS ********************************/
.detail-header{
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 10px;
  border-bottom: 1px solid #eee;
}
.detail-header h2{
  font-size: 18px;
  color: #114500;
}
.detail-grid{
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
.detail-item{
  background: #f9fafb;
  padding: 10px;
  border-radius: 10px;
  border: 1px solid #eee;
}
.detail-item label{
  font-size: 11px;
  color: #6b7280;
}
.detail-item p{
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  margin-top: 2px;
}

/******************************** TAG ********************************/
.tag-group{
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.tag{
  background: #e8f5e9;
  color: #1b5e20;
  padding: 5px 10px;
  border-radius: 999px;
  font-size: 11px;
}

/******************************** PAYMENT ********************************/
.payment-grid{
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
}
.payment-grid div{
  background: #f9fafb;
  padding: 10px;
  border-radius: 10px;
  text-align: center;
}
.payment-grid span{
  font-size: 11px;
  color: #6b7280;
}
.payment-grid strong{
  display: block;
  margin-top: 4px;
  font-size: 14px;
}
.payment-status{
  margin-top: 10px;
  text-align: center;
}
.payment-status .badge{
  background: #fef3c7;
  color: #92400e;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}
.danger{
  color: #dc2626;
}
.notes{
  font-size: 13px;
  color: #4b5563;
  line-height: 1.4;
}
.locked{
  font-size: 11px;
  background: #f3f4f6;
  padding: 4px 8px;
  border-radius: 999px;
  color: #6b7280;
  font-weight: 500;
}

.assigned-label {
  background: #e8f5e9;
  color: #2e7d32;
  padding: 4px 8px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 600;
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
<div class="event-header">
  <div class="header-left">
    <h1>Event Management</h1>
    <p>Manage all approve and completed events</p>
  </div>

  <div class="header-right">
    <div class="date-box">
      <i class="fa-solid fa-calendar"></i>
      <span id="todayDate">Today: </span>
    </div>
  </div>
</div>

<!--------------------------------------- STATUS BOX ---------------------------------------------> 
<div class="stats">
  <div class="stat-box">
    <p>Total Events</p>
    <h2>0</h2>
  </div>

  <div class="stat-box">
    <p>Upcoming</p>
    <h2>0</h2>
  </div>

  <div class="stat-box">
    <p>Completed</p>
    <h2>0</h2>
  </div>

  <div class="stat-box">
    <p>Total Sales</p>
    <h2>0</h2>
  </div>
</div>

<!--------------------------------------- EVENT ---------------------------------------------> 
<div class="event-layout">

  <div class="event-card modern-event-card">

    <div class="event-title">
      <h4>Cafe BELLA Events</h4>
      <span>Overview</span>
    </div>

    <div class="event-grid-modern">

      <div class="mini-card" onclick="openBoothModal()" style="cursor:pointer;">
        <h4>Booths</h4>
        <p>Coffee • Matcha • Tattoo</p>
      </div>

      <div class="mini-card" onclick="openStaffModal()" style="cursor:pointer;">
        <h4>Staff</h4>
        <p id="staffPreview">Barista • Artist • Cashier</p>
      </div>

    </div>

  </div>

<!--------------------------------------- EVENT CARD ---------------------------------------------> 
<div class="event-card">

<div class="all-events-scroll">

  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
    <h3 class="title">All Events</h3>


  </div>

  <div class="table-wrap">

      <div class="table-header">
        <span>Name</span>
        <span>Email</span>
        <span>Contact</span>
        <span>Guests</span>
        <span>Location</span>
        <span>Sales</span>
        <span>Action</span>
      </div>

<!--------------------------------------- TABLE BODY ---------------------------------------------> 
      <div class="table-body" id="eventTable">

        <div class="table-row">
          <span><strong>Coffee Booth Event</strong></span>
          <span>coffee@email.com</span>
          <span>09171234567</span>
          <span>50</span>
          <span>Imus</span>
          <span><strong>₱8,500</strong></span>

          <div class="actions">
            <button><i class="fa-solid fa-trash"></i></button>
          </div>
        </div>

      </div>

    </div>

  </div>

</div>

<!--------------------------------------- MODAL / BOOTH ---------------------------------------------> 
<div class="modal" id="boothModal">
  <div class="modal-box">
    <span class="close" onclick="closeBoothModal()">✕</span>

    <h3>Booth Details</h3>

    <div class="box">
      <h4>Coffee Booth</h4>
      <p>₱5,000 • Up to 50 Cups</p>
    </div>

    <div class="box">
      <h4>Matcha Booth</h4>
      <p>₱9,000 • Up to 50 Cups</p>
    </div>

    <div class="box">
      <h4>Tattoo Event</h4>
      <p>2 minimalist tattoos for ₱1,000 (In Store)</p>
    </div>

  </div>
</div>

<!--------------------------------------- MODAL / STAFF ---------------------------------------------> 
<div class="modal" id="staffModal">
  <div class="modal-box">
    <span class="close" onclick="closeStaffModal()">✕</span>

    <h3 id="staffTitle">Staff List</h3>

    <div id="staffContainer"></div>

    <button id="editBtn" class="add-btn" onclick="enableEdit()">
      ✏️ Edit
    </button>

    <button id="saveBtn" class="add-btn" onclick="manualSave()" style="display:none; margin-top:10px;">
      💾 Save Changes
    </button>

  </div>
</div>

<!--------------------------------------- MODAL / EVENT ---------------------------------------------> 
<div class="modal" id="eventModal">
  <div class="modal-box">
    <span class="close" onclick="closeModal()">✕</span>
    <h3>Event Details</h3>
    <div id="eventDetails"></div>
  </div>
</div>

<!--------------------------------------- STATUS LEGEND ---------------------------------------------> 
<div class="status-legend">
  <span>Completed</span>
  <span>Upcoming</span>
  <span>Cancelled</span>
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

let isEditing = false;

let staffList = {
  Barista: ["Barista 1", "Barista 2"],
  Artist: ["Artist 1", "Artist 2"],
  Cashier: ["Cashier 1", "Cashier 2"]
};

let assignments = {
  Coffee: {
    Barista: [],
    Cashier: []
  },
  Matcha: {
    Barista: [],
    Cashier: []
  },
  Tattoo: {
    Artist: [] 
  }
};

loadStaff();
updateTodayDate();

/******************************** RENDER EVENTS  ********************************/
const events = [
{
event:"Coffee Booth Event",
client:"Juan Dela Cruz",
email:"juan@gmail.com",
contact:"09171234567",
guests:50,
location:"Imus",
date:"May 10, 2026",
status:"Upcoming",
paymentStatus:"Partial",
total:8500,
paid:3000,
services:["Coffee Booth","Matcha Booth"],
staff:{
  Barista: "",
  Artist: "",
  Cashier: ""
},
notes:"Outdoor setup"
}
];

/******************************** RENDER EVENTS ********************************/
function renderEvents(){
  const container = document.getElementById("eventTable");
    container.innerHTML = "";

  let total=0, upcoming=0, completed=0, revenue=0;

    events.forEach((e) => {
    total++;
    revenue += e.total;

    if(e.status==="Upcoming") upcoming++;
    if(e.status==="Completed") completed++;

    container.innerHTML += `
    <div class="table-row">
      <span><strong>${e.event}</strong></span>
      <span>${e.email}</span>
      <span>${e.contact}</span>
      <span>${e.guests}</span>
      <span>${e.location}</span>
      <span><strong>₱${e.total}</strong></span>

      <span class="actions">
        <button onclick="viewEvent(${events.indexOf(e)})" class="view-btn">
          View Details
        </button>
      </span>
    </div>
    `;
  });

  document.querySelectorAll(".stat-box h2")[0].innerText = total;
  document.querySelectorAll(".stat-box h2")[1].innerText = upcoming;
  document.querySelectorAll(".stat-box h2")[2].innerText = completed;
  document.querySelectorAll(".stat-box h2")[3].innerText = "₱" + revenue;
}

function viewEvent(i){
  const e = events[i];

  document.getElementById("eventDetails").innerHTML = `
  <div class="event-detail-card">

    <div class="detail-header">
      <h2>Event Details</h2>
      <span class="status-badge">${e.status}</span>
    </div>

    <div class="detail-grid">
      <div class="detail-item">
        <label>Client</label>
        <p>${e.client}</p>
      </div>

      <div class="detail-item">
        <label>Email</label>
        <p>${e.email}</p>
      </div>

      <div class="detail-item">
        <label>Contact</label>
        <p>${e.contact}</p>
      </div>

      <div class="detail-item">
        <label>Date</label>
        <p>${e.date}</p>
      </div>

      <div class="detail-item">
        <label>Guests</label>
        <p>${e.guests}</p>
      </div>

      <div class="detail-item">
        <label>Location</label>
        <p>${e.location}</p>
      </div>
    </div>

    <div class="section-box">
      <h3>Service</h3>
      <span class="tag">${e.services[0]}</span>
    </div>

<div class="section-box">
  <h3>Payment Summary</h3>

  <div class="payment-grid">

    <div>
      <span>Package Price</span>
      <strong>
        ₱${(() => {
          const service = e.services[0];

          const packages = {
            "Coffee Booth": 5000,
            "Matcha Booth": 9000,
            "Tattoo Event": 1000
          };

          return packages[service] || 0;
        })()}
      </strong>
    </div>

    <div>
      <span>Reservation Fee</span>
      <strong>₱2000</strong>
    </div>

    <div>
      <span>Balance</span>
      <strong class="danger">
        ₱${(() => {
          const service = e.services[0];

          const packages = {
            "Coffee Booth": 5000,
            "Matcha Booth": 9000,
            "Tattoo Event": 1000
          };

          const packagePrice = packages[service] || 0;
          const reservationFee = 2000;

          return packagePrice - reservationFee;
        })()}
      </strong>
    </div>

  </div>

  <div class="payment-status">
    <span class="badge">${e.paymentStatus}</span>
  </div>

</div>

    <div class="section-box">
      <h3>Assigned Staff</h3>
      <p>Barista: ${e.staff.Barista || "-"}</p>
      <p>Artist: ${e.staff.Artist || "-"}</p>
      <p>Cashier: ${e.staff.Cashier || "-"}</p>
    </div>

    <div class="section-box">
      <h3>Notes</h3>
      <p>${e.notes}</p>
    </div>

  </div>
  `;

  document.getElementById("eventModal").style.display = "flex";
}

function closeModal(){
  document.getElementById("eventModal").style.display = "none";
}

document.addEventListener("DOMContentLoaded", function () {

  const eventModal = document.getElementById("eventModal");

  eventModal.addEventListener("click", function(e){
    if (e.target === eventModal) {
      closeModal();
    }
  });

  document.addEventListener("keydown", function(e){
    if(e.key === "Escape"){
      closeModal();
    }
  });

});
renderEventsSorted(events);

function openBoothModal(){
  document.getElementById("boothModal").style.display = "flex";
}

function closeBoothModal(){
  document.getElementById("boothModal").style.display = "none";
}
window.addEventListener("click", function(e){
  const modal = document.getElementById("boothModal");
  if(e.target === modal){
    modal.style.display = "none";
  }
});
function closeStaffModal(){
  document.getElementById("staffModal").style.display = "none";
}
window.addEventListener("click", function(e){
  const staffModal = document.getElementById("staffModal");

  if(e.target === staffModal){
    staffModal.style.display = "none";
  }
});

console.log("Editing Mode:", isEditing);

/******************************** RENDER STAFF ********************************/
function renderStaff(){
  const container = document.getElementById("staffContainer");
  container.innerHTML = "";

  const icons = {
    Barista: "fa-mug-hot",
    Artist: "fa-paintbrush",
    Cashier: "fa-cash-register"
  };

  for(let role in staffList){

    container.innerHTML += `
      <div class="staff-group">

        <div class="staff-header">
          <i class="fa-solid ${icons[role] || "fa-user"}"></i>
          ${role}
        </div>

        <div class="staff-list">

${staffList[role].map((name,i)=>`

  <div class="staff-chip">

    ${
      isEditing
      ? `<input value="${name}" 
           onchange="updateStaff('${role}', ${i}, this.value)">`
      : `<span class="staff-name">${name}</span>`
    }

    ${
      !isEditing
      ? (
        role !== "Artist"
        ? `
      ${(() => {
        let assignedTo = "";

        for (let b in assignments) {
          if (assignments[b][role]?.includes(name)) {
            assignedTo = b;
          }
        }

        return assignedTo
          ? `<span class="assigned-label">✔ ${assignedTo}</span>`
          : `
            <select class="assign-dropdown"
              onchange="assignStaff('${role}', '${name}', this.value)">
              
              <option value="">Assign</option>
              <option value="Coffee">Coffee Booth</option>
              <option value="Matcha">Matcha Booth</option>
            </select>
          `;
      })()}
        `
        : `<span class="locked">Tattoo Event</span>`
      )
      : ""
    }

    ${
      isEditing
      ? `<button onclick="removeStaff('${role}', ${i})">×</button>`
      : ""
    }

  </div>

`).join("")}

        </div>

        ${
          isEditing
          ? `<button onclick="addStaff('${role}')" class="add-staff-btn">
              + Add ${role}
            </button>`
          : ""
        }

      </div>
    `;
  }
}

/******************************** MENU BUTTON ********************************/
function assignStaff(role, name, booth){

  if(!booth) return;

  const limits = {
    Barista: 2,
    Cashier: 1
  };

  if(role === "Artist") return;

  let current = assignments[booth][role];

  if(current.length >= limits[role]){
    alert(`${booth} Booth needs only ${limits[role]} ${role}`);
    return;
  }

  // remove from other booth
  for(let b in assignments){
    if(assignments[b][role]){
      assignments[b][role] =
        assignments[b][role].filter(s => s !== name);
    }
  }

  assignments[booth][role].push(name);

  console.log(assignments);

  // ✅ SHOW SAVE BUTTON
  const saveBtn = document.getElementById("saveBtn");
  saveBtn.style.display = "block";
  saveBtn.innerText = "💾 Save Assignments";
}

function enableEdit(){
  isEditing = true;

  document.getElementById("editBtn").style.display = "none";
  document.getElementById("saveBtn").style.display = "block";

  document.getElementById("staffTitle").innerText = "Staff List (Editable)";

  renderStaff();
}

function manualSave(){
  saveStaff();
  isEditing = false;

  document.getElementById("editBtn").style.display = "block";
  document.getElementById("saveBtn").style.display = "none";

  document.getElementById("staffTitle").innerText = "Staff List";

  renderStaff();

  updateStaffPreview(); // ✅ ADD THIS

  alert("Saved successfully ✅");
}

function updateStaff(role, index, value){
  staffList[role][index] = value;
}

function addStaff(role){
  staffList[role].push("New Staff");
  renderStaff();
}

function removeStaff(role, index){
  staffList[role].splice(index,1);
  renderStaff();
}

function loadStaff(){
  const saved = localStorage.getItem("staffList");

  if(saved){
    staffList = JSON.parse(saved);
  }

  updateStaffPreview(); // ✅ ADD THIS
}


function saveStaff(){
  localStorage.setItem("staffList", JSON.stringify(staffList));
}
function openStaffModal(){
  document.getElementById("staffModal").style.display = "flex";

  isEditing = false;

  document.getElementById("editBtn").style.display = "block";
  document.getElementById("saveBtn").style.display = "none";

  document.getElementById("staffTitle").innerText = "Staff List";

  renderStaff(); // ✅ CRITICAL
}

function updateStaffPreview(){
  const preview = document.getElementById("staffPreview");

  let result = [];

  for(let role in staffList){
    if(staffList[role].length > 0){
      result.push(role);
    }
  }

  preview.textContent = result.join(" • ");
}

function closeStaffModal(){
  document.getElementById("staffModal").style.display = "none";
}

function sortEvents() {
  const value = document.getElementById("sortSelect").value;

  let sorted = [...events];

  if (value === "high") {
    sorted.sort((a, b) => b.total - a.total);
  } 
  else if (value === "low") {
    sorted.sort((a, b) => a.total - b.total);
  }

  renderEventsSorted(sorted);
}

function renderEventsSorted(data) {
  const container = document.getElementById("eventTable");
  container.innerHTML = "";

  let total = 0, upcoming = 0, completed = 0, revenue = 0;

  data.forEach((e, i) => {
    total++;
    revenue += Number(e.total || 0);

    if (e.status === "Upcoming") upcoming++;
    if (e.status === "Completed") completed++;

    container.innerHTML += `
      <div class="table-row">
        <span><strong>${e.event}</strong></span>
        <span>${e.email}</span>
        <span>${e.contact}</span>
        <span>${e.guests}</span>
        <span>${e.location}</span>
        <span><strong>₱${e.total}</strong></span>

        <span class="actions">
          <button onclick="viewEvent(${i})" class="view-btn">
            View Details
          </button>
        </span>
      </div>
    `;
  });

  document.querySelectorAll(".stat-box h2")[0].innerText = total;
  document.querySelectorAll(".stat-box h2")[1].innerText = upcoming;
  document.querySelectorAll(".stat-box h2")[2].innerText = completed;
  document.querySelectorAll(".stat-box h2")[3].innerText = "₱" + revenue;
}
</script>
