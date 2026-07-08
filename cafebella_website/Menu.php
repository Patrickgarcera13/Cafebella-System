<?php
// ✅ Connect to your shared database
require __DIR__ . '/website_php/database.php';

// Fetch all menu items once for reuse
$allMenu = [];
try {
    $stmt = $pdo->query("SELECT * FROM menu_items ORDER BY category, name");
    $allMenu = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching menu: " . $e->getMessage());
}

// Helper: Filter items by category
function getByCategory($items, $cat) {
    return array_filter($items, fn($i) => strtolower(trim($i['category'])) === strtolower($cat));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Menu - Cafe Bella</title>
<link href="https://fonts.googleapis.com/css2?family=Domine:wght@400;600&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f2f2f2; }
.header-wrapper { position: sticky; top: 0; z-index: 999; }

/******************************** TOPBAR (100% Identical to Index, Package & FAQ) ********************************/
.topbar { 
  background: #fff; 
  display: flex; 
  justify-content: space-between;
  padding: 0 60px;          /* Tinanggal ang top/bottom padding para kontrolado ang height */
  height: 36px;             /* Eksaktong 36px para selyado ang taas */
  position: fixed; 
  top: 0; 
  left: 0; 
  width: 100%; 
  z-index: 1000;
  align-items: center;      /* Vertically aligned ang mga elements sa gitna */
}
.topbar-center { 
  flex: 1; 
  text-align: center; 
  color: #114500;
  font-size: 13px; 
  line-height: 36px;        /* Pinantay sa height ng topbar para walang pixel distortion */
  font-family: 'Plus Jakarta Sans', sans-serif; /* Pinilit na pareho ang font */
}
.topbar-right { 
  display: flex; 
  gap: 15px; 
  align-items: center; 
  height: 100%;
}
.topbar-right img { 
  width: 18px; 
  height: 18px; 
  display: block;
}
.book-link { 
  color: #114500; 
  font-weight: bold;
  text-decoration: underline; 
  cursor: pointer; 
  font-size: 13px;
  font-family: 'Plus Jakarta Sans', sans-serif;
}

/******************************** NAVBAR (100% Identical to Index, Package & FAQ) ********************************/
.navbar { 
  background: #114500; 
  display: flex; 
  align-items: center;
  padding: 0 60px;          /* Tinanggal ang spacing para walang layout shifts */
  height: 80px;             /* Selyadong 80px katulad ng index.html */
  position: fixed; 
  top: 36px;                /* Eksaktong nakadikit sa ilalim ng topbar */
  left: 0; 
  width: 100%; 
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
  font-family: 'Plus Jakarta Sans', sans-serif;
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
.nav-menu a.active::after {
  width: 100%;
}
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
  font-size: 13px;
  font-family: 'Domine', serif; /* Pinantay ang input box style sa index niyo */
}
.search-icon { 
  position: absolute;
  left: 12px; 
  top: 50%; 
  transform: translateY(-50%); 
  font-size: 14px; 
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
.menu-categories {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 10px;
  margin: 140px auto 40px;
  padding: 14px 18px;
  width: fit-content;
  background: rgba(255, 255, 255, 0.55);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.4);
  box-shadow:
    0 10px 30px rgba(0,0,0,0.08),
    inset 0 1px 0 rgba(255,255,255,0.6);
  position: relative;
  scrollbar-width: none;
  flex-wrap: wrap; /* para mag next line instead of scroll */
}
.menu-categories::before {
  content: "";
  position: absolute;
  inset: -40%;
  background: radial-gradient(circle at top left,
    rgba(17,69,0,0.18),
    transparent 60%),
    radial-gradient(circle at bottom right,
    rgba(255,140,0,0.12),
    transparent 55%);
  filter: blur(30px);
  z-index: 0;
}
.menu-categories::-webkit-scrollbar {
  display: none;
}
.menu-title {
  grid-column: 1 / -1;
  font-size: 26px;
  font-weight: 700;
  color: #114500;
  margin: 60px 0 30px;
  text-align: center;
  position: relative;
  letter-spacing: 0.5px;
}

/* Elegant divider line */
.menu-title::before,
.menu-title::after {
  content: "";
  position: absolute;
  top: 50%;
  width: 35%;
  height: 1.5px;
  background: linear-gradient(to right, transparent, rgba(17,69,0,0.4), transparent);
}

.menu-title::before {
  left: 0;
}

.menu-title::after {
  right: 0;
}

/* subtle glow dot */
.menu-title span {
  background: white;
  padding: 0 14px;
}
.menu-grid { 
  width:90%;
  margin:auto;
  display:grid;
  gap:25px;
}

.menu-group {
  display: none;
  grid-template-columns: repeat(4, 1fr);
  gap: 28px;
  padding: 40px 0 60px;
  position: relative;
}
.menu-group.active {
  display:grid;
}
.menu-group.active::after {
  display: none;
  content: "";
  position: absolute;
  bottom: 10px;
  left: 0;
  width: 100%;
  height: 2px;
  background: linear-gradient(to right, transparent, #114500, transparent);
}
.menu-card2 {
  background: rgba(255, 255, 255, 0.75);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);

  border-radius: 22px;
  overflow: hidden;

  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
  border: 1px solid rgba(255,255,255,0.4);

  display: flex;
  flex-direction: column;

  position: relative;

  transition: all 0.35s ease;
  cursor: pointer;
}

/* hover premium lift */
.menu-card2:hover {
  transform: translateY(-10px);
  box-shadow: 0 25px 60px rgba(0,0,0,0.18);
}
.menu-card2 img {
  width: 100%;
  height: 210px;
  object-fit: cover;
  transition: transform 0.5s ease;
}
.menu-card2:hover img {
  transform: scale(1.08);
}
.menu-card2 h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1a1a1a;

  margin: 14px 16px 6px;
  line-height: 1.3;
  letter-spacing: 0.2px;
}
.menu-card2 p {
  margin: 0 16px 10px;
  font-size: 13px;
  color: #666;
  line-height: 1.4;
}
.menu-card2 p::before {
  content: "₱ ";
  font-weight: 600;
  color: #114500;
}
@media (max-width: 1024px){
  .menu-group {
    grid-template-columns: repeat(3, 1fr);
  }
}
@media (max-width: 768px){
  .menu-group {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 480px){
  .menu-group {
    grid-template-columns: 1fr;
  }
}
.menu-card2::after {
  content: "";
  position: absolute;
  inset: 0;

  background: radial-gradient(circle at top left,
    rgba(17,69,0,0.15),
    transparent 60%);

  opacity: 0;
  transition: 0.4s;
}
.menu-card2:hover::after {
  opacity: 1;
}

.category-title {
  text-align: center;
  font-size: 22px;
  color: #114500;
  margin-top: 140px;
  margin-bottom: -100px;
  font-weight: 600;
  letter-spacing: 1px;
}
.ingredients {
  margin: 10px 16px 14px;
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.ingredients h4 {
  display: none;
}
.ingredients ul {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  list-style: none;
  padding: 0;
}
.ingredients li {
  font-size: 11px;
  color: #114500;

  background: rgba(17, 69, 0, 0.08);
  border: 1px solid rgba(17, 69, 0, 0.15);

  padding: 5px 10px;
  border-radius: 999px;

  backdrop-filter: blur(8px);
}

/******************************** CATEGORIES ********************************/
.cat-btn {
  position: relative;
  z-index: 1;

  display: flex;
  align-items: center;
  gap: 8px;

  padding: 10px 16px;

  font-size: 13px;
  font-weight: 500;
  letter-spacing: 0.3px;

  border-radius: 999px;

  border: 1px solid transparent;

  background: rgba(255,255,255,0.7);
  color: #333;

  cursor: pointer;

  transition: all 0.3s ease;

  white-space: nowrap;
  backdrop-filter: blur(10px);
}
.cat-btn:hover {
  transform: translateY(-3px) scale(1.03);
  color: white;

  background: linear-gradient(
    135deg,
    #114500,
    #1f7a00,
    #2ecc71
  );

  box-shadow:
    0 12px 25px rgba(17,69,0,0.35);
}
.cat-btn.active {
  background: #111;
  color: #fff;
  box-shadow: 0 8px 20px rgba(0,0,0,0.25);
  transform: translateY(-1px);
}
.cat-btn:hover .cat-icon {
  transform: rotate(8deg) scale(1.1);
  filter: brightness(1.2);
}
.cat-icon {
  width: 16px;
  height: 16px;
  opacity: 0.85;
  transition: transform 0.3s ease;
}
.cat-btn.active {
  color: #fff;

  background: linear-gradient(
    135deg,
    #0f3d00,
    #1f7a00
  );

  box-shadow:
    0 10px 25px rgba(0,0,0,0.25),
    0 0 0 3px rgba(46, 204, 113, 0.2);
}
.cat-btn.active::after {
  content: "";
  position: absolute;
  bottom: -6px;
  left: 20%;
  width: 60%;
  height: 3px;

  background: linear-gradient(90deg, transparent, #2ecc71, transparent);

  border-radius: 10px;

  animation: glowMove 1.5s infinite ease-in-out;
}

@keyframes glowMove {
  0% { opacity: 0.4; transform: scaleX(0.8); }
  50% { opacity: 1; transform: scaleX(1); }
  100% { opacity: 0.4; transform: scaleX(0.8); }
}
@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}
.modal {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.65);
  backdrop-filter: blur(10px);
  justify-content: center;
  align-items: center;
  z-index: 2000;
}
.modal-header h2 {
  font-size: 22px;
  font-weight: 700;
  color: #114500;
  margin-bottom: 6px;
}
.modal-content {
  width: 850px;
  max-width: 92%;
  background: #fff;
  border-radius: 22px;
  overflow: hidden;
  position: relative;

  box-shadow: 0 30px 80px rgba(0,0,0,0.25);
  animation: pop 0.25s ease;
}
.modal-image {
  position: relative;
  height: 260px;
}

.modal-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.modal-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.55), transparent);
}
@keyframes pop {
  from { transform: scale(0.9); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.modal-body {
  display: flex;
  flex-direction: row;
}
.modal-left {
  flex: 1;
  background: #f5f5f5;
  min-height: 100%;
}
.modal-left img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.modal-right {
  flex: 1;
  padding: 24px;
}
.modal-right h2 {
  color: #114500;
  margin-bottom: 10px;
}
.modal-right p {
  color: #666;
  font-size: 14px;
}
.modal-price {
  margin-top: 18px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.modal-desc {
  font-size: 14px;
  color: #555;
  margin-top: 12px;
  line-height: 1.6;
}

/* ANIMATION */
@keyframes pop {
  from { transform: scale(0.92); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.price-box {
  background: #f4f4f4;
  border-left: 4px solid #114500;
  padding: 12px 14px;
  border-radius: 10px;
  font-size: 14px;
  color: #333;
  transition: 0.3s;
}
.price-group {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;

  margin: 12px 16px 18px;
}

.price-group span {
  background: linear-gradient(135deg, #114500, #1f7a00);
  color: white;

  padding: 6px 12px;
  border-radius: 999px;

  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.3px;

  box-shadow: 0 8px 18px rgba(17,69,0,0.25);
}
.price-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.price-box {
  background: linear-gradient(135deg, #114500, #1f7a00);
  color: white;
  padding: 10px 14px;
  border-radius: 12px;

  font-size: 13px;
  font-weight: 600;

  box-shadow: 0 10px 20px rgba(17,69,0,0.25);
}
.section {
  margin-top: 18px;
}

.section-title {
  font-size: 13px;
  font-weight: 600;
  color: #114500;
  margin-bottom: 8px;
  letter-spacing: 0.5px;
}

/* INGREDIENT CHIPS */
.ingredients-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.ingredients-chips span {
  font-size: 12px;
  padding: 6px 12px;
  border-radius: 999px;

  background: rgba(17,69,0,0.08);
  border: 1px solid rgba(17,69,0,0.15);
  color: #114500;

  backdrop-filter: blur(8px);
}

/* DIVIDER */
.divider {
  height: 1px;
  margin: 18px 0;
  background: linear-gradient(to right, transparent, #ddd, transparent);
}
.close-btn {
  position: absolute;
  right: 18px;
  top: 12px;
  font-size: 28px;
  cursor: pointer;
  color: white;
  z-index: 10;
}
.title-divider {
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #114500, transparent);
  border-radius: 10px;
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
  text-align: center;
  padding: 20px;
}

.footer {
  background: #eee;
  padding: 40px 60px;
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

<div class="header-wrapper">
  <div class="topbar">
    <div class="topbar-center">Planning an event? <a href="package.php" class="book-link">Book Now</a> and reserve your date with Cafe Bella.</div>
    <div class="topbar-right">
      <a href="https://www.facebook.com/share/1CiwKkiCkY/" target="_blank" rel="noopener"><img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" alt="Facebook"></a>
      <a href="https://www.tiktok.com/@christiandavidangelo?_r=1&_t=ZS-94tpPJhFWzZ" target="_blank" rel="noopener"><img src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png" alt="TikTok"></a>
    </div>
  </div>

  <div class="navbar">
    <div class="nav-logo"><a href="index.php"><img src="IMAGES/Cafebella.jpg" alt="Cafe Bella"></a></div>
    <ul class="nav-menu">
      <li><a href="index.php">Home</a></li>
      <li><a href="menu.php" class="active">Menu</a></li>
      <li><a href="package.php">Packages</a></li>
      <li><a href="Location.html">Location</a></li>
      <li><a href="FAQ.html">FAQs</a></li>
    </ul>
    <div class="nav-search">
      <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search menu...">
        <span class="search-icon">🔍</span>
      </div>
      <div id="suggestions" class="suggestions-box"></div>
    </div>
  </div>
</div>

<h2 class="category-title">Browse Menu</h2>
<div class="menu-categories rise-item">
  <button class="cat-btn active" onclick="showMenu('all', this)"><img src="IMAGES/menu.png" class="cat-icon">All</button>
  <button class="cat-btn" onclick="showMenu('friedrice', this)"><img src="IMAGES/friedrice.png" class="cat-icon">Fried Rice</button>
  <button class="cat-btn" onclick="showMenu('bacsilog', this)"><img src="IMAGES/bacon.png" class="cat-icon">Bacsilog</button>
  <button class="cat-btn" onclick="showMenu('noodles', this)"><img src="IMAGES/noodles.png" class="cat-icon">Noodles</button>
  <button class="cat-btn" onclick="showMenu('snacks', this)"><img src="IMAGES/snacks.png" class="cat-icon">Snacks</button>
  <button class="cat-btn" onclick="showMenu('frappe', this)"><img src="IMAGES/frappeicon.png" class="cat-icon">Frappe</button>
  <button class="cat-btn" onclick="showMenu('coffee', this)"><img src="IMAGES/coffeeicon.png" class="cat-icon">Coffee & Non-Coffee</button>
  <button class="cat-btn" onclick="showMenu('matchaR', this)"><img src="IMAGES/matcha.png" class="cat-icon">Matcha Series</button>
</div>

<div class="menu-grid rise-item">

<!-- FRIED RICE -->
<div class="menu-group rise-item" id="friedrice">
  <h2 class="menu-title rise-desc">Davion's Wok Tossed Fried Rice</h2>
<?php foreach (getByCategory($allMenu, 'Fried Rice') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="Solo ₱<?= htmlspecialchars($row['price_solo'] ?? $row['price']) ?> | Double ₱<?= htmlspecialchars($row['price_double'] ?? '') ?>"
       data-ingredients="<?= htmlspecialchars($row['ingredients'] ?? '') ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" class="lazy-img" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group">
      <span>Solo ₱<?= htmlspecialchars($row['price_solo'] ?? $row['price']) ?></span>
      <?php if (!empty($row['price_double'])): ?><span>Double ₱<?= htmlspecialchars($row['price_double']) ?></span><?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<!-- BACSILOG -->
<div class="menu-group rise-item" id="bacsilog">
  <h2 class="menu-title rise-desc">Bacsilog</h2>
<?php foreach (getByCategory($allMenu, 'Bacsilog') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="₱<?= htmlspecialchars($row['price']) ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" class="lazy-img" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group"><span>₱<?= htmlspecialchars($row['price']) ?></span></div>
  </div>
<?php endforeach; ?>
</div>

<!-- NOODLES -->
<div class="menu-group rise-item" id="noodles">
  <h2 class="menu-title rise-desc">Cheesy Spicy Noodles</h2>
<?php foreach (getByCategory($allMenu, 'Noodles') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="₱<?= htmlspecialchars($row['price']) ?>"
       data-ingredients="<?= htmlspecialchars($row['ingredients'] ?? '') ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" class="lazy-img" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group"><span>₱<?= htmlspecialchars($row['price']) ?></span></div>
  </div>
<?php endforeach; ?>
</div>

<!-- SNACKS -->
<div class="menu-group rise-item" id="snacks">
  <h2 class="menu-title rise-desc">Snacks</h2>
<?php foreach (getByCategory($allMenu, 'Snacks') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="₱<?= htmlspecialchars($row['price']) ?>"
       data-ingredients="<?= htmlspecialchars($row['ingredients'] ?? '') ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" class="lazy-img" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group"><span>₱<?= htmlspecialchars($row['price']) ?></span></div>
  </div>
<?php endforeach; ?>
</div>

<!-- FRAPPE -->
<div class="menu-group rise-item" id="frappe">
  <h2 class="menu-title rise-desc">Non Coffee Based</h2>
<?php foreach (getByCategory($allMenu, 'Frappe - Non Coffee') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="16 oz ₱<?= htmlspecialchars($row['price']) ?>"
       data-ingredients="<?= htmlspecialchars($row['ingredients'] ?? '') ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group"><span>16 oz ₱<?= htmlspecialchars($row['price']) ?></span></div>
  </div>
<?php endforeach; ?>

  <h2 class="menu-title rise-desc">Coffee Based</h2>
<?php foreach (getByCategory($allMenu, 'Frappe - Coffee') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="16 oz ₱<?= htmlspecialchars($row['price']) ?>"
       data-ingredients="<?= htmlspecialchars($row['ingredients'] ?? '') ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group"><span>16 oz ₱<?= htmlspecialchars($row['price']) ?></span></div>
  </div>
<?php endforeach; ?>
</div>

<!-- COFFEE & NON-COFFEE -->
<div class="menu-group rise-item" id="coffee">
  <h2 class="menu-title rise-desc">Non-Coffee</h2>
<?php foreach (getByCategory($allMenu, 'Non-Coffee') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="16oz ₱<?= htmlspecialchars($row['price_16oz'] ?? $row['price']) ?> | 22oz ₱<?= htmlspecialchars($row['price_22oz'] ?? '') ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" class="lazy-img" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group">
      <span>16 oz ₱<?= htmlspecialchars($row['price_16oz'] ?? $row['price']) ?></span>
      <?php if (!empty($row['price_22oz'])): ?><span>22 oz ₱<?= htmlspecialchars($row['price_22oz']) ?></span><?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>

  <h2 class="menu-title rise-desc">Coffee</h2>
<?php foreach (getByCategory($allMenu, 'Coffee') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" class="lazy-img" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group">
      <?php if (!empty($row['price_16oz'])): ?><span>COLD 16 oz ₱<?= htmlspecialchars($row['price_16oz']) ?></span><?php endif; ?>
      <?php if (!empty($row['price_22oz'])): ?><span>22 oz ₱<?= htmlspecialchars($row['price_22oz']) ?></span><?php endif; ?>
      <?php if (!empty($row['price_hot'])): ?><span>HOT 12 oz ₱<?= htmlspecialchars($row['price_hot']) ?></span><?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<!-- MATCHA SERIES -->
<div class="menu-group rise-item" id="matchaR">
  <h2 class="menu-title rise-desc">Matcha Regular</h2>
<?php foreach (getByCategory($allMenu, 'Matcha Regular') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="16oz ₱<?= htmlspecialchars($row['price_solo'] ?? $row['price']) ?> | 22oz ₱<?= htmlspecialchars($row['price_double'] ?? '') ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" class="lazy-img" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group">
      <span>16 oz ₱<?= number_format($row['price_solo'] ?? $row['price'], 0) ?></span>
      <?php if (!empty($row['price_double'])): ?><span>22 oz ₱<?= number_format($row['price_double'], 0) ?></span><?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<div class="menu-group rise-item" id="matchaP">
  <h2 class="menu-title rise-desc">Matcha Premium</h2>
<?php foreach (getByCategory($allMenu, 'Matcha Premium') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="₱<?= htmlspecialchars($row['price']) ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" class="lazy-img" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group"><span>₱<?= number_format($row['price'], 0) ?></span></div>
  </div>
<?php endforeach; ?>
</div>

<div class="menu-group rise-item" id="matchaH">
  <h2 class="menu-title rise-desc">Matcha Hojicha</h2>
<?php foreach (getByCategory($allMenu, 'Matcha Hojicha') as $row): ?>
  <div class="menu-card2 rise-item"
       onclick="openModal(this)"
       data-title="<?= htmlspecialchars($row['name']) ?>"
       data-desc="<?= htmlspecialchars($row['description'] ?? '') ?>"
       data-img="<?= htmlspecialchars($row['image']) ?>"
       data-price="₱<?= htmlspecialchars($row['price']) ?>">
    <img src="<?= htmlspecialchars($row['image']) ?>" loading="lazy" class="lazy-img" alt="<?= htmlspecialchars($row['name']) ?>">
    <h3><?= htmlspecialchars($row['name']) ?></h3>
    <div class="price-group"><span>₱<?= number_format($row['price'], 0) ?></span></div>
  </div>
<?php endforeach; ?>
</div>

</div>

<div id="menuModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">×</span>
    <div class="modal-body">
      <div class="modal-left"><img id="modalImg" src="" alt="Menu Item"></div>
      <div class="modal-right">
        <h2 id="modalTitle"></h2>
        <div class="title-divider"></div>
        <p id="modalDesc" class="modal-desc"></p>
        <div class="section">
          <h4 class="section-title">Ingredients</h4>
          <div id="modalIngredients" class="ingredients-chips"></div>
        </div>
        <div class="divider"></div>
        <div class="section">
          <h4 class="section-title">Price</h4>
          <div id="modalPrice" class="price-container"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="footbar">Kape lang saglit tapos laban ulit</div>

<div class="footer rise-item">
  <div class="footer-top rise-item">
    <div class="footer-logo rise-item"><a href="index.php"><img src="IMAGES/Cafebella.jpg" alt="Cafe Bella Logo"></a></div>
    <div class="footer-col rise-item">
      <h4 rise-desc>Quick Links</h4>
      <a href="index.php">Home</a>
      <a href="menu.php">Menu</a>
      <a href="package.php">Event Packages</a>
      <a href="feedback.html">Feedback</a>
      <a href="Location.html">Location</a>
      <a href="FAQ.html">FAQ's</a>
    </div>
    <div class="footer-col rise-item">
      <h4 rise-desc>Services</h4>
      <a href="package.php">Coffee Booth</a>
      <a href="package.php">Matcha Booth</a>
      <a href="package.php">Tattoo Event</a>
    </div>
    <div class="footer-col rise-item">
      <h4 rise-desc>About</h4>
      <a href="https://www.facebook.com/share/1CiwKkiCkY/" target="_blank" rel="noopener">Contact Our Team</a>
    </div>
    <div class="footer-col rise-item">
      <h4 rise-desc>Socials</h4>
      <a href="https://www.facebook.com/share/1CiwKkiCkY/" target="_blank" rel="noopener">Facebook</a>
      <a href="https://www.tiktok.com/@christiandavidangelo?_r=1&_t=ZS-94tpPJhFWzZ" target="_blank" rel="noopener">Tiktok</a>
    </div>
    <div class="footer-col gcash rise-item">
      <h4 rise-desc>We accept</h4>
      <img src="IMAGES/GCash.png" alt="GCash">
    </div>
  </div>
  <div class="footer-bottom rise-item">© 2026 Cafe BELLA. All rights reserved.</div>
</div>

<script>
// --- YOUR EXISTING SCRIPT REMAINS FULLY INTACT ---
const menuItems = ["Kimchi Fried Rice", "Spam Fried Rice", "Bacsilog", "Cheesy Spicy Noodles", "Chicken Sandwich", "Americano", "Matcha Latte"];
function showSuggestions() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const box = document.getElementById("suggestions");
  box.innerHTML = "";
  if (!input) { box.style.display = "none"; return; }
  const filtered = menuItems.filter(i => i.toLowerCase().includes(input));
  if (!filtered.length) { box.style.display = "none"; return; }
  filtered.forEach(item => {
    const div = document.createElement("div");
    div.className = "suggestion-item";
    div.textContent = item;
    div.onclick = () => { document.getElementById("searchInput").value = item; box.style.display = "none"; };
    box.appendChild(div);
  });
  box.style.display = "block";
}
document.addEventListener("click", e => { if (!document.querySelector(".nav-search").contains(e.target)) document.getElementById("suggestions").style.display = "none"; });

const riseElements = document.querySelectorAll(".rise-title, .rise-desc, .rise-item");
function handleRise() {
  const trigger = window.innerHeight * 0.85;
  riseElements.forEach(el => el.classList.toggle("show", el.getBoundingClientRect().top < trigger));
}
window.addEventListener("scroll", handleRise);
window.addEventListener("load", handleRise);

function openModal(card) {
  document.getElementById("menuModal").style.display = "flex";
  document.getElementById("modalImg").src = card.dataset.img;
  document.getElementById("modalTitle").textContent = card.dataset.title;
  document.getElementById("modalDesc").textContent = card.dataset.desc;
  const ingredients = card.dataset.ingredients;
  const ingBox = document.getElementById("modalIngredients");
  ingBox.innerHTML = "";
  if (ingredients) ingredients.split(",").forEach(i => { const s=document.createElement("span"); s.textContent=i.trim(); ingBox.appendChild(s); });
  const price = card.dataset.price;
  let priceHTML = "";
  if (price.includes("|")) price.split("|").forEach(p => priceHTML += `<div class="price-box">${p.trim()}</div>`);
  else priceHTML = `<div class="price-box">${price}</div>`;
  document.getElementById("modalPrice").innerHTML = priceHTML;
}
function closeModal() { document.getElementById("menuModal").style.display = "none"; }
window.onclick = e => { if (e.target.id === "menuModal") closeModal(); };

function showMenu(category, btn) {
  document.querySelectorAll(".menu-group").forEach(g => g.classList.remove("active"));
  document.querySelectorAll(".cat-btn").forEach(b => b.classList.remove("active"));
  if (category === "all") document.querySelectorAll(".menu-group").forEach(g => g.classList.add("active"));
  else document.getElementById(category)?.classList.add("active");
  btn?.classList.add("active");
}
window.onload = () => showMenu("all");
</script>
</body>
</html>
