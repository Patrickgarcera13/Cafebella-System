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
<title>Calendar</title>

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
.calendar-header {
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
.calendar-header .header-left h1 {
  font-size: 20px;
  margin: 0;
  color: #114500;
  line-height: 1.2;
}
.calendar-header .header-left p {
  font-size: 13px;
  margin-top: 6px;
  color: #6b7280;
  line-height: 1.4;
}
.calendar-header .date-box {
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

/******************************** LAYOUT ********************************/
.layout{
  display: grid;
  grid-template-columns: 1fr 260px; /* mini calendar + right panel */
  gap: 18px;
  flex: 1;
  min-height: 0;
  height: calc(100vh - 120px); /* 👈 mas mataas area */
  padding: 0 10px 10px;
}

/******************************** CALENDAR ********************************/
.calendar,
.mini-calendar,
.panel{
  background:white;
  border-radius:16px;
  padding:16px;
  border:1px solid #eef2f7;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  display:flex;
  flex-direction:column;
  height: auto;            
  max-height: none;   
}
.calendar .header{
  display:flex;
  justify-content:center;
  align-items:center;
  gap:20px;
}
.calendar-nav{
  display:flex;
  align-items:center;
  justify-content:space-between;

  width: 100%;
  padding: 10px 18px;

  border-radius: 16px;
  background: linear-gradient(135deg, #ffffff, #f8fafc);
  border: 1px solid #e5e7eb;
  box-shadow: 0 8px 18px rgba(0,0,0,0.06);
}

/******************************** MINI CALENDAR ********************************/
.mini-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 14px;
  padding: 4px 2px;
}
.mini-header h4 {
  font-size: 15px;
  font-weight: 700;
  color: #111827;
}
.mini-header button {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background: #f8fafc;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
}
.mini-header button:hover {
  background: #16a34a;
  color: white;
  border-color: #16a34a;
  transform: translateY(-1px);
}
.mini-calendar {
  background: #ffffff;
  border-radius: 20px;
  padding: 18px;
  border: 1px solid #eef2f7;
  box-shadow: 0 12px 30px rgba(0,0,0,0.08);
  width: 100%;
  transition: all 0.3s ease;
}
.mini-calendar h4{
  font-size:14px;
  font-weight:600;
  margin-bottom:10px;
  color:#111827;
}
.mini-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 6px;
  overflow: hidden;
  max-height: 360px;   /* adjust to fit 6 rows */
}
.mini-grid > div:first-child {
  font-size: 10px;
  font-weight: 700;
  color: #94a3b8;
  text-align: center;
  padding: 6px 0;
  letter-spacing: 0.5px;
  row-gap: 8px;
}
.mini-day{
  height: 58px;        /* 🔥 from ~42px → bigger cell */
  font-size: 13px;
  display: flex;
  align-items: flex-start;
  justify-content: flex-start;
  padding: 8px;
  position: relative;
  overflow: hidden;     /* keep events inside */
  border-radius: 12px;
  border: 1px solid #0d4204;  /* 👈 light clean border */
  background: #ffffff;
  box-sizing: border-box;
}
.mini-today {
  background: linear-gradient(135deg, #16a34a, #22c55e) !important;
  color: white !important;

  box-shadow:
    0 15px 35px rgba(34,197,94,0.45),
    0 0 0 3px rgba(34,197,94,0.2);

  transform: scale(1.06);
}
.mini-selected {
  outline: none;
  border: 2px solid #3b82f6;
  border-radius: 10px;
  box-sizing: border-box;
}
/* ✨ EMPTY CELLS */
.mini-day[style*="visibility: hidden"] {
  background: transparent;
  border: none;
  box-shadow: none;
}
.mini-day::after {
  content: "";
  position: absolute;
  bottom: 6px;
  left: 50%;
  transform: translateX(-50%);

  width: 5px;
  height: 5px;
  border-radius: 50%;

  background: transparent;
}
.mini-day.has-event::after {
  background: #22c55e;
  box-shadow: 0 0 8px #22c55e;
}

/* orange = pending */
.mini-day.has-pending::after {
  background: #f59e0b;
  box-shadow: 0 0 8px #f59e0b;
}

/* blue = accepted */
.mini-day.has-accepted::after {
  background: #3b82f6;
  box-shadow: 0 0 8px #3b82f6;
}
.mini-day,
.day-events,
.event-chip {
  z-index: 1;
}
/* ================= NEW CALENDAR UI FIX ================= */

.mini-calendar .calendar-nav{
  display:flex;
  align-items:center;
  justify-content:center;
  gap:15px;
  margin-bottom:12px;
  padding:14px;
  border-radius:16px;

  background: linear-gradient(135deg, #16a34a, #22c55e) !important;
  color:white;
  box-shadow: 0 10px 25px rgba(34,197,94,0.35);
}
.mini-calendar {
  height: fit-content;
  max-height: 520px;   /* 🔥 prevents overflow */
  overflow: hidden;
}

/* BUTTON */
.mini-calendar .nav-btn{
  width:36px;
  height:36px;
  border-radius:10px;
  border:none;
  background: rgba(255,255,255,0.25);
  color:white;
  cursor:pointer;
}
.mini-calendar .nav-btn:hover{
  background:white;
  color:#16a34a;
  transform:scale(1.1);
}
.mini-calendar .week-header{
  display:grid;
  grid-template-columns: repeat(7,1fr);
  padding:8px 0;
  margin-bottom:6px;
  background:#ecfdf5;
  border-radius:10px;
  border:1px solid #bbf7d0;
}
.mini-calendar .week-header div{
  text-align:center;
  font-size:11px;
  font-weight:700;
  color:#166534;
  letter-spacing:1px;
}
.mini-calendar .mini-grid{
  gap:6px;
}

.nav-btn{
  width: 38px;
  height: 38px;
  flex-shrink:0;
  border-radius: 12px;
  border: 1px solid #e5e7eb;

  background: white;
  color: #16a34a;

  font-size: 18px;
  font-weight: 700;

  cursor: pointer;

  display:flex;
  align-items:center;
  justify-content:center;

  transition: all 0.25s ease;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}
.nav-btn:hover{
  background: #16a34a;
  color: white;
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 10px 20px rgba(22,163,74,0.25);
}
.nav-left{
  display:flex;
  align-items:center;
  gap:12px;
  flex:1;
}
.nav-right{
  display:flex;
  align-items:center;
  gap:10px;
  justify-content:flex-end;
  flex:1;
}
/******************************** CURRENT DAY LEFT SIDE (inside Hours Calendar ) ********************************/
.left-today{
  display:flex;
  flex-direction:column;
  justify-content:center;
  align-items:center;
  font-size:12px;
  border-right:1px solid #e5e7eb;
}

#leftDay{
  font-weight:700;
  color:#16a34a; /* BLUE */
  font-size:18px;
  letter-spacing:1px;
}

#leftDate{
  font-size:42px;
  font-weight:800;
  color:#16a34a; 
  line-height:1;
}

/******************************** WEEK ( inside Hours Calendar ) ********************************/
.week{
  display: grid;
  grid-template-columns: 100px repeat(7,1fr);
  height: fit-content;  
  min-height: 0;
  max-height: calc(var(--visible-hours) * var(--cell-height));
  overflow-y: auto;
}
.week-days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  margin-top: 10px;
  padding: 10px 0;
  border-bottom: 1px solid #e5e7eb;
  background: #f8fafc;
  border-radius: 12px;
}
.week-days > div{
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
}

/******************************** TIMELINE ( inside Hour Calendar ) ********************************/
.time-slot{
  height: var(--cell-height);
  display:flex;
  align-items:center;
  justify-content:flex-end;
  padding-right:10px;
  font-size:12px;
  color:#6b7280;
}
.time-slot span{
  background: #ffffff;
  padding: 4px 8px;
  border-radius: 8px;
  border: 1px solid #eef2f7;
  box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
.time-slot:hover span{
  background: #f9fafb;
  border-color: #d1d5db;
  color: #111827;
  transform: translateX(-2px);
}
.current-hour{
  background: transparent;
  border-left: none;
  font-weight: 600;
}
.current-hour span{
  background: #025a24;
  color: #ffffff;
  border: 1px solid #e5e7eb;
  box-shadow: none;
}
.current-hour::before{
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 3px;
  background: #16a34a;
  box-shadow: 0 0 12px #22c55e;
  border-radius: 4px;
}
.current-date{
  font-size:13px;
  font-weight:600;
  color:#ffffff;
  white-space:nowrap;
}
/******************************** HEADER ********************************/
.header{
  display:flex;
  justify-content: center;
  gap: 15px;
  align-items:center;
  margin-bottom:15px;
}
.header-right{
  display:flex;
  align-items:center;
}
.header-left p{
  font-size:14px;
  color:#6b7280;
  margin-top: 4px;
}
.header-left h1{
  font-size:24px;
  font-weight:700;
  color:#114500;
  margin-bottom:4px;
  margin: 0;
}

/******************************** NOTES INSIDE HOUR / EVENTS ********************************/
.day-events{
  position: absolute;
  top: 26px;   /* moved down a bit */
  left: 4px;
  right: 4px;

  display: flex;
  flex-direction: column;
  gap: 4px;

  max-height: calc(100% - 30px);
  overflow: hidden;
}
.day-note{
  position:absolute;
  top:8px;
  left:8px;
  right:8px;
  background:#16a34a;
  color:white;
  font-size:12px;
  padding:6px 8px;
  border-radius:8px;
  text-align:center;
  cursor:pointer;
  box-shadow:0 4px 10px rgba(0,0,0,0.15);
  z-index:10;
}
.day-col{
  position: relative;
  display: flex;
  flex-direction: column;
  height: fit-content;  
}
.day-col .hour-line{
  height:60px;
  border-bottom:1px solid #f8fafc;
}
.day-col:nth-child(6){
  background: rgba(22,163,74,0.06);
  border-radius:12px;
}
.day-number {
  position: absolute;
  top: 4px;
  left: 6px;
  font-size: 12px;
  font-weight: 700;
  color: #111827;
  z-index: 5;
}
#dayEvents{
  max-height: 300px;
  overflow-y: auto;
  margin-top: 10px;
  padding-right: 5px;
}
.date-label{
  font-size: 12px;
  font-weight: 600;
  color: #ffffff;
  margin-bottom: 6px;
  letter-spacing: 0.5px;
}
.today-btn{
  background:#16a34a !important;
  color:white;
  font-weight:700;
  letter-spacing:0.5px;
}
#todayList, #pendingList{
  margin-top:6px;
  display:flex;
  flex-direction:column;
  gap:6px;
}

/******************************** LEGEND / Coffee Booth Matcha Booth Tattoo Event ********************************/
.legend-row{
  display:grid;
  grid-template-columns: 1fr 1fr;
  align-items:center;
  padding:4px 0;
}
.legend-header{
  font-weight:700;
  font-size:13px;
  color:#111827;
  margin-bottom:4px;
}
.legend-item{
  display:flex;
  align-items:center;
  gap:8px;
  font-size:13px;
}
.legend-panel{
  display:flex;
  flex-direction:column;
  gap:4px;
}
.legend-divider{
  height:1px;
  background:#e5e7eb;
  margin:8px 0;
}
.dot{
  width:9px;
  height:9px;
  border-radius:50%;
}
.yellow{ background:#facc15; }
.green{ background:#22c55e; }
.blue{ background:#3b82f6; }
.panel{
  background:white;
  border-radius:20px;
  padding:16px;
  border:1px solid #eef2f7;
  box-shadow: 0 6px 18px rgba(0,0,0,0.06);
  height: 100%;
  overflow: hidden;
  display:flex;
  flex-direction:column;
  gap:10px;
}
.panel h3{
  margin-bottom: 10px;
  color: #111827;
  font-size:15px;
  font-weight:700;
}

/******************************** TODAY SCHEDULE ********************************/
.side-box{
  margin-top:12px;
  padding:12px;
  border-radius:14px;
  border:1px solid #eef2f7;
  background:#f9fafb;
  box-shadow:0 4px 10px rgba(0,0,0,0.04);
}
.today-box{
  border-left:4px solid #16a34a;
}
.pending-box{
  border-left:4px solid #f59e0b;
}
.box-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:8px;
}
.box-header h4{
  font-size:13px;
  font-weight:700;
  color:#111827;
}
.badge{
  font-size:11px;
  background:#16a34a;
  color:white;
  padding:2px 8px;
  border-radius:999px;
}
.badge.pending{
  background:#f59e0b;
}
.box-list{
  display:flex;
  flex-direction:column;
  gap:6px;
  max-height: none;   
  overflow: visible;  
}
.box-list .item{
  background:white;
  border:1px solid #eee;
  border-radius:10px;
  padding:8px;
  font-size:12px;
  transition:0.2s;
}
.box-list .item:hover{
  transform:translateX(3px);
  border-color:#16a34a;
}

/******************************** EVENT BLOCK ********************************/
.event-chip{
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  font-size: 10px;
  padding: 3px 6px;
  border-radius: 999px;
}
.event-block {
  position:absolute;
  left:8px;
  right:8px;
  padding:8px 10px;
  color:#fff;
  display:flex;
  align-items:center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  transition:0.2s ease;
  border-radius:12px;
  font-size:12px;
  font-weight:600;
  padding-left:10px;
}

.event-block:hover{
  transform:scale(1.02);
}
.event-block::before {
  content: "";
  width: 4px;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  background: rgba(255,255,255,0.4);
}
@keyframes fadeUp{
  from{
    opacity:0;
    transform:translateY(5px);
  }
  to{
    opacity:1;
    transform:translateY(0);
  }
}

/******************************** ITEM ********************************/
.item{
  display:flex;
  justify-content:space-between;
  padding:12px 10px;
  margin-top:8px;
  transition:0.2s;
  border-radius:12px;
  font-size:13px;
  background:#f9fafb;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.item:hover{
  background:var(--primary-light);
}

/******************************** MODAL ********************************/
.modal {
  display:none;
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.5);
  justify-content:center;
  align-items:center;
  z-index: 9999; 
}
.modal[style*="flex"]{
  opacity:1;
  transform:scale(1);
}
.modal-content{
  background:white;
  padding:25px;
  border-radius:16px;
  width:320px;
  box-shadow:0 20px 40px rgba(0,0,0,0.2);
  position: relative;
  z-index: 10000;
}

.modal-content h3{
  margin-bottom:10px;
}
.modal-content input,
.modal-content select{
  width:100%;
  padding:10px;
  margin-top:10px;
  border-radius:8px;
  border:1px solid var(--border);
}
.month-center{
  display:none;
  flex-direction:column;
  align-items:center;
  gap:2px;   /* 🔥 reduced spacing */
}
.month-filters{
  display:flex;
  gap:8px;
  align-items:center;
  margin-top:2px;   /* 🔥 from 6px → 2px */
}
.month-filters select{
  font-size: 11px;
  font-weight: 600;

  padding: 4px 8px;
  border-radius: 8px;

  border: 1px solid #e5e7eb;
  background: #ffffff;

  color: #374151;

  cursor: pointer;

  transition: all 0.2s ease;
  outline: none;
}
.month-filters select:hover{
  border-color: #22c55e;
  box-shadow: 0 6px 14px rgba(34,197,94,0.15);
  transform: translateY(-1px);
}
.month-filters select:focus{
  border-color: #16a34a;
  box-shadow: 0 0 0 3px rgba(34,197,94,0.2);
}
#monthTitle{
  font-family: 'Plus Jakarta Sans', 'Segoe UI', Roboto, Arial, sans-serif;
  font-size: 13px;
  font-weight: 600; /* more professional than 800 */
  letter-spacing: 0.5px;
  color: #114500;
  text-transform: none; /* remove ALL CAPS effect */
  padding: 6px 14px;
  border-radius: 12px;
  background: linear-gradient(135deg, #ecfdf5, #dcfce7);
  border: 1px solid #bbf7d0;
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
  margin:0;
}
#dayEvents{
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 10px;
}
.schedule-row{
  display: flex;
  align-items: center;
  gap: 14px;
}
.schedule-time{
  min-width: 90px;
  text-align: right;
  font-size: 13px;
  color: #6b7280;
  font-weight: 600;
}
.schedule-card{
  flex: 1;
  background: #ffffff;
  border-radius: 14px;
  padding: 12px 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;

  box-shadow: 0 6px 16px rgba(0,0,0,0.06);
  transition: 0.25s ease;
}
.schedule-card:hover{
  transform: translateY(-3px);
  box-shadow: 0 10px 24px rgba(0,0,0,0.12);
}
.schedule-info{
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.schedule-title{
  font-size: 14px;
  font-weight: 700;
  color: #111827;
}
.schedule-status{
  font-size: 11px;
  padding: 4px 10px;
  border-radius: 999px;
  font-weight: 600;
  width: fit-content;
}
.schedule-status.accepted{
  background: #dcfce7;
  color: #166534;
}
.schedule-status.pending{
  background: #f59e0b;
  color: #ffffff;
}

.schedule-status.declined{
  background: #fee2e2;
  color: #991b1b;
}
.schedule-actions{
  display: flex;
  align-items: center;
}
.no-events{
  text-align: center;
  color: #9ca3af;
  font-size: 13px;
  padding: 20px;
}

.btn-green{
  background:#16a34a;
  color:white;
  border:none;
  padding:10px 14px;
  border-radius:10px;
  width:100%;
  margin-top:10px;
  cursor:pointer;
  font-weight:600;
  transition:0.2s;
}
.btn-green:hover{
  background:#15803d;
}
.btn-cancel{
  background: #fee2e2;
  color: #b91c1c;
  border: none;
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.2s;
}
.btn-cancel:hover{
  background: #ef4444;
  color: white;
  transform: scale(1.05);
}
.schedule-status.cancelled{
  background: #fee2e2;
  color: #b91c1c;
}

/******************************** EVENT TYPES COLORS ********************************/
.yellow { background:#facc15; }
.blue { background:#3b82f6; }
.purple { background:#a855f7; }

/******************************** STATUS COLORS ********************************/
.status-accepted{
  width:10px;
  height:10px;
  background:#22c55e; /* green */
  border-radius:50%;
  display:inline-block;
}

.status-pending{
  width:10px;
  height:10px;
  background:#f59e0b; /* orange */
  border-radius:50%;
  display:inline-block;
}

.status-cancelled{
  width:10px;
  height:10px;
  background:#ef4444; /* red */
  border-radius:50%;
  display:inline-block;
}
/******************************** CLEAN SPACING ********************************/
.legend-panel{
  display:flex;
  flex-direction:column;
  gap:6px;   /* was 10px → tighter */
}
.legend-panel h3{
  margin:6px 0 2px;   /* reduce top/bottom spacing */
  font-size:15px;
  font-weight:700;
  color:#111827;
}
.legend-item{
  margin:0;          /* remove extra spacing */
  padding:2px 0;     /* tighter vertical spacing */
  font-size:13px;
  display:flex;
  align-items:center;
  gap:8px;
}
.legend-divider{
  height:1px;
  background:#e5e7eb;
  margin:4px 0;   /* tighter */
}
.side-box{
  margin-top:4px;   /* closer together */
  padding:10px;     /* slightly reduced */
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
<div class="calendar-header">
  <div class="header-left">
    <h1>Calendar</h1>
    <p>Manage schedules, bookings, and daily events in real time</p>
  </div>

  <div class="header-right">
    <div class="date-box">
      <i class="fa-solid fa-calendar"></i>
      <span id="todayDate">Today: </span>
    </div>
  </div>
</div>

<!--------------------------------------- TOOLBAR ---------------------------------------------> 
<div class="layout">

<!--------------------------------------- MINI CALENDAR ---------------------------------------------> 
<div class="mini-calendar">

  <!-- ✨ NEW MODERN HEADER -->
<div class="calendar-nav">

  <!-- LEFT: Month navigation + current date -->
  <div class="nav-left">

    <button class="nav-btn" onclick="changeMonth(-1)">‹</button>

    <div class="current-date">
      <span id="currentFullDate"></span>
    </div>

    <button class="nav-btn" onclick="changeMonth(1)">›</button>

  </div>

  <!-- RIGHT: Title + Filters -->
  <div class="nav-right">

    <div class="month-filters">

      <select id="monthFilter" onchange="updateFromDropdowns()">
        <option value="">Month</option>
        <option value="0">Jan</option>
        <option value="1">Feb</option>
        <option value="2">Mar</option>
        <option value="3">Apr</option>
        <option value="4">May</option>
        <option value="5">Jun</option>
        <option value="6">Jul</option>
        <option value="7">Aug</option>
        <option value="8">Sep</option>
        <option value="9">Oct</option>
        <option value="10">Nov</option>
        <option value="11">Dec</option>
      </select>

      <select id="dayFilter" onchange="updateFromDropdowns()">
        <option value="">Day</option>
        <script>
          for(let i=1;i<=31;i++){
            document.write(`<option value="${i}">${i}</option>`);
          }
        </script>
      </select>

      <select id="yearFilter" onchange="updateFromDropdowns()">
        <option value="">Year</option>
      </select>

    </div>

  </div>

</div>
  <!-- ✨ WEEK DESIGN -->
  <div class="week-header">
    <div>SUN</div>
    <div>MON</div>
    <div>TUE</div>
    <div>WED</div>
    <div>THU</div>
    <div>FRI</div>
    <div>SAT</div>
  </div>

  <!-- 📅 CALENDAR GRID -->
  <div class="mini-grid" id="miniGrid"></div>

</div>

<!--------------------------------------- LEGEND / Event Package --------------------------------------------->
<div class="legend-panel">

  <!-- HEADER -->
  <div class="legend-row legend-header">
    <div>Event Types</div>
    <div>Status</div>
  </div>

  <!-- ROW 1 -->
  <div class="legend-row">
    <div class="legend-item">
      <span class="dot yellow"></span>
      Coffee Booth
    </div>

    <div class="legend-item">
      <span class="status-accepted"></span>
      Accepted
    </div>
  </div>

  <!-- ROW 2 -->
  <div class="legend-row">
    <div class="legend-item">
      <span class="dot blue"></span>
      Matcha Booth
    </div>

    <div class="legend-item">
      <span class="status-pending"></span>
      Pending
    </div>
  </div>

  <!-- ROW 3 -->
  <div class="legend-row">
    <div class="legend-item">
      <span class="dot purple"></span>
      Tattoo Event
    </div>

    <div class="legend-item">
      <span class="status-cancelled"></span>
      Cancelled
    </div>
  </div>

  <div class="legend-divider"></div>

  <!-- TODAY -->
  <div class="side-box today-box">
    <div class="box-header">
      <h4>Today</h4>
      <span class="badge" id="todayCount">0</span>
    </div>
    <div id="todayList" class="box-list"></div>
  </div>

  <!-- PENDING -->
  <div class="side-box pending-box">
    <div class="box-header">
      <h4>Pending</h4>
      <span class="badge pending" id="pendingCount">0</span>
    </div>
    <div id="pendingList" class="box-list"></div>
  </div>

</div>

<!--------------------------------------- MODAL --------------------------------------------->
<div class="modal" id="modal">
  <div class="modal-content">
    <h3>Add Event</h3>
    <input id="title" placeholder="Event">
    <input id="time" type="time">
    <select id="status">
      <option>Pending</option>
      <option>Accepted</option>
      <option>Declined</option>
      <option>Cancelled</option>
    </select>
    <button class="btn-green" onclick="add()">Save</button>
  </div>
</div>
<div class="modal" id="confirmModal">
  <div class="modal-content">
    <h3>Delete Event?</h3>
    <p>This action cannot be undone.</p>

    <button style="background:#ef4444;color:white;" onclick="confirmDelete()">Delete</button>
    <button onclick="closeConfirm()">Cancel</button>
  </div>
</div>

<!--------------------------------------- POP UP --------------------------------------------->
<div class="popup" id="popup"></div>

<div class="modal" id="dayModal">
  <div class="modal-content" style="width:400px;">
    <h3 id="dayTitle"></h3>
    <div id="dayEvents"></div>
  </div>
</div>

<script>

let editMode = false;
let editTarget = null;
let deleteTarget = null;
let date = new Date();
let selected = null;
let searchText = "";
let filterStatus = "All";
let filterYearValue = "";
let filterMonthValue = "";
let viewMode = "month";
let viewDate = new Date();

let events = JSON.parse(localStorage.getItem("events")) || [];

// 👇 ILAGAY DITO
let eventColors = {
  "Coffee Booth": "#facc15",
  "Matcha Booth": "#22c55e",
  "Tattoo Event": "#3b82f6"
};
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

/******************************** render() ********************************/
function render(){

  if(viewMode === "year"){
    renderYearView();
    return;
  }

  renderMini();
  renderSide();
}

function renderSide(){

  let today = new Date().toISOString().split("T")[0];

  let todayEvents = events.filter(e => e.date === today);
  let pendingEvents = events.filter(e => e.status === "Pending");

  document.getElementById("todayList").innerHTML =
    todayEvents.length
      ? todayEvents.map(e =>
          `<div class="item">${e.time} - ${e.title}</div>`
        ).join("")
      : "<div class='item'>No events today</div>";

  document.getElementById("pendingList").innerHTML =
    pendingEvents.length
      ? pendingEvents.map(e =>
          `<div class="item">${e.time} - ${e.title}</div>`
        ).join("")
      : "<div class='item'>No pending events</div>";
}

function add(){

  let newDate = selected || new Date().toISOString().split("T")[0];
  let newTime = document.getElementById("time").value;

  let title = document.getElementById("title").value;
  let status = document.getElementById("status").value;

  if(newTime.startsWith("10:")){
    alert("10:00 AM is not allowed");
    return;
  }

  if(!title || !newTime){
    alert("Please complete all fields");
    return;
  }

  // ❌ CHECK IF TIME ALREADY EXISTS IN SAME DAY
  let exists = events.find(e => 
    e.date === newDate && e.time === newTime
  );

  if(exists){
    alert("⚠️ This time slot already has a booking for this day!");
    return;
  }

  // ✏️ EDIT MODE
  if(editMode && editTarget){

    editTarget.title = title;
    editTarget.time = newTime;
    editTarget.status = status;

    editMode = false;
    editTarget = null;

  } 
  // ➕ ADD NEW EVENT
  else {

    events.push({
      date: newDate,
      title: title,
      time: newTime,
      status: status,
      notes: notes
    });
  }

  // 💾 SAVE + RELOAD UI
  save();
  closeModal();
  render();
}

function change(v){
  date.setDate(date.getDate() + (v * 7)); // ✅ 1 WEEK SHIFT
  render();
}
function miniChange(v){
  date.setDate(1);
  date.setMonth(date.getMonth() + v);

  selected = null; // reset selected day (optional)
  render();
}
function today(){
  date=new Date();
  render();
}

function search(v){
  searchText=v;
  render();
}

function filter(v){
  filterStatus=v;
  render();
}

function color(s){
  if(s==="Accepted") return "green";
  if(s==="Pending") return "orange";
  return "red";
}

render();

function save(){
  localStorage.setItem("events", JSON.stringify(events));
}

function deleteEvent(date,title){
  deleteTarget = {date,title};
  document.getElementById("confirmModal").style.display="flex";
}
function confirmDelete(){

  events = events.filter(e =>
    !(e.date === deleteTarget.date && e.title === deleteTarget.title)
  );

  save();
  closeConfirm();
  render();
}
function closeConfirm(){
  document.getElementById("confirmModal").style.display="none";
  deleteTarget = null;
}
function editEvent(date,title){

  editMode = true;

  editTarget = events.find(e =>
    e.date === date && e.title === title
  );

  document.getElementById("title").value = editTarget.title;
  document.getElementById("time").value = editTarget.time;
  document.getElementById("status").value = editTarget.status;

  openModal();
}

document.addEventListener("keydown", (e)=>{
  if(e.key === "Escape"){
    document.getElementById("modal").style.display="none";
    document.getElementById("popup").style.display="none";
  }
});

document.getElementById("modal").addEventListener("click", (e)=>{
  if(e.target.classList.contains("modal")){
    e.target.style.display="none";
  }
});

function renderMini(){

  const grid = document.getElementById("miniGrid");
  if(!grid) return;

  grid.innerHTML = "";

  // ✅ FINAL SOURCE OF TRUTH
  let y = viewDate.getFullYear();
  let m = viewDate.getMonth();

  let firstDay = new Date(y, m, 1).getDay();
  let daysInMonth = new Date(y, m + 1, 0).getDate();

  let totalCells = 42;

  for(let i = 0; i < totalCells; i++){

    let div = document.createElement("div");
    div.className = "mini-day";

    let dayNum = i - firstDay + 1;

    if(dayNum > 0 && dayNum <= daysInMonth){

      let dayStr = `${y}-${String(m+1).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;

      div.innerHTML = `<div class="day-number">${dayNum}</div>`;

      // EVENTS
      let dayEvents = events.filter(e => e.date === dayStr);

      if(dayEvents.length > 0){

        let container = document.createElement("div");
        container.className = "day-events";

        dayEvents.slice(0, 3).forEach(ev => {
          let chip = document.createElement("div");
          chip.className = "event-chip";
          chip.style.background = eventColors[ev.title] || "#16a34a";
          chip.innerText = ev.title;

          container.appendChild(chip);
        });

        div.appendChild(container);
      }

      // TODAY
      let todayStr = new Date().toISOString().split("T")[0];
      if(dayStr === todayStr){
        div.classList.add("mini-today");
      }

      //selected
      if(dayStr === selected){
        div.classList.add("mini-selected");
      }
      div.onclick = () => {

        let y = viewDate.getFullYear();
        let m = viewDate.getMonth();

        let fullDate = `${y}-${String(m+1).padStart(2,'0')}-${String(dayNum).padStart(2,'0')}`;

        openDayView(fullDate);
      };

    } else {
      div.style.visibility = "hidden";
    }

    grid.appendChild(div);
  }
}
function openDayView(date){

  let list = events.filter(e => e.date === date);

  let active = list.filter(e => e.status !== "Cancelled");
  let cancelled = list.filter(e => e.status === "Cancelled");

  document.getElementById("dayTitle").innerText = "Schedule - " + date;

  function renderItems(items){
    return items.map(e => {

      let color = eventColors[e.title] || "#16a34a";

      return `
        <div class="schedule-row">

          <div class="schedule-time">
            ${formatTime(e.time)}
          </div>

          <div class="schedule-card" style="border-left:5px solid ${color}">
            
            <div class="schedule-info">
              <div class="schedule-title">${e.title}</div>

              <div class="schedule-status ${e.status.toLowerCase()}">
                ${e.status === "Cancelled" ? "❌ " : ""}${e.status}
              </div>
            </div>

          </div>

        </div>
      `;
    }).join("");
  }

  document.getElementById("dayEvents").innerHTML = `
    
    <div style="font-size:13px;font-weight:700;color:#6b7280;margin-bottom:8px;">
      Accepted / Pending / Declined
    </div>

    ${active.length ? renderItems(active) : "<p class='no-events'>No active events</p>"}

    <div style="font-size:13px;font-weight:700;color:#b91c1c;margin:16px 0 8px;">
      Cancelled Events
    </div>

    ${cancelled.length ? renderItems(cancelled) : `
      <div class="schedule-row">

        <div class="schedule-time">
          11:00 AM
        </div>

        <div class="schedule-card" style="border-left:5px solid #3b82f6">

          <div class="schedule-info">
            <div class="schedule-title">Matcha Booth</div>

            <div class="schedule-status cancelled">
              ❌ Cancelled
            </div>
          </div>

        </div>

      </div>
    `}

  `;

  document.getElementById("dayModal").style.display = "flex";
  document.getElementById("dayModal").addEventListener("click", function(e){
  if (e.target === this) {
    this.style.display = "none";
  }
});
}
function formatTime(time){
  let [h, m] = time.split(":");
  h = parseInt(h);

  let suffix = h >= 12 ? "PM" : "AM";
  h = h % 12 || 12;

  return `${h}:${m} ${suffix}`;
}
/******************************** POS DATE DISPLAY ********************************/
function updatecalendarDate(){
  const el = document.getElementById("calendarDate");
  if(!el) return;

  const now = new Date();

  el.textContent = now.toLocaleDateString('en-US', {
    weekday: 'long',
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  });
}

window.addEventListener("DOMContentLoaded", () => {
  updatecalendarDate();
  setInterval(updatecalendarDate, 60000);
});

function changeMonth(v){

  let y = viewDate.getFullYear();
  let m = viewDate.getMonth() + v;

  // ✅ reset to 1 first
  viewDate = new Date(y, m, 1);

  syncSelectedDate();
  render();
}
function changeView(value){
  viewMode = value;
  render();
}
function syncSelectedDate(){

  let y = viewDate.getFullYear();
  let m = viewDate.getMonth();

  let d = selected ? new Date(selected).getDate() : 1;

  // ✅ clamp day (prevents Feb 30, etc.)
  let maxDay = new Date(y, m + 1, 0).getDate();
  d = Math.min(d, maxDay);

  viewDate = new Date(y, m, d);

  selected = viewDate.toISOString().split("T")[0];

  // ✅ ALSO SYNC DROPDOWNS
  document.getElementById("monthFilter").value = m;
  document.getElementById("dayFilter").value = d;
  document.getElementById("yearFilter").value = y;
}
function formatLocalDate(date){
  let y = date.getFullYear();
  let m = String(date.getMonth()+1).padStart(2,'0');
  let d = String(date.getDate()).padStart(2,'0');
  return `${y}-${m}-${d}`;
}
function updateFromDropdowns(){
  let y = document.getElementById("yearFilter").value;
  let m = document.getElementById("monthFilter").value;
  let d = document.getElementById("dayFilter").value;

  let now = new Date();

  y = y ? Number(y) : now.getFullYear();
  m = m ? Number(m) : now.getMonth();
  d = d ? Number(d) : now.getDate();

  let maxDay = new Date(y, m + 1, 0).getDate();
  if(d > maxDay) d = maxDay;

  viewDate = new Date(y, m, d);
  selected = formatLocalDate(viewDate);

  render();
}
window.addEventListener("DOMContentLoaded", () => {

  // ✅ GENERATE YEARS HERE
  const yearSelect = document.getElementById("yearFilter");
  yearSelect.innerHTML = '<option value="">Year</option>';

  let currentYear = new Date().getFullYear();

  for(let i = currentYear - 50; i <= currentYear + 50; i++){
    let option = document.createElement("option");
    option.value = i;
    option.textContent = i;
    yearSelect.appendChild(option);
  }

  // ✅ existing mo
  syncSelectedDate();
  render();
});

function generateRandomEventsForCurrentMonth(count = 10){

  let y = viewDate.getFullYear();
  let m = viewDate.getMonth();

  let daysInMonth = new Date(y, m + 1, 0).getDate();

  // ONLY YOUR EVENT PACKAGES
  let titles = [
    "Coffee Booth",
    "Matcha Booth",
    "Tattoo Event"
  ];

  let statuses = ["Pending", "Accepted", "Declined"];

  for(let i = 0; i < count; i++){

    let randomDay = Math.floor(Math.random() * daysInMonth) + 1;

    let dateStr = `${y}-${String(m+1).padStart(2,'0')}-${String(randomDay).padStart(2,'0')}`;

    let randomHour = Math.floor(Math.random() * 10) + 8; // 8AM–5PM
    let randomTime = `${String(randomHour).padStart(2,'0')}:00`;

    events.push({
      date: dateStr,
      title: titles[Math.floor(Math.random() * titles.length)],
      time: randomTime,
      status: statuses[Math.floor(Math.random() * statuses.length)]
    });
  }

  save();
  render();
}

function updateCurrentFullDate(){
  const el = document.getElementById("currentFullDate");
  if(!el) return;

  const now = new Date();

  el.textContent = now.toLocaleDateString('en-US', {
    month: 'long',
    day: 'numeric',
    year: 'numeric'
  });
}

window.addEventListener("DOMContentLoaded", () => {
  updateCurrentFullDate();
});
</script>

</body>
</html>
