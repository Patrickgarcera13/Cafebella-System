<?php
// ✅ Connect to your shared database
require __DIR__ . '/website_php/database.php';

// Fetch all event packages from database
$packages = [];
try {
    $stmt = $pdo->query("SELECT * FROM event_packages WHERE status = 'active' ORDER BY id DESC");
    $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching packages: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Package - Cafe Bella</title>
<link href="https://fonts.googleapis.com/css2?family=Domine:wght@400;600&display=swap" rel="stylesheet">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Plus Jakarta Sans', 'Segoe UI', Roboto, Arial, sans-serif; background: #f5f5f5; }

/******************************** TOPBAR (100% Identical to Index & Menu) ********************************/
.topbar {
  background: #fff;
  display: flex;
  justify-content: space-between;
  padding: 0 60px;          /* Tinanggal ang top/bottom padding para fixed ang height */
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
}
.book-link:hover { color: #0a2f00; }
/******************************** NAVBAR (100% Identical to Index & Menu) ********************************/
.navbar { 
  background: #114500; 
  display: flex; 
  align-items: center; 
  padding: 0 60px;          /* Tinanggal ang spacing para walang layout shifts */
  height: 80px;             /* Selyadong 80px katulad ng ibang pages */
  position: fixed; 
  top: 36px;                /* Eksaktong nakadikit sa ilalim ng topbar */
  left: 0; 
  width: 100%; 
  z-index: 999; 
}
.header-wrapper { position: sticky; top: 0; z-index: 999; }
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
.nav-menu a::after { content: ""; position: absolute; left: 0; bottom: 0; width: 0%; height: 2px; background-color: white; transition: width 0.3s ease; }
.nav-menu a.active::after { width: 100%; }
.nav-menu a:hover::after { width: 100%; }

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
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 14px; pointer-events: none; }

/******************************** CONTENT SECTIONS ********************************/
.section { padding: 40px 80px; margin-top: 140px; } /* Inadjust sa 140px para saktong maluwag sa ilalim ng fixed navs */
.section-title { text-align: center; margin-bottom: 50px; }
.section-title h2 { color: #114500; font-size: 32px; }
.section-title p { font-size: 17px; max-width: 600px; margin: 10px auto; }
.cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
.card { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1); transition: 0.3s; }
.card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
.card img { width: 100%; height: 200px; object-fit: cover; }
.card-content { padding: 20px; }
.card h3 { color: #114500; margin-bottom: 10px; }
.price { font-weight: bold; margin-bottom: 15px; }
.card p { font-size: 14px; margin-bottom: 15px; }
.btn { display: inline-block; padding: 12px 28px; background: #114500; color: white; border: 2px solid #114500; border-radius: 50px; font-family: 'Domine', serif; font-weight: 600; text-align: center; cursor: pointer; text-decoration: none; transition: 0.3s ease; }
.btn:hover { background: white; color: #114500; border: 2px solid #114500; }

/******************************** FOOTBAR ********************************/
.footbar { background: white; border-top: 2px solid #114500; border-bottom: 2px solid #114500; text-align: center; padding: 15px 20px; font-family: 'Domine', serif; color: #114500; font-size: 14px; margin-top: 40px; }

/******************************** FOOTER (Revised & Replaced from Index) ********************************/
.footer { background: #eee; padding: 40px 60px; margin-top: 80px; }
.footer-top { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 30px; }
.footer-logo img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
.footer-col { font-size: 13px; }
.footer-col h4 { margin-bottom: 10px; color: #114500; }
.footer-col p, .footer-col a { display: block; color: #114500; text-decoration: none; margin-bottom: 5px; }
.footer-bottom { text-align: center; margin-top: 20px; color: #114500; }
.gcash img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
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
      <div class="nav-logo"><a href="index.php"><img src="IMAGES/Cafebella.jpg" alt="Cafe Bella Logo"></a></div>
      <ul class="nav-menu">
        <li><a href="index.php">Home</a></li>
        <li><a href="Menu.php">Menu</a></li>
        <li><a href="Package.php" class="active">Packages</a></li>
        <li><a href="Location.html">Location</a></li>
        <li><a href="FAQ.html">FAQs</a></li>
      </ul>
      <div class="nav-search">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Search menu...">
          <span class="search-icon">🔍</span>
        </div>
      </div>
    </div>
  </div>

  <div class="section">
    <div class="section-title">
      <h2>Our Packages</h2>
      <p>Cafe BELLA offers curated event packages designed for memorable celebrations.</p>
    </div>
    <div class="cards" id="packagesContainer">

<?php
// ✅ Dynamically render packages from database
if (!empty($packages)) {
    foreach ($packages as $pkg) {
        $imageSrc = htmlspecialchars($pkg['image']);
        $title = htmlspecialchars($pkg['title']);
        $displayPrice = htmlspecialchars($pkg['display_price']);
        $desc = htmlspecialchars($pkg['description']);
        $serviceName = htmlspecialchars($pkg['service_name']);
        // Gawing maikli ang key para sa localStorage
        $serviceKey = str_contains(strtolower($serviceName), 'coffee') ? 'coffee' :
        (str_contains(strtolower($serviceName), 'matcha') ? 'matcha' : 'tattoo');
        ?>
        <div class="card">
          <img src="<?php echo $imageSrc; ?>" alt="<?php echo $title; ?>">
          <div class="card-content">
            <h3><?php echo $title; ?></h3>
            <div class="price"><?php echo $displayPrice; ?></div>
            <p><?php echo $desc; ?></p>
            <a href="Bookyourevent.php" class="btn" onclick="localStorage.setItem('selectedService', '<?php echo $serviceKey; ?>')">Book Now</a>
          </div>
        </div>
        <?php
        }
        } else {
          echo '<p style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">No packages available yet. Please check back later.</p>';
          }
  ?>

    </div>
  </div>

  <div class="footbar">Kape lang saglit tapos laban ulit</div>
  
  <div class="footer rise-item">
    <div class="footer-top rise-item">
      <div class="footer-logo rise-item">
        <a href="index.php">
          <img src="IMAGES/Cafebella.jpg" alt="Cafe Bella Logo">
        </a>
      </div>
      <div class="footer-col rise-item">
        <h4 rise-desc>Quick Links</h4>
        <a href="index.php">Home</a>
        <a href="Menu.php">Menu</a>
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
    <div class="footer-bottom rise-item">
      © 2026 Cafe BELLA. All rights reserved.
    </div>
  </div>

<script>
// ✅ Removed old localStorage logic — all data comes directly from database
document.addEventListener("DOMContentLoaded", function() {
  console.log("Packages loaded from database successfully");
});
</script>
</body>
</html>
