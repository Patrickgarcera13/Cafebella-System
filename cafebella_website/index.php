<?php
// ✅ Connect to your shared database (correct path)
require __DIR__ . '/website_php/database.php';

// Get menu items from database
$menuItems = [];
try {
    $stmt = $pdo->query("SELECT * FROM menu_items ORDER BY category, name");
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching menu: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cafe Bella</title>

<link href="https://fonts.googleapis.com/css2?family=Domine:wght@400;600&display=swap" rel="stylesheet">

<style>

:root {
  --space-xs: 8px;
  --space-sm: 16px;
  --space-md: 24px;
  --space-lg: 40px;
  --space-xl: 64px;
}  
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Plus Jakarta Sans', 'Segoe UI', Roboto, Arial, sans-serif;
  background: #f2f2f2;
}

html {
  scroll-behavior: smooth;
}
/******************************** TOPBAR ********************************/
.topbar {
  background: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center; /* Sinisigurong vertical center ang lahat */
  padding: 0 60px;     /* Tinanggal ang top/bottom padding para fixed ang taas */
  height: 36px;        /* Selyadong taas ng topbar */

  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
}

.topbar-center {
  flex: 1;
  text-align: center;
  color: #114500;
  font-size: 13px;
  line-height: 36px; /* Pantay sa height ng topbar */
}

.topbar-right {
  display: flex;
  gap: 15px;
  align-items: center; /* Pantay ang mga icon at button sa gitna */
  height: 100%;
}

.topbar-right img {
  width: 18px;
  height: 18px; /* Fixed square size */
  display: block;
}

.book-link {
  color: #114500;
  font-weight: bold;
  text-decoration: underline;
  cursor: pointer;
  font-size: 13px;
}

.book-link:hover {
  color: #0a2f00;
}

/* GO TO ADMIN BUTTON STYLE (Eksaktong sukat at lapat) */
.admin-btn-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}

.admin-btn {
  display: inline-block;
  background-color: #114500;
  color: white;
  text-decoration: none;
  font-size: 12px;
  font-weight: bold;
  padding: 4px 14px; /* Mas pinitpit para magkasya sa maliit na topbar nang hindi sumasabog */
  border-radius: 20px;
  border: 1px solid #114500;
  transition: all 0.3s ease;
  margin-left: 5px;
  cursor: pointer;
  line-height: 1.2;
}

.admin-btn:hover {
  background-color: transparent;
  color: #114500;
  box-shadow: 0 2px 6px rgba(17, 69, 0, 0.2);
}

/******************************** NAVBAR ********************************/
.navbar {
  background: #114500;
  display: flex;
  align-items: center;
  padding: 0 60px;     /* Tinanggal ang top/bottom padding para kontrolado ang height */
  height: 80px;        /* Selyadong taas ng navbar - Siguraduhing 80px din sa menu.html niyo */

  position: fixed;
  top: 36px;           /* Sakto sa ilalim ng topbar */
  left: 0;
  width: 100%;
  z-index: 999;
}

.header-wrapper {
  position: sticky;
  top: 0;
  z-index: 999;
}

.nav-logo {
  display: flex;
  align-items: center;
  height: 100%;
}

.nav-logo img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  cursor: pointer;
  display: block;
}

.nav-menu {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 50px;
  list-style: none;
  height: 100%;
}

.nav-menu a {
  color: white;
  text-decoration: none;
  position: relative;
  padding-bottom: 5px;
  font-size: 15px;
}

.nav-menu a::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  width: 0%;
  height: 2px;
  background-color: white;
  transition: width 0.3s ease;
}

.nav-menu a.active::after,
.nav-menu a:hover::after {
  width: 100%;
}

.nav-search {
  display: flex;
  align-items: center;
  margin-left: auto;
  height: 100%;
}

.search-box {
  position: relative;
  width: 220px;
}

.search-box input {
  width: 100%;
  padding: 8px 12px 8px 35px;
  border-radius: 20px;
  border: 1px solid #ccc;
  outline: none;
  font-family: 'Domine', serif;
  font-size: 13px;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  pointer-events: none;
}
/******************************** HERO ********************************/
.hero {

  position: relative;
  height: 670px;
  overflow: hidden;
  margin-top: 0px;    /* 2️⃣ Ginawang 0 para magsimula siya mismo sa dulo ng natural flow */
  top: -4px;          /* 3️⃣ Gagamit tayo ng top offset para piliting idikit nang husto ang video sa navbar */
  display: block;

  padding: 0;

}
/******************************** SLIDE ARROW ********************************/
.slide {
  position: absolute;
  width: 100%;
  height: 100%;
  object-fit: cover;
  opacity: 0;
  transition: opacity 2.5s ease-in-out;
}

.slide.active {
  opacity: 1;
}

.caption {
  position: absolute;
  top: 58%; /* from 50% → pababa */
  left: 50%;
  transform: translate(-50%, -50%);
  max-width: 700px;
  text-align: center;
  color: white;
  z-index: 2;
  opacity: 0;
  transition: 0.5s ease;
}

.caption.show {
  opacity: 1;
}

.caption h1 {
  font-size: 48px;
  margin-bottom: 15px;
  margin-top: 15px; /* konting push down */
}

.caption p {
  font-size: 18px;
  margin-bottom: 25px;
  margin-top: 15px;
}

/******************************** HERO BOTTONS ********************************/
.hero-btn {
  padding: 12px 28px;
  background: #114500;
  color: white;
  text-decoration: none;
  border: 2px solid #114500;
  border-radius: 10px;
  display: inline-block;
  transition: all 0.3s ease;
  margin-top: calc(36px + 60px);
}

.hero-btn:hover {
  background: white;
  color: #114500;
  border-radius: 50px;
}

/******************************** DOTS ********************************/
.dots {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
}

.dot {
  width: 8px;
  height: 8px;
  background: rgba(255,255,255,0.5);
  border-radius: 50%;
  cursor: pointer;
}

.dot.active {
  background: white;
}

/******************************** RISE ANIMATION ********************************/

.rise-title,
.rise-desc,
.rise-item {
  opacity: 0;
  transform: translateY(40px);
  transition: opacity 0.8s ease, transform 0.8s cubic-bezier(0.22, 1, 0.36, 1);
}

.rise-title.show,
.rise-desc.show,
.rise-item.show {
  opacity: 1;
  transform: translateY(0);
}
/******************************** MENU ********************************/
.menu-section {
  padding: var(--space-xl) 20px;
  position: relative;
  overflow: hidden;
  isolation: isolate;
  background:
    radial-gradient(circle at top left, rgba(255, 252, 248, 0.95), transparent 55%),
    radial-gradient(circle at bottom right, rgba(210, 185, 160, 0.22), transparent 60%),
    linear-gradient(145deg, #fbf7f2, #f2ebe4);
}
.menu-section::before {
  content: "";
  position: absolute;
  inset: 0;

  background:
    radial-gradient(circle at 25% 20%, rgba(120, 80, 50, 0.10), transparent 40%),
    radial-gradient(circle at 75% 30%, rgba(160, 120, 90, 0.08), transparent 45%),
    radial-gradient(circle at 50% 90%, rgba(90, 60, 40, 0.06), transparent 50%);

  filter: blur(45px);
  z-index: 0;
  pointer-events: none;
}
.menu-section::after {
  content: "";
  position: absolute;
  inset: 0;

  background: url("https://www.transparenttextures.com/patterns/paper-fibers.png");
  opacity: 0.28;

  mix-blend-mode: multiply;
  z-index: 1;
  pointer-events: none;
}
.menu-section > * {
  position: relative;
  z-index: 2;
}
.menu-section h1 {
  color: #114500;
  font-size: 42px;
  margin-bottom: 15px;
  letter-spacing: 1px;
}
.menu-header {
  max-width: 700px;
  margin: 0 auto;
  text-align: center;
  max-width: 700px;
  margin: 0 auto var(--space-lg);
  text-align: center;
}
.menu-header h1 {
  font-size: 38px;
  color: #114500;
  margin-bottom: var(--space-sm);

}
.menu-caption {
  max-width: 650px;
  margin: auto;
  font-size: 15px;
  color: #444;
  line-height: 1.8;
  margin-bottom: var(--space-md);
}
.menu-track {
  display: flex;
  gap: 20px;
  transition: transform 0.5s ease;
  scroll-snap-type: x mandatory;
}
.menu-card {
  flex: 0 0 280px;
  border-radius: 22px;
  overflow: hidden;
  position: relative;
  background: #111;

  box-shadow:
    0 10px 30px rgba(0,0,0,0.15),
    0 2px 10px rgba(0,0,0,0.08);

  scroll-snap-align: start;
  aspect-ratio: 3 / 4;
}
.menu-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center top;

  transition: transform 0.7s ease;
}
.menu-card span {
  position: absolute;
  bottom: 18px;
  left: 18px;
  right: 18px;
  z-index: 3;

  color: #fff;
  font-size: 15px;
  font-weight: 500;
  letter-spacing: 0.5px;
  line-height: 1.4;

  text-shadow: 0 4px 15px rgba(0,0,0,0.7);
}
.menu-card::after {
  content: "";
  position: absolute;
  inset: 0;

  background: linear-gradient(
    to top,
    rgba(0,0,0,0.85) 10%,
    rgba(0,0,0,0.4) 45%,
    transparent 75%
  );

  z-index: 2;
}
.menu-btn {
  background: #114500;
  padding: 12px 30px;
  border-radius: 50px;
  font-weight: 600;
  letter-spacing: 1px;
  transition: 0.3s ease;
  margin-top: var(--space-sm);
}
.menu-btn:hover {
  background: #0a2f00;
  transform: scale(1.05);
}
.menu-badge {
  display: inline-block;
  padding: 6px 14px;
  background: #114500;
  color: white;
  font-size: 12px;
  letter-spacing: 2px;
  border-radius: 50px;
  margin-bottom: 12px;
}
.menu-slider-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  max-width: 100%; /* 👈 full width */
  padding: 10px 0; /* tanggal side padding */
  margin: var(--space-lg) auto 0;
}
.menu-slider {
  overflow: hidden;
  width: 100%;
  border-radius: 15px;
  padding: 10px 5px;
  position: relative;
}
/******************************** PREMIUM CTA BUTTON ********************************/

.premium-btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;

  padding: 14px 34px;
  border-radius: 50px;

  background: linear-gradient(135deg, #114500, #1f6b00);
  color: white;

  font-weight: 600;
  font-size: 14px;
  letter-spacing: 1px;

  text-decoration: none;

  box-shadow: 0 10px 25px rgba(17, 69, 0, 0.25);

  position: relative;
  overflow: hidden;

  transition: all 0.4s ease;
}

/* hover glow effect */
.premium-btn::before {
  content: "";
  position: absolute;
  top: 0;
  left: -120%;
  width: 120%;
  height: 100%;
  background: rgba(255,255,255,0.15);
  transform: skewX(-25deg);
  transition: 0.5s;
}

.premium-btn:hover::before {
  left: 120%;
}

.premium-btn:hover {
  transform: translateY(-3px) scale(1.05);
  box-shadow: 0 15px 35px rgba(17, 69, 0, 0.35);
}
.premium-btn:hover .btn-arrow {
  transform: translateX(5px);
}
.btn-arrow {
  font-size: 16px;
  transition: transform 0.3s ease;
}
/******************************** EVENT ********************************/

.event-section {
  position: relative;
  overflow: hidden;
  isolation: isolate;
  padding: var(--space-xl) 20px;
  background:
    radial-gradient(circle at top right, rgba(255, 250, 245, 0.9), transparent 60%),
    radial-gradient(circle at bottom left, rgba(140, 110, 80, 0.18), transparent 60%),
    linear-gradient(145deg, #f8f3ed, #efe7dd);
}
.event-header {
  max-width: 700px;
  margin: 0 auto;
  text-align: center;
  margin-bottom: var(--space-lg);
}
.event-header h2 {
  font-size: 38px;
  color: #114500;
  margin-bottom: 16px;
}
.event-header p {
  font-size: 15px;
  color: #444;
  line-height: 1.8;
  margin-bottom: var(--space-md);
}
.event-top {
  display: flex;
  align-items: center;
  justify-content: space-between; 
  margin-bottom: 30px;
}
.event-top img {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}
.event-badge {
  display: inline-block;
  padding: 6px 16px;
  background: #114500;
  color: white;
  font-size: 12px;
  letter-spacing: 2px;
  border-radius: 50px;
  margin-bottom: 12px;
}
.event-text {
  flex: 1;
  max-width: 600px;
  text-align: center;
}

.event-text h2 {
  color: #114500;
  font-size: 32px;
  max-width: 600px;
  text-align: center;
  margin-bottom: 20px;
}

.event-text p {
  font-size: 17px;
  margin-bottom: 15px;
  line-height: 1.5;
  color: #114500;
}

.event-btn {
  padding: 12px 28px;
  background: white;
  color: #114500;
  border: 2px solid #114500;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: var(--space-sm);
}

.event-btn:hover {
  background: #114500;
  color: white;
  border-radius: 20px;
  transform: translateY(-2px);
}
.event-cards {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  margin-top: 60px;
  margin: var(--space-lg) auto 0;
  max-width: 1100px;
  gap: var(--space-md);

}
.event-card {
  height: 280px;
  border-radius: 20px;
  position: relative;
  overflow: hidden;
  cursor: pointer;

  box-shadow: 0 15px 40px rgba(0,0,0,0.12);
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.event-card:nth-child(1){
  background-image: url('IMAGES/eventpic.jpg');
}
.event-card:nth-child(2){
  background-image: url('IMAGES/eventpic.jpg');
}
.event-card:nth-child(3){
  background-image: url('IMAGES/eventpic.jpg');
}
.event-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 30px 80px rgba(0,0,0,0.2);
}
.event-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transition: transform 0.5s ease;
}
.event-card:hover img {
  transform: scale(1.08);
}
.event-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.75), transparent);
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
  padding: 22px;
  color: white;
  text-align: left;
}
.event-overlay h3 {
  font-size: 18px;
  margin-bottom: 6px;
}

.event-overlay p {
  font-size: 13px;
  opacity: 0.9;
  margin-top: 2px;
}

/* responsive */
@media (max-width: 900px) {
  .event-cards {
    grid-template-columns: 1fr;
  }
}

/******************************** MODAL ********************************/
.modal {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.88);
  overflow-y: auto;
}
.modal-content {
  background: #ffffff;
  margin: 60px auto;
  padding: 35px;
  width: 92%;
  max-width: 1000px;
  border-radius: 22px;
  position: relative;
  animation: fadeUp 0.3s ease;
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.close {
  position: absolute;
  right: 20px;
  top: 15px;
  font-size: 30px;
  cursor: pointer;
  color: #114500;
}
.modal-header {
  text-align: center;
  margin-bottom: 22px;
}
.modal-header h2 {
  font-size: 30px;
  color: #114500;
  margin-bottom: 6px;
  font-weight: 600;
}
.modal-header p {
  font-size: 14px;
  color: #666;
  letter-spacing: 0.4px;
}
.modal-badge {
  display: inline-block;
  padding: 6px 14px;
  background: #114500;
  color: white;
  font-size: 11px;
  letter-spacing: 2px;
  border-radius: 50px;
  margin-bottom: 10px;
}

/******************************** MODAL DESCRIPTION ********************************/

.modal-description {
  margin-top: 18px;
  font-size: 13.5px;
  line-height: 1.7;
  color: #555;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

/* GRID */
.modal-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  margin-top: 20px;   /* 👉 push grid down */
  gap: 16px;          /* slight refinement (optional upgrade) */
}
.modal-item {
  border-radius: 14px;
  overflow: hidden;
  background: #f3f3f3;
}
.modal-item img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  display: block;
}
@media (max-width: 768px) {
  .modal-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 500px) {
  .modal-grid {
    grid-template-columns: 1fr;
  }
}

.collab-wrapper {
  margin-top: 18px;
  margin-bottom: 22px; /* 👉 important para humiwalay sa grid */
}

.collab-label {
  display: block;
  font-size: 12px;
  color: #999;
  letter-spacing: 1px;
  margin-bottom: 8px;
}

.collab-card {
  display: inline-flex;
  align-items: center;
  justify-content: space-between;

  gap: 14px;
  padding: 10px 14px;

  border-radius: 12px;
  border: 1px solid #e5e5e5;
  background: #fafafa;

  text-decoration: none;

  transition: all 0.25s ease;

  min-width: 260px;
}

.collab-card:hover {
  background: #114500;
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(17, 69, 0, 0.25);
}

.collab-text strong {
  display: block;
  font-size: 14px;
  color: #114500;
  transition: 0.25s;
}

.collab-text small {
  font-size: 12px;
  color: #777;
  transition: 0.25s;
}

/* hover color change */
.collab-card:hover .collab-text strong,
.collab-card:hover .collab-text small {
  color: white;
}

.collab-arrow {
  font-size: 16px;
  color: #114500;
  transition: 0.25s;
}

.collab-card:hover .collab-arrow {
  color: white;
  transform: translateX(4px);
}


.feedback-line{
  width: 80px;
  height: 2px;
  background: #114500;
  margin: 14px auto;
  opacity: 0.4;
}

.feedback-badge{
  display: inline-block;
  padding: 6px 14px;
  background: #114500;
  color: white;
  font-size: 11px;
  border-radius: 50px;
  letter-spacing: 1.2px;
  margin-bottom: 12px;
}
.feedback-container{
  max-width: 1100px;
  margin: auto;
}
.feedback-section{
  padding: 120px 20px;
  background: radial-gradient(circle at top, #ffffff 0%, #f5f5f5 100%);
  text-align: center;
  position: relative;
}
.feedback-top {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 200px;
  background:
    linear-gradient(to bottom, rgba(255,255,255,0) 0%, #f2f2f2 90%),
    url('IMAGES/feedback.png') top center/cover no-repeat;
  z-index: 0;
}
.feedback-tag{
  display: inline-block;
  padding: 6px 16px;
  font-size: 11px;
  letter-spacing: 2px;
  background: #114500;
  color: white;
  border-radius: 50px;
  margin-bottom: 14px;
}
.feedback-footer-info {
  margin-top: 35px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 18px;
}
/* ================= COFFEE BEAN ANIMATION LEFT AND RIGHT ================= */

@keyframes splashHover {
  0% {
    transform: scale(1) rotate(0deg) translateY(0);
  }
  40% {
    transform: scale(1.15) rotate(3deg) translateY(-10px);
  }
  70% {
    transform: scale(0.95) rotate(-2deg) translateY(5px);
  }
  100% {
    transform: scale(1.1) rotate(0deg) translateY(0);
  }
}

.feedback-side:hover {
  animation: splashHover 0.6s ease;
  filter: drop-shadow(0 15px 20px rgba(14, 97, 65, 0.25));
}
.feedback-sub {
  font-size: 14px;
  margin-bottom: 40px;
  position: relative;
  z-index: 2;
}
.feedback-cards{
  margin-top: 60px;
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 26px;
}
.feedback-card{
  background: rgba(255,255,255,0.9);
  border: 1px solid rgba(17,69,0,0.08);
  border-radius: 18px;
  padding: 26px;
  position: relative;

  box-shadow: 0 10px 25px rgba(0,0,0,0.05);

  transition: all 0.35s ease;
}
.feedback-card:hover{
  transform: translateY(-10px);
  box-shadow: 0 25px 60px rgba(0,0,0,0.12);
}
.feedback-card:hover::before{
  width: 100%;
}

.feedback-author {
  font-size: 12px;
  font-weight: bold;
}
.feedback-meta{
  margin-top: 18px;
  padding-top: 12px;
  border-top: 1px solid #eee;
}
.feedback-meta span{
  font-size: 12px;
  color: #777;
  letter-spacing: 0.4px;
}
.feedback-meta small{
  font-size: 11px;
  color: #999;
}

/******************************** TESTIMONIALS SECTION (PURE LIGHT THEME SLIDER) ********************************/

.testimonials-section {
  padding: var(--space-xl) 20px;
  background: #fdfbf7; /* Ang eksaktong mamahaling cream background mula sa image_cb22e3.jpg */
  position: relative;
}

.testimonials-container {
  max-width: 1100px;
  margin: auto;
}

/* --- HEADER PART --- */
.testimonials-header {
  text-align: center;
  margin-bottom: var(--space-lg);
}

.testimonials-badge {
  display: inline-block;
  padding: 6px 16px;
  background: rgba(27, 79, 36, 0.08); /* Napakagaan na green tint */
  color: #1b4f24; /* Ang iyong signature Cafe Bella Deep Green */
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 2px;
  border-radius: 50px;
  margin-bottom: 14px;
  text-transform: uppercase;
}

.testimonials-header h2 {
  font-size: 40px;
  color: #2b1b12; /* Ang orihinal mong warm dark brown/charcoal text color */
  font-weight: 700;
  margin-bottom: 12px;
}

.testimonials-header p {
  font-size: 14px;
  color: #6b5b4d; /* Muted brown base text */
  max-width: 600px;
  margin: auto;
  line-height: 1.7;
}

/* --- SLIDER STRUCTURE (Patterned after Screenshot 2026-06-26 173928.png) --- */
.testimonial-slider-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  max-width: 920px;
  margin: 40px auto 0 auto;
  position: relative;
  gap: 24px;
}

/* Ang mismong Card Structure ng pangalawang screenshot pero nakakulay White at Cream */
.modern-testimonial-card {
  background: #ffffff; /* Purong puti para lumutang sa iyong cream section background */
  border: 1px solid rgba(27, 79, 36, 0.15); /* Ang banayad mong berdeng border outline */
  border-radius: 14px;
  padding: 50px 60px;
  width: 100%;
  position: relative;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02); /* Napaka-subtle na shadow para malinis */
  text-align: left;
}

/* Malaking quote icon watermark sa itaas na sulok */
.quote-icon {
  position: absolute;
  top: 35px;
  right: 50px;
  font-size: 85px;
  font-family: 'Georgia', serif;
  color: rgba(27, 79, 36, 0.05); /* Sobrang transparent na brand green watermark effect */
  line-height: 1;
  pointer-events: none;
  font-weight: bold;
}

/* Platform Metadata (Facebook/Google Review row) */
.card-meta {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 18px;
}

.platform-icon {
  width: 18px;
  height: 18px;
  object-fit: contain;
}

.platform-text {
  font-size: 0.85rem;
  color: #6b5b4d;
  font-weight: 500;
}

/* Five Stars */
.modern-testimonial-card .stars {
  color: #ff9800; /* Rich gold stars gaya ng nasa parehong references */
  font-size: 1.25rem;
  margin-bottom: 25px;
}

/* Ang Italicized Main Review Text */
.modern-testimonial-card .testimonial-text {
  font-size: 1.45rem;
  font-style: italic;
  font-family: 'Georgia', serif;
  color: #2b1b12; /* Swak sa iyong font color scheme, kitang-kita at hindi black */
  line-height: 1.6;
  margin-bottom: 30px;
  letter-spacing: 0.2px;
}

/* Reviewer Data sa Ibaba */
.reviewer-info .name {
  font-size: 1.15rem;
  color: #1b4f24; /* Cafe Bella Premium Deep Green */
  margin: 0 0 4px 0;
  font-weight: 600;
}

.reviewer-info .date {
  color: #a0968f; /* Muted warm grey-brown para sa date */
  font-size: 0.85rem;
}

/* --- NAVIGATION BUTTONS (Circular side arrows from layout) --- */
.slider-arrow {
  background: #ffffff;
  border: 1px solid rgba(27, 79, 36, 0.25); /* Eleganteng berdeng outline ng bilog */
  color: #1b4f24; /* Green arrow head */
  width: 48px;
  height: 48px;
  border-radius: 50%;
  font-size: 1.3rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  flex-shrink: 0;
}

.slider-arrow:hover {
  background: #1b4f24; /* Nagiging full solid green kapag tinapatan ng mouse */
  color: #ffffff; /* Puti ang nagiging arrow icon sa loob kapag naka-hover */
  border-color: #1b4f24;
  transform: scale(1.05);
}

/* --- DOTS INDICATORS (Hahaba ang active dot katulad ng nasa larawan) --- */
.testimonial-dots {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-top: 30px;
}

.t-dot {
  width: 7px;
  height: 7px;
  background: #dcd7cf; /* Banayad na kulay-cream/grey para sa inactive dots */
  border-radius: 50%;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.t-dot.active {
  background: #1b4f24; /* Magiging mahabang Deep Green pill ang active slide indicator */
  width: 26px;
  border-radius: 4px;
}
/******************************** LOCATION ********************************/

.location-section {
  position: relative;
  padding: 80px 0;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  margin: 80px auto;
  margin-top: 80px;
}

.location-bg {
  position: absolute;
  width: 100%;
  height: 100%;
  background: url('IMAGES/location.jpg') center/cover no-repeat;
  filter: blur(12px) brightness(0.8);
  z-index: 0;
}

.location-container {
  position: relative;
  z-index: 2;
  background: rgba(255,255,255,0.85);
  backdrop-filter: blur(6px);
  padding: 30px;
  display: block;
  text-align: center;
  width: 90%;
  max-width: 1000px;
  border-radius: 10px;
  margin: auto;
}

.location-left {
  flex: 1;
}

.location-img img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  margin-bottom: 10px;
  border-radius: 6px;
}
.location-text {
  color: #114500;
  text-align: center; /* clean and balanced */
  padding: 10px 15px;
}
.location-text h2 {
  font-size: 28px;
  font-weight: 600;
  margin-bottom: 18px;
  letter-spacing: 0.5px;
}

/******************************** LOC ADDRESS ********************************/
.location-info {
  border: 1.5px solid rgba(17, 69, 0, 0.2);
  padding: 20px 18px;
  border-radius: 10px;
  font-size: 14.5px;
  background: rgba(255,255,255,0.7);
  display: flex;
  flex-direction: column;
  gap: 8px; /* 🔥 consistent vertical spacing */
}
.location-info p {
  margin: 0;
  line-height: 1.6;
  color: #2b2b2b;
}
.location-info p:nth-child(-n+3) {
  font-size: 14.5px;
}
.location-info p:last-child {
  margin-top: 10px;
  padding-top: 10px;
  border-top: 1px solid rgba(17, 69, 0, 0.15);
  font-weight: 500;
  color: #114500;
}
/******************************** CAFE BELLA GUGUL MAP ********************************/
.location-map {
  flex: 1;
}

.location-map iframe {
  width: 100%;
  height: 100%;
  min-height: 300px;
  border: 2px solid #114500;
  border-radius: 6px;
}

/******************************** MATCHA ROTATING ********************************/
.rotating-green-tea {
  width: 180px;        
  height: 180px;          
  animation: spin 6s linear infinite; /* PARA UMIKOT NG TULOY TULOY */
  display: block;
  margin: 40px auto;      
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/******************************** FAQ ********************************/

.faq-section {
  background: #114500;
  color: white;
  text-align: center;
  padding: 80px 20px;
  margin-top: 80px;
}
.faq-section h2 {
  font-size: 36px;
  margin-bottom: 10px;
}
.faq-sub {
  font-size: 14px;
  margin-bottom: 30px;
}


/* FAQ BOX */
.faq-container {
  max-width: 700px;
  margin: auto;
}

.faq-item {
  border: 2px solid white;
  border-radius: 30px;
  margin-bottom: 15px;
  overflow: hidden;
  cursor: pointer;
}

.faq-question {
  padding: 12px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.faq-answer {
  max-height: 0;
  overflow: hidden;
  background: white;
  color: #114500;
  transition: max-height 0.4s ease;
  padding: 0 20px;
}

.faq-answer p {
  padding: 10px 0;
  font-size: 14px;
}

.faq-item.active .faq-answer {
  max-height: 200px;
}

.faq-contact a.contact-us-btn {
  display: inline-block;
  margin-top: 15px;
  padding: 12px 30px;          
  border-radius: 25px;          
  border: 2px solid #277d0a;   
  background-color: white;      
  color: #277d0a;              
  font-weight: bold;             
  font-size: 16px;               
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease;    
}

.faq-contact a.contact-us-btn:hover {
  background-color: #277d0a;  
  color: white;               
  transform: scale(1.05);     
}
/******************************** FOOTBAR ********************************/
.footbar {
  background: white;
  border-top: 2px solid #114500;
  border-bottom: 2px solid #114500;
  text-align: center;
  padding: 15px 20px;
  font-family: 'Domine', serif;
  color: #114500;
  font-size: 14px;
  margin-top: 40px;
}
/******************************** FOOTER ********************************/
.footer {
  background: #eee;
  padding: 40px 60px;
  margin-top: 80px;
}

.footer-top {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
}

.footer-logo img {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;    
}

.footer-col {
  font-size: 13px;
}

.footer-col h4 {
  margin-bottom: 10px;
  color: #114500;
}

.footer-col p,
.footer-col a {
  display: block;
  color: #114500;
  text-decoration: none;
  margin-bottom: 5px;
}

.footer-bottom {
  text-align: center;
  margin-top: 20px;
  color: #114500;
}

/******************************** GCASH LOGO ********************************/
.gcash img {
  width: 40px;
  height: 40px;         
  border-radius: 50%;    
  object-fit: cover;      
}

</style>
</head>

<body>

<!-------------------------------- TOPBAR ------------------------------------->
<div class="header-wrapper">
  <div class="topbar">
    <div class="topbar-center">
      Planning an event? <a href="Package.html" class="book-link">Book Now</a> and reserve your date with Cafe Bella.
    </div>
<div class="topbar-right">
  <a href="https://www.facebook.com/share/1CiwKkiCkY/" target="_blank" rel="noopener">
    <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook">
  </a>

  <a href="https://www.tiktok.com/@christiandavidangelo?_r=1&_t=ZS-94tpPJhFWzZ" target="_blank" rel="noopener">
    <img src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png" alt="TikTok">
  </a>
</div>
  </div>

<!-------------------------------- NAVBAR ------------------------------------->
<div class="navbar">

  <div class="nav-logo">
    <a href="index.php">
      <img src="IMAGES/Cafebella.jpg" alt="Cafe Bella Logo">
    </a>
  </div>

  <ul class="nav-menu">
    <li><a href="index.php" class="active">Home</a></li>
    <li><a href="Menu.php">Menu</a></li>
    <li><a href="Package.php">Packages</a></li>
    <li><a href="Location.html">Location</a></li>
    <li><a href="FAQ.html">FAQs</a></li>
  </ul>

  <div class="nav-search">
    <div class="search-box">
      <input type="text" id="searchInput" placeholder="Search menu..." onkeyup="showSuggestions()">
      <span class="search-icon">🔍</span>
    </div>
    <div id="suggestions" class="suggestions-box"></div>
  </div>

</div>

<!-------------------------------- HERO ------------------------------------->
<div class="hero rise-item" id="hero">
  <video class="slide active" muted autoplay playsinline>
    <source src="IMAGES/Cafebellavideo.mp4" type="video/mp4">
  </video>
  <img src="IMAGES/firstslide.png" class="slide" alt="">
  <img src="IMAGES/secondslide.jpg" class="slide" alt="">
  <img src="IMAGES/thirdslide.jpg" class="slide" alt="">
  <img src="IMAGES/fourthslide.jpg" class="slide" alt="">

  <div class="caption" id="captionBox">
    <h1 id="captionTitle"></h1>
    <p id="captionText"></p>
    <a href="#" class="hero-btn" id="captionBtn">View our menu</a>
  </div>

  <div class="dots">
    <span class="dot active"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
  </div>
</div>

<!-------------------------------- MENU ------------------------------------->
<div id="menu" class="menu-section rise-item">
  <div class="menu-header">
    <span class="menu-badge rise-title">OUR SIGNATURE MENU</span>
    <h1 class="rise-title">Crafted with Passion, Served with Style</h1>
    <p class="menu-caption rise-desc">
      At <strong>Cafe BELLA</strong>, every dish and drink is thoughtfully prepared using quality ingredients.
      From comforting rice meals to refreshing matcha and coffee creations — every bite is designed to elevate your experience.
    </p>
    <a href="Menu.php#menu-categories" class="menu-btn premium-btn rise-item">
      Explore Full Menu
      <span class="btn-arrow">→</span>
    </a>
  </div>

<div class="menu-slider-wrapper rise-item">
  <div class="menu-slider">
    <div class="menu-track" id="menuTrack">

<?php
// ✅ Dynamically load menu items from database
if (!empty($menuItems)) {
    foreach ($menuItems as $row) {
        // Use correct image path from database (already IMAGES/MENU/...)
        $imageSrc = htmlspecialchars($row['image']);
?>
      <div class="menu-card">
          <img src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
          <span><?php echo htmlspecialchars($row['name']); ?></span>
      </div>
<?php
    }
} else {
    echo '<p style="padding: 20px; text-align: center; color: #666;">No menu items available yet.</p>';
}
?>

    </div>
  </div>
</div>
</div>

<!-------------------------------- EVENT PACKAGES ------------------------------------->
<div class="event-section rise-item">
  <div class="event-header">
    <span class="event-badge">EVENT PACKAGES</span>
    <h2 class="rise-title">Make Your Celebrations Truly Special</h2>
    <p class="rise-desc">
      Explore our curated event packages designed to create unforgettable experiences.
      From intimate gatherings to large celebrations, we’ve got you covered with premium setups.
    </p>
    <button class="event-btn premium-btn" onclick="window.location.href='Package.html'">
      View Our Packages <span class="btn-arrow">→</span>
    </button>
  </div>

  <div class="event-cards">
    <div class="event-card" onclick="openModal('coffeeModal')">
      <img src="IMAGES/Coffeebooth.jpg" alt="Coffee Booth">
      <div class="event-overlay">
        <h3>Coffee Booth</h3>
        <p>Perfect for corporate events & weddings</p>
      </div>
    </div>
    <div class="event-card" onclick="openModal('matchaModal')">
      <img src="IMAGES/Matchabooth.jpg" alt="Matcha Booth">
      <div class="event-overlay">
        <h3>Matcha Booth</h3>
        <p>Unique aesthetic drinks experience</p>
      </div>
    </div>
    <div class="event-card" onclick="openModal('tattooModal')">
      <img src="IMAGES/Tattoobooth.jpg" alt="Tattoo Booth">
      <div class="event-overlay">
        <h3>Tattoo Booth</h3>
        <p>Fun interactive guest experience</p>
      </div>
    </div>
  </div>
</div>

<!-------------------------------- TESTIMONIALS ------------------------------------->
<div class="testimonials-section rise-item">
  <div class="testimonials-container">
    <div class="testimonials-header">
      <span class="testimonials-badge">TESTIMONIALS</span>
      <h2>What Our Customers Say</h2>
      <p>Real experiences shared by Cafe Bella customers from our official Facebook page</p>
    </div>

    <div class="testimonial-slider-wrapper">
      <button class="slider-arrow prev">‹</button>
      <div class="modern-testimonial-card">
        <div class="quote-icon">“</div>
        <div class="card-meta">
          <span class="platform-text">Facebook Review</span>
        </div>
        <div class="stars">★★★★★</div>
        <div class="testimonial-text" id="testimonialQuote">
          "Loading feedbacks..."
        </div>
        <div class="reviewer-info">
          <h4 class="name" id="reviewerName">-</h4>
          <span class="date" id="reviewDate">-</span>
        </div>
      </div>
      <button class="slider-arrow next">›</button>
    </div>
    <div class="testimonial-dots" id="testimonialDots"></div>
  </div>
</div>

<!-------------------------------- LOCATION ------------------------------------->
<div class="location-section rise-item" id="location">
  <div class="location-bg rise-item"></div>
  <div class="location-container rise-item">
    <div class="location-left rise-item">
      <div class="location-img rise-item">
        <img src="IMAGES/location.jpg" alt="Location">
      </div>
      <div class="location-text rise-item">
        <h2 rise-desc>Visit Cafe BELLA</h2>
        <div class="location-info rise-item">
          <p rise-desc>Address : BLK 12 Lot 13 Almond</p>
          <p rise-desc>Drive Phase 4 Soldiers Hills 4,</p>
          <p rise-desc>Bacoor Philippines 4102</p>
          <p rise-desc>Mobile : 0905671816</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-------------------------------- MATCHA ROTATING ------------------------------------->
<div style="text-align:center; margin-top:40px;">
  <img src="IMAGES/greentea.png" class="rotating-green-tea rise-item" alt="Green Tea">
</div>

<!-------------------------------- FAQ SECTION ------------------------------------->
<div class="faq-section rise-item">
  <h2 rise-desc>Frequently Asked Questions</h2>
  <div class="faq-sub rise-item">
    Everything you need to know about our event booking services
  </div>
  <div class="faq-container rise-item">
    <div class="faq-item rise-item">
      <div class="faq-question rise-item">
        How early should I book and is there a reservation fee?
        <span>▼</span>
      </div>
      <div class="faq-answer rise-item">
        <p rise-desc>
          Bookings must be made at least one (1) week in advance to ensure availability and proper preparation for your event.<br>
          A ₱2,000 reservation fee is required to secure and confirm your booking.
        </p>
      </div>
    </div>
    <div class="faq-item rise-item">
      <div class="faq-question rise-item">
        What payment methods do you accept, and when should the balance be settled?
        <span>▼</span>
      </div>
      <div class="faq-answer rise-item">
        <p rise-desc>
          We accept payments via GCash and Cash.<br>
          The remaining balance must be fully settled on or before the event date.
        </p>
      </div>
    </div>
    <div class="faq-item rise-item">
      <div class="faq-question rise-item">
        Can I cancel my booking or request a refund for the reservation fee?
        <span>▼</span>
      </div>
      <div class="faq-answer rise-item">
        <p rise-desc>
          Cancellation and refund policies depend on the type of package and preparation stage of your booking.<br>
          Please contact our team directly so we can assist you with your specific concern and available options.
        </p>
      </div>
    </div>
  </div>
  <div class="faq-contact rise-item">
    <p class="rise-desc">Still have questions?</p>
    <a href="https://www.facebook.com/share/1CiwKkiCkY/" 
       class="contact-us-btn" 
       target="_blank" 
       rel="noopener noreferrer">
       Contact Our Team
    </a>
  </div>
</div>

<!-------------------------------- FOOTBAR ------------------------------------->
<div class="footbar rise-item">
  Kape lang saglit tapos laban ulit
</div>

<!-------------------------------- FOOTER ------------------------------------->
<div class="footer rise-item">
  <div class="footer-top rise-item">
    <div class="footer-logo rise-item">
      <a href="index.php">
        <img src="IMAGES/Cafebella.jpg" alt="Cafe Bella Logo">
      </a>
    </div>
    <div class="footer-col rise-item">
      <h4>Quick Links</h4>
      <a href="index.php">Home</a>
      <a href="Menu.php">Menu</a>
      <a href="Package.php">Event Packages</a>
      <a href="feedback.html">Feedback</a>
      <a href="Location.html">Location</a>
      <a href="FAQ.html">FAQ's</a>
    </div>
    <div class="footer-col rise-item">
      <h4>Services</h4>
      <a href="Package.php">Coffee Booth</a>
      <a href="Package.php">Matcha Booth</a>
      <a href="Package.php">Tattoo Event</a>
    </div>
    <div class="footer-col rise-item">
      <h4>About</h4>
      <a href="https://www.facebook.com/share/1CiwKkiCkY/" 
         target="_blank" 
         rel="noopener noreferrer">
         Contact Our Team
      </a>
    </div>
    <div class="footer-col rise-item">
      <h4>Socials</h4>
      <a href="https://www.facebook.com/share/1CiwKkiCkY/" target="_blank" rel="noopener">Facebook</a>
      <a href="https://www.tiktok.com/@christiandavidangelo?_r=1&_t=ZS-94tpPJhFWzZ" target="_blank" rel="noopener">Tiktok</a>
    </div>
    <div class="footer-col gcash rise-item">
      <h4>We accept</h4>
      <img src="IMAGES/GCash.png" alt="GCash">
    </div>
  </div>
  <div class="footer-bottom rise-item">
    © 2026 Cafe BELLA. All rights reserved.
  </div>
</div>

<!-- MODALS -->
<div id="coffeeModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('coffeeModal')">&times;</span>
    <div class="modal-header">
      <div class="modal-badge">COFFEE BOOTH</div>
      <h2>Coffee Booth Gallery</h2>
      <p>Corporate events • Weddings • Special occasions</p>
      <div class="modal-description">
        Enjoy a premium mobile café experience with professional barista service, serving expertly crafted espresso and specialty beverages. Includes two skilled baristas accommodating up to 50 guests, with a selection of four flavors from both coffee and non-coffee options, all served in 16 oz cups for a complete café-style experience.
      </div>
    </div>
    <div class="modal-grid">
      <div class="modal-item"><img src="IMAGES/Coffeebooth1.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Coffeebooth2.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Coffeebooth3.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Coffeebooth4.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Coffeebooth5.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Coffeebooth6.jpg" alt=""></div>
    </div>
  </div>
</div>

<div id="matchaModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('matchaModal')">&times;</span>
    <div class="modal-header">
      <div class="modal-badge">MATCHA BOOTH</div>
      <h2>Matcha Booth Gallery</h2>
      <p>Japanese-inspired • Aesthetic drinks • Wellness experience</p>
      <div class="modal-description">
        Experience an authentic Japanese matcha booth featuring ceremonial-grade matcha, specialty lattes, and wellness-focused beverages. Includes ceremonial matcha with Oatside milk, two skilled baristas serving up to 50 cups, with a choice of three flavors, all served in 16 oz cups for a refined and refreshing experience.
      </div>
    </div>
    <div class="modal-grid">
      <div class="modal-item"><img src="IMAGES/Matchabooth1.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Matchabooth2.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Matchabooth3.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Matchabooth4.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Matchabooth5.jpg" alt=""></div>
      <div class="modal-item"><img src="IMAGES/Matchabooth6.jpg" alt=""></div>
    </div>
  </div>
</div>

<div id="tattooModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal('tattooModal')">&times;</span>
    <div class="modal-header">
      <div class="modal-badge">TATTOO BOOTH</div>
      <h2>Tattoo Booth Gallery</h2>
      <p>Interactive • Creative • Guest experience</p>
      <div class="modal-description">
        Enjoy a fun and interactive temporary tattoo station in collaboration with a professional tattoo artist, offering a curated selection of minimalist designs using safe, skin-friendly materials. Guests can avail of two minimalist tattoos for ₱1,000, complete with a complimentary 16 oz drink for a unique and memorable experience.
      </div>
      <div class="collab-wrapper">
        <span class="collab-label">In collaboration with</span>
        <a href="https://www.facebook.com/share/1FJCRH37Rs/" target="_blank" class="collab-card">
          <div class="collab-text">
            <strong>Gene Brando Tattooist</strong>
            <small>Professional Tattoo Artist</small>
          </div>
          <div class="collab-arrow">→</div>
        </a>
      </div>
      <div class="modal-grid">
        <div class="modal-item"><img src="IMAGES/Tattoobooth1.jpg" alt=""></div>
        <div class="modal-item"><img src="IMAGES/Tattoobooth2.jpg" alt=""></div>
        <div class="modal-item"><img src="IMAGES/Tattoobooth3.jpg" alt=""></div>
        <div class="modal-item"><img src="IMAGES/Tattoobooth4.jpg" alt=""></div>
        <div class="modal-item"><img src="IMAGES/Tattoobooth5.jpg" alt=""></div>
        <div class="modal-item"><img src="IMAGES/Tattoobooth6.jpg" alt=""></div>
      </div>
    </div>
  </div>
</div>

</body>
</html>

<script>
// ALL YOUR EXISTING SCRIPT REMAINS — I ONLY FIXED THE LINKS & REMOVED BROKEN FETCH CALLS
const riseElements = document.querySelectorAll(".rise-title, .rise-desc, .rise-item, .event-card");

function handleRise() {
  const triggerPoint = window.innerHeight * 0.85;
  riseElements.forEach(el => {
    const elementTop = el.getBoundingClientRect().top;
    el.classList.toggle("show", elementTop < triggerPoint);
  });
}
window.addEventListener("scroll", handleRise);
window.addEventListener("load", handleRise);

// HERO SLIDER
let slides = document.querySelectorAll(".slide");
let dots = document.querySelectorAll(".dot");
let captionTitle = document.getElementById("captionTitle");
let captionText = document.getElementById("captionText");
let captionBtn = document.getElementById("captionBtn");
let captionBox = document.getElementById("captionBox");
let index = 0;
let autoSlide = null;

let data = [
  { title: "", text: "", btn: "View our menu", link: "#menu" },
  { title: "Cafe BELLA", text: "Cafe BELLA aims to bring unique and memorable experiences to every event.", btn: "View Menu", link: "Menu.php" },
  { title: "Our Packages", text: "Cafe BELLA offers curated event packages designed for memorable celebrations.", btn: "Book Now", link: "php" },
  { title: "Our Space", text: "Step into a cozy space inspired by classic retro coffee shop vibes.", btn: "Visit Us", link: "Location.html" }
];

function showSlide(i) {
  slides.forEach(s => {
    s.classList.remove("active");
    if (s.tagName === "VIDEO") { s.pause(); s.currentTime = 0; }
  });
  dots.forEach(d => d.classList.remove("active"));

  if(slides[i]) slides[i].classList.add("active");
  if(dots[i]) dots[i].classList.add("active");

  if (captionTitle) captionTitle.textContent = data[i].title;
  if (captionText) captionText.textContent = data[i].text;
  if (captionBtn) { captionBtn.textContent = data[i].btn; captionBtn.href = data[i].link; }
  if (captionBox) captionBox.classList.toggle("show", data[i].text !== "");

  if (autoSlide) clearTimeout(autoSlide);
  if (slides[i] && slides[i].tagName === "VIDEO") {
    slides[i].play();
    slides[i].onended = nextSlide;
  } else {
    autoSlide = setTimeout(nextSlide, 5000);
  }
}
function nextSlide() { index = (index + 1) % slides.length; showSlide(index); }
dots.forEach((dot, i) => dot.addEventListener("click", () => { index = i; showSlide(index); }));

// MENU SLIDER SCROLL
let autoScroll;
function startAutoScroll() {
  const slider = document.querySelector(".menu-slider");
  if (!slider) return;
  autoScroll = setInterval(() => {
    slider.scrollLeft += 1;
    if (slider.scrollLeft >= slider.scrollWidth / 2) slider.scrollLeft = 0;
  }, 20);
}
function stopAutoScroll() { clearInterval(autoScroll); }
const menuSlider = document.querySelector(".menu-slider");
if (menuSlider) {
  menuSlider.addEventListener("mouseenter", stopAutoScroll);
  menuSlider.addEventListener("mouseleave", startAutoScroll);
}

// FAQ
document.querySelectorAll(".faq-item").forEach(faq => {
  faq.addEventListener("click", () => faq.classList.toggle("active"));
});

// MODALS
function openModal(id) { const m = document.getElementById(id); if(m){m.style.display="block"; document.body.classList.add("modal-open");} }
function closeModal(id) { const m = document.getElementById(id); if(m){m.style.display="none"; if(!document.querySelector(".modal[style*='block']")) document.body.classList.remove("modal-open");} }
window.addEventListener("click", e => { if(e.target.classList.contains("modal")) closeModal(e.target.id); });

// TESTIMONIALS
let testimonialData = [
  { text: '"Hello po, my feedback sa all-meat sandwich was a solid 10/10! Super sarap and definitely worth the price."', name: "Verified Facebook Customer", type: "Facebook Review", rating: 5, date: "June 2026" },
  { text: '"Yung fried rice very flavorful masarap sya kaya lagi namin inoorder then yung bacsilog nagustuhan ng kids."', name: "Verified Facebook Customer", type: "Facebook Review", rating: 5, date: "May 2026" },
  { text: '"I think I\'ve finally found my favorite cafe. Your servings are really generous, and it still fits the budget!"', name: "Verified Facebook Customer", type: "Facebook Review", rating: 5, date: "April 2026" }
];
let currentTIndex = 0;

function updateTestimonialCard(i) {
  const q = document.getElementById('testimonialQuote');
  const n = document.getElementById('reviewerName');
  const d = document.getElementById('reviewDate');
  const s = document.querySelector('.modern-testimonial-card .stars');
  const p = document.querySelector('.platform-text');
  if(q) q.innerText = testimonialData[i].text;
  if(n) n.innerText = testimonialData[i].name;
  if(d) d.innerText = testimonialData[i].date;
  if(p) p.innerText = testimonialData[i].type;
  if(s) s.innerText = "★".repeat(testimonialData[i].rating) + "☆".repeat(5 - testimonialData[i].rating);
}
function moveTestimonial(dir) {
  currentTIndex = (currentTIndex + dir + testimonialData.length) % testimonialData.length;
  updateTestimonialCard(currentTIndex);
}

document.querySelector(".slider-arrow.prev")?.addEventListener("click", () => moveTestimonial(-1));
document.querySelector(".slider-arrow.next")?.addEventListener("click", () => moveTestimonial(1));

window.onload = function () {
  showSlide(0);
  startAutoScroll();
  updateTestimonialCard(0);
};
</script>
