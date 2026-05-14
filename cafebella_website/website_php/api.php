<?php
header('Content-Type: application/json');
require_once 'database.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$input = json_decode(file_get_contents('php://input'), true);

/************************** API FUNCTIONS ******************************/

// --- ADD NEW CATEGORY ---
if ($action == 'addCategory') {
    try {
        $category_name = $_POST['category_name'] ?? '';
        if (empty($category_name)) {
            echo json_encode(['status' => 'error', 'message' => 'Name is required']);
            exit;
        }
        $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->execute([$category_name]);
        $new_id = $pdo->lastInsertId();
        echo json_encode([
            'status' => 'success',
            'message' => 'Category added',
            'data' => [
                'category_id' => $new_id,
                'category_name' => $category_name
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ADD NEW PRODUCT ---
if ($action == 'addProduct') {
    try {
        $category_id   = $_POST['category_id'] ?? '';
        $product_name  = $_POST['product_name'] ?? '';
        $base_price    = $_POST['price'] ?? '0.00';
        
        // ✅ DITO ANG GUSTO MO:
        // Kung WALANG PINILING LITRATO = NULL
        // Kung MAY PINILI = ANG PATH NG LITRATO
        $product_image = isset($_POST['product_image']) && $_POST['product_image'] !== 'IMAGES/POS_image/foodpic.jpg' 
                        ? $_POST['product_image'] 
                        : null;

        $variants_json = $_POST['variants'] ?? '[]';
        $variants      = json_decode($variants_json, true);

        if (empty($category_id) || empty($product_name)) {
            echo json_encode(['status' => 'error', 'message' => '⚠️ Category and Product Name are required!']);
            exit;
        }

        $base_price = is_numeric($base_price) ? $base_price : 0;
        if ($base_price < 0) $base_price = 0;

        $has_variant = (!empty($variants) && is_array($variants)) ? 1 : 0;

        $stmt = $pdo->prepare("INSERT INTO products (category_id, product_name, base_price, product_image, has_variant) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $product_name, $base_price, $product_image, $has_variant]);
        $product_id = $pdo->lastInsertId();

        if (!empty($variants) && is_array($variants)) {
            $stmtVar = $pdo->prepare("INSERT INTO product_variants (product_id, variant_name, price) VALUES (?, ?, ?)");
            foreach ($variants as $var) {
                if (!empty($var['name']) && isset($var['price']) && is_numeric($var['price']) && $var['price'] >= 0) {
                    $stmtVar->execute([$product_id, $var['name'], $var['price']]);
                }
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => '✅ Product added successfully!',
            'data' => ['product_id' => $product_id]
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- GET CATEGORIES ---
if ($action == 'getCategories') {
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
        $categories = $stmt->fetchAll();
        echo json_encode([
            'status' => 'success',
            'data' => $categories
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ✅ GET PRODUCTS (ITO ANG SAKTO SA GUSTO MO!) ---
if ($action == 'getProducts') {
    $cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;
    
    try {
        if ($cat_id == 0) {
            $stmt = $pdo->query("SELECT * FROM products ORDER BY product_name ASC");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY product_name ASC");
            $stmt->execute([$cat_id]);
        }
        
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$prod) {
            // ✅ ANG PINAKA-GUSTO MO:
            // KUNG NULL o WALANG LAMAN → AUTOMATIC DEFAULT IMAGE
            if(empty($prod['product_image']) || $prod['product_image'] === null || $prod['product_image'] === "") {
                $prod['product_image'] = 'IMAGES/POS_image/foodpic.jpg';
            }

            // ✅ KUNIN ANG MGA VARIANTS
            $stmtVar = $pdo->prepare("SELECT variant_name, price FROM product_variants WHERE product_id = ? ORDER BY variant_id ASC");
            $stmtVar->execute([$prod['product_id']]);
            $prod['variants'] = $stmtVar->fetchAll(PDO::FETCH_ASSOC);

            // ✅ WALANG MIN/MAX — GAYA NG GUSTO MO
            if (!empty($prod['variants'])) {
                $prod['price'] = null;
                $prod['has_variant'] = 1;
            } else {
                $prod['price'] = $prod['base_price'];
                $prod['has_variant'] = 0;
            }
        }

        echo json_encode([
            'status' => 'success',
            'data' => $products
        ], JSON_UNESCAPED_SLASHES);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- UPLOAD IMAGE ---
if ($action == 'uploadImage') {
    try {
        if (!isset($_FILES['image'])) throw new Exception("No file uploaded");

        $file = $_FILES['image'];
        $targetDir = "IMAGES/POS_image/";
        $filename = time() . "_" . basename($file["name"]);
        $targetPath = $targetDir . $filename;

        $ext = strtolower(pathinfo($targetPath,PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if (!in_array($ext, $allowed)) throw new Exception("Invalid image type");

        if (move_uploaded_file($file["tmp_name"], $targetPath)) {
            echo json_encode([
                'status' => 'success',
                'path' => $targetPath
            ]);
        } else {
            throw new Exception("Upload failed");
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => '❌ Invalid action']);
?>