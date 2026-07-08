<?php
header('Content-Type: application/json');
require_once '../../website_php/database.php';

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
        $input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;

$category_id = intval($input['category_id'] ?? 0);
$product_name = trim($input['product_name'] ?? '');
$base_price = isset($input['price']) && is_numeric($input['price']) ? floatval($input['price']) : 0.00;
$product_image = !empty($input['product_image']) && $input['product_image'] !== 'IMAGES/POS_image/foodpic.jpg' ? $input['product_image'] : null;

// Kunin direkta ang array, hindi na bilang JSON string
$variants = $input['variants'] ?? [];
$ingredients = $input['ingredients'] ?? [];

if (!is_array($variants)) $variants = [];
if (!is_array($ingredients)) $ingredients = [];
// ✅ MAGDAGDAG NG PAGSUSURI PARA MAKITA KUNG MAY DUMATING
error_log("Variants: " . print_r($variants, true));
error_log("Ingredients: " . print_r($ingredients, true));

        // ✅ VALIDATION
if (empty($category_id) || empty($product_name)) {
    echo json_encode(['status' => 'error', 'message' => '⚠️ Category and Product Name are required!']);
    exit;
}

// ✅ Allow either base price OR variants
if (!is_array($variants) || empty($variants)) {
    // Walang variants → kailangan ng base price na mas malaki sa 0
    if (!is_numeric($base_price) || $base_price <= 0) {
        echo json_encode(['status' => 'error', 'message' => '❌ Enter Base Price OR add at least one Variant!']);
        exit;
    }
    $has_variant = 0;
} else {
    // May variants → hindi na kailangan ng base price, i-set lang sa 0
    foreach ($variants as $var) {
        if (empty($var['variant_name']) || empty($var['price']) || !is_numeric($var['price']) || $var['price'] <= 0) {
            echo json_encode(['status' => 'error', 'message' => '❌ Complete all Variant Names and Prices before saving!']);
            exit;
        }
    }
    $base_price = 0; // gamitin natin ito sa UPDATE, hindi ang $price
    $has_variant = 1;
}

        // ✅ SAVE PRODUCT
        $stmt = $pdo->prepare("INSERT INTO products (category_id, product_name, base_price, product_image, has_variant) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $product_name, $base_price, $product_image, $has_variant]);
        $product_id = $pdo->lastInsertId();

// ✅ SAVE NEW VARIANTS ONLY ONCE
$stmtVar = $pdo->prepare("INSERT INTO product_variants (product_id, variant_name, price) VALUES (?, ?, ?)");
$variantIds = [];
$seenVariants = []; // Prevent duplicate same-name variants

foreach ($variants as $var) {
    $vName = trim($var['variant_name']);
    if (empty($vName) || in_array($vName, $seenVariants)) continue; // Skip empty or duplicate

    $stmtVar->execute([$product_id, $vName, $var['price']]);
    $variantIds[$vName] = $pdo->lastInsertId();
    $seenVariants[] = $vName;
}

        // ✅ SAVE NEW INGREDIENTS
$stmtIng = $pdo->prepare("INSERT INTO product_ingredients (product_id, variant_id, inventory_id, quantity_needed, unit) VALUES (?, ?, ?, ?, ?)");
foreach ($ingredients as $ing) {
    // Siguraduhin na tama ang pagkuha ng Variant ID
    $vid = null;
    if (!empty($ing['variant_name']) && isset($variantIds[$ing['variant_name']])) {
        $vid = intval($variantIds[$ing['variant_name']]);
    }

    // Siguraduhin na may laman ang mahahalagang datos bago ipasok
    if (!empty($ing['inventory_id']) && is_numeric($ing['quantity_needed']) && $ing['quantity_needed'] > 0) {
        $stmtIng->execute([
            $product_id,
            $vid,
            intval($ing['inventory_id']),
            floatval($ing['quantity_needed']),
            trim($ing['unit'])
        ]);
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

// --- SAVE MENU (FULL SYNC) ---
if ($action == 'saveMenu') {
    try {
        $input = json_decode(file_get_contents("php://input"), true);
        $categories = $input['categories'] ?? [];
        $menu = $input['menu'] ?? [];

        $pdo->beginTransaction();

        // Update only existing categories instead of deleting all
        foreach ($categories as $cat) {
            if (!empty($cat['category_id'])) {
                $stmt = $pdo->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
                $stmt->execute([$cat['category_name'], $cat['category_id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
                $stmt->execute([$cat['category_name']]);
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Menu saved successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
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

// --- ✅ GET PRODUCTS ---
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
            if (empty($prod['product_image']) || $prod['product_image'] === null || $prod['product_image'] === "") {
                $prod['product_image'] = 'IMAGES/POS_image/foodpic.jpg';
            }

            // Get Variants
            $stmtVar = $pdo->prepare("SELECT variant_id, variant_name, price FROM product_variants WHERE product_id = ? ORDER BY variant_id ASC");
            $stmtVar->execute([$prod['product_id']]);
            $prod['variants'] = $stmtVar->fetchAll(PDO::FETCH_ASSOC);

            // ✅ GET INGREDIENTS
            $stmtIng = $pdo->prepare("
            SELECT pi.*, i.item_name, i.unit as inv_unit
            FROM product_ingredients pi
            JOIN inventory i ON pi.inventory_id = i.id
            WHERE pi.product_id = ? ORDER BY pi.id ASC
                "); 
$stmtIng->execute([$prod['product_id']]);
$prod['ingredients'] = $stmtIng->fetchAll(PDO::FETCH_ASSOC);

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
        $targetDir = __DIR__ . "/../IMAGES/POS_image/";
        $filename = time() . "_" . basename($file["name"]);
        $targetPath = $targetDir . $filename;
        $ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($ext, $allowed)) throw new Exception("Invalid image type");

        if (move_uploaded_file($file["tmp_name"], $targetPath)) {
            echo json_encode([
                'status' => 'success',
                'path' => "IMAGES/POS_image/" . $filename
            ]);
        } else {
            throw new Exception("Upload failed");
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- DELETE CATEGORY ---
if ($action == 'deleteCategory') {
    try {
        $id = $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'Missing ID']);
            exit;
        }

        $pdo->beginTransaction();

        // delete variants first
        $stmt = $pdo->prepare("
        DELETE pi FROM product_ingredients pi
        INNER JOIN products p ON pi.product_id = p.product_id
        WHERE p.category_id = ?
        ");
        $stmt->execute([$id]);

        // delete products
        $stmt = $pdo->prepare("DELETE FROM products WHERE category_id = ?");
        $stmt->execute([$id]);

        // delete category
        $stmt = $pdo->prepare("DELETE FROM categories WHERE category_id = ?");
        $stmt->execute([$id]);

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => 'Category deleted']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- DELETE PRODUCT ---
if ($action == 'deleteProduct') {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(['status' => 'error', 'message' => 'Missing ID']);
        exit;
    }

    // ✅ Delete ingredients first
    $pdo->prepare("DELETE FROM product_ingredients WHERE product_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM products WHERE product_id = ?")->execute([$id]);

    echo json_encode(['status' => 'success']);
    exit;
}

//UPDATE PRODUCT
if ($action == "updateProductFull") {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }

    $product_id   = intval($input['product_id'] ?? 0);
    $category_id  = intval($input['category_id'] ?? 0);
    $product_name = trim($input['product_name'] ?? '');
    $price        = floatval($input['price'] ?? 0);
    $image        = !empty($input['product_image']) && $input['product_image'] !== 'IMAGES/POS_image/foodpic.jpg' ? trim($input['product_image']) : null;
    $variants     = is_array($input['variants'] ?? null) ? $input['variants'] : [];
    $ingredients  = is_array($input['ingredients'] ?? null) ? $input['ingredients'] : [];

    // ✅ BASIC CHECK
    if ($product_id <= 0 || $category_id <= 0 || $product_name === '') {
        echo json_encode(['status' => 'error', 'message' => '❌ Product ID, Category and Name cannot be empty!']);
        exit;
    }

    // ✅ VALIDATE PRESYO O VARIANT
    if (empty($variants)) {
        if ($price <= 0) {
            echo json_encode(['status' => 'error', 'message' => '❌ Enter Base Price or add Variant!']);
            exit;
        }
        $has_variant = 0;
        $final_price = $price;
    } else {
        foreach ($variants as $v) {
            if (trim($v['variant_name'] ?? '') === '' || floatval($v['price'] ?? 0) <= 0) {
                echo json_encode(['status' => 'error', 'message' => '❌ Fill all Variant names and prices!']);
                exit;
            }
        }
        $final_price = 0;
        $has_variant = 1;
    }

    $pdo->beginTransaction();
    try {
        // 1. ✅ I-UPDATE ANG PANGUNAHING PRODUKTO — HINDI NAGBABAGO ANG PRODUCT ID
        $stmt = $pdo->prepare("UPDATE products 
                               SET category_id = ?, product_name = ?, base_price = ?, product_image = ?, has_variant = ? 
                               WHERE product_id = ?");
        $stmt->execute([$category_id, $product_name, $final_price, $image, $has_variant, $product_id]);

        // 2. ✅ BURAHIN LANG ANG MGA INGREDIENTS — HINDI ANG MGA VARIANT!
        $pdo->prepare("DELETE FROM product_ingredients WHERE product_id = ?")->execute([$product_id]);

        // 3. ✅ KUNIN ANG LAHAT NG UMIIRAL NA VARIANT AT ANG KANILANG TUNAY NA ID
        $existingVariants = [];
        $stmtGet = $pdo->prepare("SELECT variant_id, variant_name FROM product_variants WHERE product_id = ?");
        $stmtGet->execute([$product_id]);
        while ($row = $stmtGet->fetch(PDO::FETCH_ASSOC)) {
            $existingVariants[trim($row['variant_name'])] = $row['variant_id'];
        }

        $currentVariantIds = [];
        foreach ($variants as $var) {
            $vName = trim($var['variant_name']);
            $vPrice = floatval($var['price']);

            if (isset($existingVariants[$vName])) {
                // ✅ GAMITIN ANG LUMANG ID — WALANG BAGONG VARIANT NA GAGAWA
                $stmtUpd = $pdo->prepare("UPDATE product_variants SET price = ? WHERE variant_id = ?");
                $stmtUpd->execute([$vPrice, $existingVariants[$vName]]);
                $currentVariantIds[$vName] = $existingVariants[$vName];
                unset($existingVariants[$vName]); // Tanggalin para malaman kung may tinanggal na variant
            } else {
                // ✅ BAGONG VARIANT LANG DITO IDADAGDAG
                $stmtIns = $pdo->prepare("INSERT INTO product_variants (product_id, variant_name, price) VALUES (?, ?, ?)");
                $stmtIns->execute([$product_id, $vName, $vPrice]);
                $currentVariantIds[$vName] = $pdo->lastInsertId();
            }
        }

        // ✅ BURAHIN LANG ANG MGA VARIANT NA TINANGGAL MO SA LISTAHAN
        foreach ($existingVariants as $del_id) {
            $pdo->prepare("DELETE FROM product_variants WHERE variant_id = ?")->execute([$del_id]);
        }

        // 4. ✅ IDUGTONG ANG INGREDIENT SA TAMANG VARIANT ID — GAMIT ANG UMIIRAL NA ID
        $stmtIng = $pdo->prepare("INSERT INTO product_ingredients (product_id, variant_id, inventory_id, quantity_needed, unit) 
                                   VALUES (?, ?, ?, ?, ?)");

        foreach ($ingredients as $ing) {
            $vid = null;
            $ingVarName = trim($ing['variant_name'] ?? '');
            if ($ingVarName !== '' && isset($currentVariantIds[$ingVarName])) {
                $vid = $currentVariantIds[$ingVarName]; // ✅ DITO TAMA — GAMIT ANG EXISTING ID
            }

            $invId = intval($ing['inventory_id'] ?? 0);
            $qty = floatval($ing['quantity_needed'] ?? 0);
            if ($invId > 0 && $qty > 0) {
                $stmtIng->execute([
                    $product_id,
                    $vid,
                    $invId,
                    $qty,
                    trim($ing['unit'] ?? '')
                ]);
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => '✅ Updated correctly — same Product & Variant IDs used']);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => '❌ ' . $e->getMessage()]);
    }
    exit;
}

// --- BAWASAN ANG STOCK PAG MAY BENTA ---
if ($action == 'deductInventoryStock') {
    try {
        $pdo->beginTransaction();

        // ✅ AYUSIN ANG PAGBASA NG DUMATING NA DATA (JSON string)
        $itemsJson = $_POST['items'] ?? '';
        $items = json_decode($itemsJson, true);

        if (!is_array($items)) {
            throw new Exception("❌ No order items received.");
        }

        foreach ($items as $orderItem) {
            $product_id = intval($orderItem['product_id'] ?? 0);
            $variant_id = isset($orderItem['variant_id']) && $orderItem['variant_id'] !== '' ? intval($orderItem['variant_id']) : null;
            $qty_sold = floatval($orderItem['qty'] ?? 0);

            if ($product_id <= 0 || $qty_sold <= 0) {
                continue; // Skip invalid items
            }

            // Get ingredients: if variant exists, use only variant-specific; else use base ingredients
            $stmt = $pdo->prepare("
                SELECT pi.inventory_id, pi.quantity_needed, i.item_name, i.stock
                FROM product_ingredients pi
                JOIN inventory i ON pi.inventory_id = i.id
                WHERE pi.product_id = ? 
                  AND pi.variant_id <=> ?
            ");
            $stmt->execute([$product_id, $variant_id]);
            $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($ingredients)) {
                // Optional: warn but don't fail if no ingredients set yet
                continue;
            }

            // Ibawas ang dami sa inventory
            foreach ($ingredients as $ing) {
                $deduct = round($ing['quantity_needed'] * $qty_sold, 3);

                if ($deduct <= 0) continue;

                if ($ing['stock'] < $deduct) {
                    throw new Exception("❌ Not enough stock for: {$ing['item_name']} (Available: {$ing['stock']}, Needed: {$deduct})");
                }

                $update = $pdo->prepare("
                    UPDATE inventory
                    SET stock = stock - ?
                    WHERE id = ? AND stock >= ?
                ");
                $update->execute([$deduct, $ing['inventory_id'], $deduct]);

                if ($update->rowCount() === 0) {
                    throw new Exception("❌ Kulang ang stock o hindi mahanap ang sangkap (ID: " . $ing['inventory_id'] . ")");
                }
            }
        }

        $pdo->commit();
        echo json_encode(['status' => 'success', 'message' => '✅ Order completed and stock updated!']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action == 'getInventoryStock') {
    try {
        // ✅ Gumamit ng tamang column na pangalan: stock
        $stmt = $pdo->query("SELECT id, item_name, unit, stock FROM inventory ORDER BY item_name ASC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- CHECK LOW STOCK ITEMS ---
if ($action == 'getLowStockItems') {
    try {
        $threshold = 10;
        $stmt = $pdo->prepare("SELECT id, item_name, unit, stock FROM inventory WHERE stock < ? ORDER BY stock ASC");
        $stmt->execute([$threshold]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- TRANSACTION PART START ---
// --- ✅ SAVE NEW ORDER ---
if ($action == 'saveOrder') {
    try {
        $pdo->beginTransaction();

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) throw new Exception("Invalid order data");

        // Gumawa ng natatanging Receipt Code
        $receiptCode = 'RCP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

        // Kunin ang mga detalye
        $orderType = $input['order_type'] ?? 'Dine In';
        $customerName = trim($input['customer_name'] ?? 'Walk-in');
        if ($customerName === '') $customerName = 'Walk-in'; // Fallback if empty

        $subtotal = floatval($input['subtotal'] ?? 0);
        $discountPercent = floatval($input['discount_percent'] ?? 0);
        $discountAmount = floatval($input['discount_amount'] ?? 0);
        $total = floatval($input['total_amount'] ?? 0);
        $paymentMethod = $input['payment_method'] ?? '';
        $amountReceived = floatval($input['amount_received'] ?? 0);
        $change = floatval($input['change_amount'] ?? 0);
        $items = $input['items'] ?? [];

        if (empty($paymentMethod) || $total <= 0 || empty($items)) {
            throw new Exception("❌ Missing required order details");
        }

        // Ipasok sa orders table — ADDED customer_name
        $stmt = $pdo->prepare("
            INSERT INTO orders 
            (receipt_code, order_type, customer_name, subtotal, discount_percent, discount_amount, payment_method, total_amount, amount_received, change_amount)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $receiptCode,
            $orderType,
            $customerName,
            $subtotal,
            $discountPercent,
            $discountAmount,
            $paymentMethod,
            $total,
            $amountReceived,
            $change
        ]);

        $orderId = $pdo->lastInsertId();

        // Ipasok ang mga items — UNCHANGED
        $stmtItem = $pdo->prepare("
            INSERT INTO order_items 
            (order_id, product_id, variant_id, variant_name, product_name, quantity, unit_price, subtotal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($items as $item) {
            $stmtItem->execute([
                $orderId,
                $item['product_id'],
                $item['variant_id'] ?? null,
                $item['variant_name'] ?? null,
                $item['product_name'],
                $item['qty'],
                $item['price'],
                $item['total']
            ]);
        }

        $pdo->commit();
        echo json_encode([
            'status' => 'success',
            'message' => "✅ Order saved! Receipt #: $receiptCode",
            'order_id' => $orderId,
            'receipt_code' => $receiptCode,
            'customer_name' => $customerName
        ]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ✅ GET ALL TRANSACTIONS ---
if ($action == 'getTransactions') {
    try {
        $stmt = $pdo->query("
            SELECT order_id, receipt_code, order_type, customer_name, order_date, payment_method, total_amount, status 
            FROM orders 
            ORDER BY order_date DESC
        ");
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'data' => $transactions
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

// --- ✅ GET DETAILS OF ONE TRANSACTION ---
if ($action == 'getTransactionDetails') {
    try {
        $orderId = intval($_GET['order_id'] ?? 0);
        if ($orderId <= 0) throw new Exception("Invalid Order ID");

        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) throw new Exception("Order not found");

        $stmtItems = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmtItems->execute([$orderId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'status' => 'success',
            'order' => $order,
            'items' => $items
        ]);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
// --- TRANSACTION PART END ---

// --- INVALID ACTION ---
echo json_encode(['status' => 'error', 'message' => '❌ Invalid action']);
?>
