<?php
// Lock page to logged-in Admins only
require 'website_php/auth_check.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cafe Bella - Admin Panel</title>
<style>
  body {
     font-family: Arial, sans-serif;background: #f9f9f9; padding: 30px; color: #333;
 }
  h1 {
     color: #114500;
      display: inline-block;
       margin: 0;
       }

  .admin-header {
     display: flex;
      justify-content: space-between;
       align-items: center;
        margin-bottom: 20px;
       }
  .admin-container {
     max-width: 1050px;
      margin: 0 auto;
       background: white;
        padding: 20px;
         border-radius: 12px;
          box-shadow: 0 4px 10px rgba(0,0,0,0.1);
         }
  
  /* Navigation Tabs Control */
  .admin-tabs { display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid #114500; padding-bottom: 10px; }
  .tab-btn { padding: 10px 20px; font-weight: bold; cursor: pointer; background: #e0eee0; color: #114500; border: none; border-radius: 6px; transition: 0.3s; }
  .tab-btn.active { background: #114500; color: white; }

  /* Section Containers visibility logic */
  .admin-section { display: none; }
  .admin-section.active { display: block; }

  /* Form Styles */
  .add-form-container { background: #f2f7f2; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #e0eee0; }
  .add-form-container h3 { color: #114500; margin-top: 0; margin-bottom: 15px; }
  .form-row { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 15px; }
  .form-group { flex: 1; min-width: 180px; display: flex; flex-direction: column; }
  .form-group.full-width { flex: 1 1 100%; }
  .form-group label { font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #555; }
  .form-group input, .form-group select, .form-group textarea { padding: 8px; border: 1px solid #ccc; border-radius: 4px; outline: none; font-family: Arial, sans-serif; }
  .btn-add-item { background: #114500; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; align-self: flex-end; }
  .btn-add-item:hover { background: #0a2f00; }

  /* Data Table Layouts */
  table { width: 100%; border-collapse: collapse; margin-top: 20px; }
  th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: top; }
  th { background-color: #114500; color: white; }
  img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; }
/* Palitan mo lang itong part na ito sa iyong CSS */

  .btn { 
    padding: 8px 14px; 
    cursor: pointer; 
    border: none; 
    border-radius: 6px; 
    font-weight: bold; 
    font-size: 13px;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .btn-edit { background: #feebc8; color: #dd6b20; }
  .btn-edit:hover { background: #dd6b20; color: white; }

  .btn-save { background: #c6f6d5; color: #114500; display: none; }
  .btn-save:hover { background: #114500; color: white; }

  /* Nilagyan ng 12px na gap/distansya para hindi sila magkadikit */
  .btn-delete { background: #fed7d7; color: #e53e3e; margin-left: 12px; }
  .btn-delete:hover { background: #e53e3e; color: white; }

  /* ================= PREMIUM REFRESH FOR UTILITY CONTROLS ================= */

/* 1. Para sa BACK TO HOME LINK */
.back-btn { 
  display: inline-flex; 
  align-items: center; 
  color: #114500; 
  text-decoration: none; 
  font-weight: 600; 
  font-size: 14px; 
  padding: 8px 14px;
  background: #f0fff0; /* Banayad na berdeng background */
  border-radius: 8px;
  transition: all 0.2s ease;
}

.back-btn:hover { 
  background: #e0eee0; 
  color: #0a2f00;
  text-decoration: none;
  transform: translateX(-3px); /* Kaunting usog sa kaliwa kapag tinutukan */
}

/* 2. Para sa LOGOUT BUTTON */
.btn-logout { 
  background: transparent; 
  color: #dc3545; 
  padding: 8px 18px; 
  border: 1px solid #fecaca; /* Manipis na pulang border */
  border-radius: 8px; 
  cursor: pointer; 
  font-weight: 600; 
  font-size: 14px;
  transition: all 0.2s ease;
}

.btn-logout:hover { 
  background: #fff5f5; /* Malambot na pulang glow */
  border-color: #fca5a5;
  color: #c82333;
  transform: translateY(-1px); /* Bahagyang aangat para sa interactive feel */
}
</style>
</head>
<body>

<div class="admin-container">
  <div class="admin-header">
    <a href="index.php" class="back-btn" id="publicLink">&larr; Back to Home</a>
    <button class="btn-logout" onclick="handleLogout()">Logout</button>
  </div>
  
  <div class="admin-header" style="margin-bottom: 25px;">
    <h1>Cafe Bella Admin Dashboard</h1>
  </div>

  <div class="admin-tabs">
    <button class="tab-btn active" onclick="switchTab('menu', this)">Manage Food Menu</button>
    <button class="tab-btn" onclick="switchTab('packages', this)">Manage Event Packages</button>
    <button class="tab-btn" onclick="switchTab('feedback', this)">Manage Customer Feedback</button>
  </div>

<div id="menuSection" class="admin-section active">
    <div class="add-form-container">
      <h3>+ Add New Menu Item</h3>
      
      <form id="addItemForm" onsubmit="handleAddItem(event)" enctype="multipart/form-data">
        <div class="form-row">
          <div class="form-group">
            <label for="newTitle">Item Name</label>
            <input type="text" id="newTitle" placeholder="e.g., Pork Sisig" required>
          </div>
          
          <div class="form-group">
            <label for="newCategory">Category</label>
            <input type="text" id="newCategory" placeholder="e.g., friedrice, drinks, snacks" required>
          </div>
          
          <div class="form-group">
            <label for="newPrice">Price</label>
            <input type="text" id="newPrice" placeholder="e.g., 95" required>
          </div>
          
          <div class="form-group">
            <label for="newImg">Upload Image</label>
            <input type="file" id="newImg" accept="image/*" required>
          </div>
        </div>
        <button type="submit" class="btn-add-item">Add Item</button>
      </form>
    </div>
    <table>
      <thead>
        <tr>
          <th>Image</th>
          <th>Item Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="adminTableBody"></tbody>
    </table>
  </div>

<div id="packagesSection" class="admin-section">
  <div class="add-form-container">
    <div style="background:#f0f8ff; padding:12px; border-radius:6px; margin:15px 0; font-size:13px; line-height:1.5;">
  <strong>📖 Gabay sa Pagtatakda ng Presyo:</strong><br>
  • <strong>Base Price (₱)</strong> — Presyo kapag umabot sa <strong>Minimum Guests</strong>.<br>
  • <strong>Min / Max Guests</strong> — Pinakamababa at pinakamataas na bilang ng tao na pwedeng tanggapin.<br>
  • <strong>Break Point (Guests)</strong> — Bilang ng tao kung saan magkakaroon ng espesyal na presyo. Ilagay ang <strong>0</strong> kung wala nito.<br>
  • <strong>Break Price (₱)</strong> — Ang espesyal na presyo kapag umabot sa Break Point. Ilagay ang <strong>0</strong> kung wala nito.<br>
  • <strong>Extra Per Guest (₱)</strong> — Dagdag na singil bawat tao kapag lumagpas sa Minimum Guests. Ilagay ang <strong>0</strong> kung fixed price lang.<br>
  • <strong>Max Total Price (₱)</strong> — Ang pinakamataas na kabuuang presyo — kahit gaano pa karami ang tao, hindi lalagpas dito.
</div>
    <h3>+ Add New Event Package</h3>

    <form id="addPackageForm" onsubmit="handleAddPackage(event)" enctype="multipart/form-data">
  <div class="form-row">
    <div class="form-group">
      <label for="pkgService">Service Name</label>
      <input type="text" id="pkgService" placeholder="e.g., Coffee Booth" required>
    </div>
    <div class="form-group">
      <label for="pkgTitle">Display Title</label>
      <input type="text" id="pkgTitle" placeholder="e.g., Coffee Booth Service" required>
    </div>
    <div class="form-group">
      <label for="pkgDisplayPrice">Display Price Text</label>
      <input type="text" id="pkgDisplayPrice" placeholder="e.g., ₱5,000 — Up to 50 cups" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="pkgBasePrice">Base Price (₱)</label>
      <input type="number" step="0.01" id="pkgBasePrice" placeholder="e.g., 3450" required>
    </div>
    <div class="form-group">
      <label for="pkgMinGuests">Min Guests</label>
      <input type="number" id="pkgMinGuests" placeholder="e.g., 30" required>
    </div>
    <div class="form-group">
      <label for="pkgMaxGuests">Max Guests</label>
      <input type="number" id="pkgMaxGuests" placeholder="e.g., 100" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="pkgBreakPoint">Break Point (Guests)</label>
      <input type="number" id="pkgBreakPoint" placeholder="e.g., 50" value="0">
    </div>
    <div class="form-group">
      <label for="pkgBreakPrice">Break Price (₱)</label>
      <input type="number" step="0.01" id="pkgBreakPrice" placeholder="e.g., 5000" value="0">
    </div>
    <div class="form-group">
      <label for="pkgExtraPerGuest">Extra Per Guest (₱)</label>
      <input type="number" step="0.01" id="pkgExtraPerGuest" placeholder="e.g., 80" required>
    </div>
    <div class="form-group">
      <label for="pkgMaxTotal">Max Total Price (₱)</label>
      <input type="number" step="0.01" id="pkgMaxTotal" placeholder="e.g., 5000" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="pkgStatus">Status</label>
      <select id="pkgStatus" required>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
    <div class="form-group">
      <label for="pkgImg">Upload Image</label>
      <input type="file" id="pkgImg" accept="image/*" required>
    </div>
  </div>

  <div class="form-group" style="width: 100%; margin-top: 10px;">
    <label for="pkgDesc">Description</label>
    <textarea id="pkgDesc" placeholder="Package inclusions and details..." rows="2" required></textarea>
  </div>

  <button type="submit" class="btn-add-item" style="margin-top: 15px;">Add Package</button>
</form>
  </div>

  <table class="admin-table">
    <thead>
  <tr>
    <th>Image</th>
    <th>Package Details</th>
    <th>Pricing Rules</th>
    <th>Status</th>
    <th>Action</th>
  </tr>
</thead>
    <tbody id="packagesTableBody">
      </tbody>
  </table>
</div>

  <div id="feedbackSection" class="admin-section">
    <div class="add-form-container">
      <h3>+ Add New Customer Feedback</h3>
      <form id="addFeedbackForm" onsubmit="handleAddFeedback(event)">
        <div class="form-row">
          <div class="form-group">
            <label for="fbName">Customer Name</label>
            <input type="text" id="fbName" placeholder="e.g., Maria Santos" required>
          </div>
          <div class="form-group">
            <label for="fbRating">Rating Stars</label>
            <select id="fbRating" required>
              <option value="★★★★★">★★★★★ (5 Stars)</option>
              <option value="★★★★☆">★★★★☆ (4 Stars)</option>
              <option value="★★★☆☆">★★★☆☆ (3 Stars)</option>
            </select>
          </div>
          <div class="form-group">
            <label for="fbPlatform">Platform / Source Tag</label>
            <input type="text" id="fbPlatform" placeholder="e.g., Facebook Review, Google Review" required>
          </div>
          <div class="form-group full-width">
            <label for="fbText">Testimonial Review Message Text</label>
            <textarea id="fbText" rows="3" placeholder="Paste or type customer feedback comment statement..." required></textarea>
          </div>
        </div>
        <button type="submit" class="btn-add-item">Publish Feedback</button>
      </form>
    </div>
    <table>
      <thead>
        <tr>
          <th>Author Details</th>
          <th>Review Content Statement</th>
          <th>Rating Info</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="feedbackTableBody"></tbody>
    </table>
  </div>

</div>

<script>
// ✅ Removed old localStorage check — auth_check.php already protects this page
let feedbacksData = [];

function switchTab(tabName, btnElement) {
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  document.querySelectorAll('.admin-section').forEach(sec => sec.classList.remove('active'));
  btnElement.classList.add('active');
  
  const publicLink = document.getElementById('publicLink');
  if(tabName === 'menu') {
    document.getElementById('menuSection').classList.add('active');
    publicLink.href = "Menu.html";
    publicLink.innerHTML = "&larr; Back to Public Menu";
  } else if (tabName === 'packages') {
    document.getElementById('packagesSection').classList.add('active');
    publicLink.href = "Package.php";
    publicLink.innerHTML = "&larr; Back to Public Packages";
  } else {
    document.getElementById('feedbackSection').classList.add('active');
    publicLink.href = "index.php";
    publicLink.innerHTML = "&larr; Back to Home Page";
  }
}

// ================= FOOD MENU FUNCTIONS =================
let mysqlMenuData = [];

function loadAdminTable() {
  const tbody = document.getElementById('adminTableBody');
  if(!tbody) return;
  tbody.innerHTML = '';

  fetch('website_php/process_menu.php')
    .then(res => res.json())
    .then(data => {
      mysqlMenuData = data;
      mysqlMenuData.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>
            <img id="img-preview-${index}" src="${item.image}" style="width:50px; height:50px; object-fit:cover;"><br>
            <input type="text" class="input-edit" id="input-img-${index}" value="${item.image}" disabled>
          </td>
          <td><strong>${item.name}</strong></td>
          <td>${item.category}</td>
          <td><input type="text" class="input-edit" id="input-price-${index}" value="${item.price}" disabled></td>
          <td>
            <button class="btn btn-edit" id="btn-edit-${index}" onclick="enableEdit(${index})">Edit</button>
            <button class="btn btn-save" id="btn-save-${index}" onclick="saveEdit(${index})" style="display:none;">Save</button>
            <button class="btn btn-delete" onclick="deleteMenuItem(${item.id})">Delete</button>
          </td>
        `;
        tbody.appendChild(row);
      });
    })
    .catch(err => console.error("Error loading menu table:", err));
}

function handleAddItem(event) {
  event.preventDefault();
  const formData = new FormData(document.getElementById('addItemForm'));
  // ✅ Fix: match field name expected by process_menu.php
  formData.append('image_file', formData.get('newImg'));
  formData.delete('newImg');

  fetch('website_php/process_menu.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'success') {
      alert('New menu item successfully added!');
      document.getElementById('addItemForm').reset();
      loadAdminTable();
    } else {
      alert('Error: ' + data.message);
    }
  });
}

function enableEdit(index) {
  document.getElementById(`input-img-${index}`).disabled = false;
  document.getElementById(`input-price-${index}`).disabled = false;
  document.getElementById(`btn-edit-${index}`).style.display = 'none';
  document.getElementById(`btn-save-${index}`).style.display = 'inline-block';
}

function saveEdit(index) {
  const formData = new FormData();
  formData.append('id', mysqlMenuData[index].id);
  formData.append('name', mysqlMenuData[index].name);
  formData.append('category', mysqlMenuData[index].category);
  formData.append('price', document.getElementById(`input-price-${index}`).value);
  formData.append('current_image', document.getElementById(`input-img-${index}`).value);

  fetch('website_php/process_menu.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'success') {
      alert('Menu item updated!');
      document.getElementById(`input-img-${index}`).disabled = true;
      document.getElementById(`input-price-${index}`).disabled = true;
      document.getElementById(`btn-edit-${index}`).style.display = 'inline-block';
      document.getElementById(`btn-save-${index}`).style.display = 'none';
      loadAdminTable();
    } else {
      alert('Update failed: ' + data.message);
    }
  });
}

// ✅ Add delete function for menu
function deleteMenuItem(id) {
  if (confirm('Delete this item permanently?')) {
    fetch('website_php/process_menu.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
      if(data.status === 'success') loadAdminTable();
      else alert('Delete failed: ' + data.message);
    });
  }
}

// ================= EVENT PACKAGES FUNCTIONS =================
let mysqlPackagesData = [];

function loadPackagesTable() {
  const tbody = document.getElementById('packagesTableBody');
  if(!tbody) return;
  tbody.innerHTML = '';

  fetch('website_php/process_packages.php')
    .then(res => res.json())
    .then(data => {
      mysqlPackagesData = data;
      mysqlPackagesData.forEach((pkg, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
          <td>
            <img src="${pkg.image}" style="width:70px; height:70px; object-fit:cover; border-radius:6px;"><br>
            <input type="file" class="input-edit" id="edit-pkg-img-${index}" accept="image/*" disabled>
          </td>
          <td>
            <strong>${pkg.title}</strong><br>
            <small>Service: ${pkg.service_name}</small><br>
            <textarea class="textarea-edit" id="edit-pkg-desc-${index}" rows="2" disabled>${pkg.description}</textarea>
          </td>
          <td style="font-size:12px;">
            Base: ₱${pkg.base_price} | Min:${pkg.min_guests} Max:${pkg.max_guests}<br>
            Break:${pkg.break_point} → ₱${pkg.break_price}<br>
            Extra: ₱${pkg.extra_per_guest}/guest | Max: ₱${pkg.max_total}
          </td>
          <td>
            <select class="input-edit" id="edit-pkg-status-${index}" disabled>
              <option value="active" ${pkg.status === 'active' ? 'selected' : ''}>Active</option>
              <option value="inactive" ${pkg.status === 'inactive' ? 'selected' : ''}>Inactive</option>
            </select>
          </td>
          <td>
            <button class="btn btn-edit" id="btn-pkg-edit-${index}" onclick="enablePkgEdit(${index})">Edit</button>
            <button class="btn btn-save" id="btn-pkg-save-${index}" onclick="savePkgEdit(${index})" style="display:none;">Save</button>
            <button class="btn btn-delete" onclick="deletePackageItem(${pkg.id})">Delete</button>
          </td>
        `;
        tbody.appendChild(row);
      });
    })
    .catch(err => console.error("Error loading packages:", err));
}

function handleAddPackage(event) {
  event.preventDefault();
  const formData = new FormData();
  
  formData.append('service_name', document.getElementById('pkgService').value);
  formData.append('title', document.getElementById('pkgTitle').value);
  formData.append('display_price', document.getElementById('pkgDisplayPrice').value);
  formData.append('base_price', document.getElementById('pkgBasePrice').value);
  formData.append('min_guests', document.getElementById('pkgMinGuests').value);
  formData.append('max_guests', document.getElementById('pkgMaxGuests').value);
  formData.append('break_point', document.getElementById('pkgBreakPoint').value);
  formData.append('break_price', document.getElementById('pkgBreakPrice').value);
  formData.append('extra_per_guest', document.getElementById('pkgExtraPerGuest').value);
  formData.append('max_total', document.getElementById('pkgMaxTotal').value);
  formData.append('status', document.getElementById('pkgStatus').value);
  formData.append('description', document.getElementById('pkgDesc').value);
  formData.append('package_file', document.getElementById('pkgImg').files[0]);

  fetch('website_php/process_packages.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'success') {
      alert('New event package added!');
      document.getElementById('addPackageForm').reset();
      loadPackagesTable();
    } else {
      alert('Error: ' + data.message);
    }
  });
}

function enablePkgEdit(index) {
  // Enable all fields
  document.getElementById(`edit-pkg-img-${index}`).disabled = false;
  document.getElementById(`edit-pkg-desc-${index}`).disabled = false;
  document.getElementById(`edit-pkg-status-${index}`).disabled = false;
  
  // Add dynamic edit fields with CLEAR LABELS
  const row = document.querySelectorAll('#packagesTableBody tr')[index];
  const pricingCell = row.cells[2];
  pricingCell.innerHTML = `
    <div style="margin-bottom:5px;">
      <small><strong>Base Price (₱):</strong></small><br>
      <input type="number" step="0.01" class="input-edit" id="edit-base-${index}" value="${mysqlPackagesData[index].base_price}" style="width:100%;">
    </div>
    <div style="margin-bottom:5px;">
      <small><strong>Minimum Guests:</strong></small><br>
      <input type="number" class="input-edit" id="edit-min-${index}" value="${mysqlPackagesData[index].min_guests}" style="width:100%;">
    </div>
    <div style="margin-bottom:5px;">
      <small><strong>Maximum Guests:</strong></small><br>
      <input type="number" class="input-edit" id="edit-max-${index}" value="${mysqlPackagesData[index].max_guests}" style="width:100%;">
    </div>
    <div style="margin-bottom:5px;">
      <small><strong>Break Point (Guests):</strong></small><br>
      <input type="number" class="input-edit" id="edit-breakAt-${index}" value="${mysqlPackagesData[index].break_point}" style="width:100%;">
    </div>
    <div style="margin-bottom:5px;">
      <small><strong>Break Price (₱):</strong></small><br>
      <input type="number" step="0.01" class="input-edit" id="edit-breakPrice-${index}" value="${mysqlPackagesData[index].break_price}" style="width:100%;">
    </div>
    <div style="margin-bottom:5px;">
      <small><strong>Extra Per Guest (₱):</strong></small><br>
      <input type="number" step="0.01" class="input-edit" id="edit-extra-${index}" value="${mysqlPackagesData[index].extra_per_guest}" style="width:100%;">
    </div>
    <div>
      <small><strong>Max Total Price (₱):</strong></small><br>
      <input type="number" step="0.01" class="input-edit" id="edit-maxTotal-${index}" value="${mysqlPackagesData[index].max_total}" style="width:100%;">
    </div>
  `;

  document.getElementById(`btn-pkg-edit-${index}`).style.display = 'none';
  document.getElementById(`btn-pkg-save-${index}`).style.display = 'inline-block';
}

function savePkgEdit(index) {
  const formData = new FormData();
  formData.append('id', mysqlPackagesData[index].id);
  formData.append('service_name', mysqlPackagesData[index].service_name);
  formData.append('title', mysqlPackagesData[index].title);
  formData.append('display_price', mysqlPackagesData[index].display_price);
  formData.append('base_price', document.getElementById(`edit-base-${index}`).value);
  formData.append('min_guests', document.getElementById(`edit-min-${index}`).value);
  formData.append('max_guests', document.getElementById(`edit-max-${index}`).value);
  formData.append('break_point', document.getElementById(`edit-breakAt-${index}`).value);
  formData.append('break_price', document.getElementById(`edit-breakPrice-${index}`).value);
  formData.append('extra_per_guest', document.getElementById(`edit-extra-${index}`).value);
  formData.append('max_total', document.getElementById(`edit-maxTotal-${index}`).value);
  formData.append('status', document.getElementById(`edit-pkg-status-${index}`).value);
  formData.append('description', document.getElementById(`edit-pkg-desc-${index}`).value);
  formData.append('current_image', mysqlPackagesData[index].image);
  
  const imgInput = document.getElementById(`edit-pkg-img-${index}`);
  if (imgInput.files.length > 0) {
    formData.append('package_file', imgInput.files[0]);
  }

  fetch('website_php/process_packages.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === 'success') {
      alert('Package updated successfully!');
      loadPackagesTable();
    } else {
      alert('Update failed: ' + data.message);
    }
  });
}

function deletePackageItem(id) {
  if (confirm('Delete this package permanently?')) {
    fetch('website_php/process_packages.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
      if(data.status === 'success') loadPackagesTable();
      else alert('Delete failed: ' + data.message);
    });
  }
}


// ================= CUSTOMER FEEDBACK FUNCTIONS =================
// ✅ Will connect to database once you create process_feedback.php
function loadFeedbackTable() {
  const tbody = document.getElementById('feedbackTableBody');
  tbody.innerHTML = '';
  feedbacksData.forEach((fb, index) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>
        <strong>Name:</strong> <input type="text" class="input-edit" id="edit-fb-name-${index}" value="${fb.name}" disabled><br>
        <strong>Tag:</strong> <input type="text" class="input-edit" id="edit-fb-plat-${index}" value="${fb.platform}" disabled>
      </td>
      <td><textarea class="textarea-edit" id="edit-fb-text-${index}" rows="3" disabled>${fb.text}</textarea></td>
      <td><strong>${fb.rating}</strong></td>
      <td>
        <button class="btn btn-edit" onclick="enableFbEdit(${index})">Edit</button>
        <button class="btn btn-save" onclick="saveFbEdit(${index})" style="display:none;">Save</button>
        <button class="btn btn-delete" onclick="deleteFeedback(${index})">Delete</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

function handleAddFeedback(event) {
  event.preventDefault();
  const name = document.getElementById('fbName').value;
  const rating = document.getElementById('fbRating').value;
  const platform = document.getElementById('fbPlatform').value;
  const text = document.getElementById('fbText').value;

  feedbacksData.push({ name, rating, platform, text });
  loadFeedbackTable();
  document.getElementById('addFeedbackForm').reset();
  alert('Feedback saved! (Will connect to database next)');
}

function enableFbEdit(index) {
  document.getElementById(`edit-fb-name-${index}`).disabled = false;
  document.getElementById(`edit-fb-plat-${index}`).disabled = false;
  document.getElementById(`edit-fb-text-${index}`).disabled = false;
  document.getElementById(`btn-fb-edit-${index}`).style.display = 'none';
  document.getElementById(`btn-fb-save-${index}`).style.display = 'inline-block';
}

function saveFbEdit(index) {
  feedbacksData[index].name = document.getElementById(`edit-fb-name-${index}`).value;
  feedbacksData[index].platform = document.getElementById(`edit-fb-plat-${index}`).value;
  feedbacksData[index].text = document.getElementById(`edit-fb-text-${index}`).value;
  loadFeedbackTable();
  alert('Feedback updated!');
}

function deleteFeedback(index) {
  if (confirm('Delete this review?')) {
    feedbacksData.splice(index, 1);
    loadFeedbackTable();
  }
}

// ✅ Logout clears session properly
function handleLogout() {
  window.location.href = 'website_php/logout_admin.php';
}

document.addEventListener("DOMContentLoaded", () => {
  loadAdminTable();
  loadPackagesTable();
  loadFeedbackTable();
});
</script>
</body>
</html>
