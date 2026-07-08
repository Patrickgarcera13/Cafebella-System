<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../../website_php/database.php';

header('Content-Type: application/json');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kunin lahat ng items
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare("SELECT * FROM inventory ORDER BY item_name ASC");
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["status" => "success", "data" => $items]);
        exit;
    }

    // Magdagdag ng bagong item
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $item_name = trim($_POST['item_name'] ?? '');
        $categories = trim($_POST['categories'] ?? '');
        $supplier = trim($_POST['supplier'] ?? '');
        $unit = trim($_POST['unit'] ?? '');
        $stock = intval($_POST['stock'] ?? 0);
        $cost = floatval($_POST['cost'] ?? 0);
        $reorder_level = intval($_POST['reorder_level'] ?? 0);

        if (empty($item_name)) {
            echo json_encode(["status" => "error", "message" => "Item name is required"]);
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO inventory (item_name, categories, supplier, unit, stock, cost, reorder_level)
            VALUES (:item_name, :categories, :supplier, :unit, :stock, :cost, :reorder_level)
        ");
        $stmt->execute([
            ':item_name' => $item_name,
            ':categories' => $categories,
            ':supplier' => $supplier,
            ':unit' => $unit,
            ':stock' => $stock,
            ':cost' => $cost,
            ':reorder_level' => $reorder_level
        ]);

        echo json_encode(["status" => "success", "message" => "Item added successfully"]);
        exit;
    }

    // I-update ang item
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        parse_str(file_get_contents("php://input"), $putData);
        $id = intval($putData['id'] ?? 0);
        $item_name = trim($putData['item_name'] ?? '');
        $categories = trim($putData['categories'] ?? '');
        $supplier = trim($putData['supplier'] ?? '');
        $unit = trim($putData['unit'] ?? '');
        $stock = intval($putData['stock'] ?? 0);
        $cost = floatval($putData['cost'] ?? 0);
        $reorder_level = intval($putData['reorder_level'] ?? 0);

        if ($id <= 0 || empty($item_name)) {
            echo json_encode(["status" => "error", "message" => "Invalid data"]);
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE inventory
            SET item_name = :item_name, categories = :categories, supplier = :supplier,
                unit = :unit, stock = :stock, cost = :cost, reorder_level = :reorder_level
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $id,
            ':item_name' => $item_name,
            ':categories' => $categories,
            ':supplier' => $supplier,
            ':unit' => $unit,
            ':stock' => $stock,
            ':cost' => $cost,
            ':reorder_level' => $reorder_level
        ]);

        echo json_encode(["status" => "success", "message" => "Item updated successfully"]);
        exit;
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    exit;
}

//POS SIDE

// --------------------------
// BAWASAN ANG STOCK PAG MAY BENTA SA POS
// --------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deduct_stock') {
    try {
        $pdo->beginTransaction();

        $product_id = intval($_POST['product_id'] ?? 0);
        $variant_id = !empty($_POST['variant_id']) ? intval($_POST['variant_id']) : null;
        $quantity_sold = floatval($_POST['quantity'] ?? 1);

        // Kunin lahat ng sangkap na kailangan
        $sql = "SELECT pi.inventory_id, pi.quantity_needed
                FROM product_ingredients pi
                WHERE pi.product_id = :product_id
                  AND (pi.variant_id = :variant_id OR pi.variant_id IS NULL)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':product_id' => $product_id,
            ':variant_id' => $variant_id
        ]);
        $ingredients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($ingredients)) {
            throw new Exception("Walang sangkap na nakatala para sa produktong ito.");
        }

        // Ibawas ang dami sa imbentaryo
        foreach ($ingredients as $ing) {
            $deduct_amount = $ing['quantity_needed'] * $quantity_sold;

            $update = $pdo->prepare("UPDATE inventory 
                                     SET stock = stock - :deduct 
                                     WHERE id = :id AND stock >= :deduct");
            $update->execute([
                ':deduct' => $deduct_amount,
                ':id' => $ing['inventory_id']
            ]);

            if ($update->rowCount() === 0) {
                throw new Exception("Hindi sapat ang stock para sa isa o higit pang sangkap.");
            }
        }

        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Stock nabawasan matagumpay"]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}
?>
