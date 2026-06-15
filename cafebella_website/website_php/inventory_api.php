<?php
require 'database.php';

$action = $_POST['action'] ?? '';

/* ================= GET ALL ITEMS ================= */
if ($action == "fetch") {
    $stmt = $pdo->query("SELECT * FROM inventory ORDER BY id DESC");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as &$item) {
        $stock_val = (int)$item['stock'];
        $reorder_val = (int)$item['reorder_level'];

        if ($stock_val == 0) {
            $item['status_text'] = "Out of Stock";
            $item['status_class'] = "out";
        } elseif ($stock_val <= $reorder_val) {
            $item['status_text'] = "Low Stock";
            $item['status_class'] = "low";
        } else {
            $item['status_text'] = "In Stock";
            $item['status_class'] = "ok";
        }
    }

    echo json_encode($items);
}

/* ================= ADD ITEM ================= */
if ($action == "add") {
    // ✅ DINAGDAG: PAGSUSURI KUNG MAY LAMAN ANG MGA KAILANGAN
    if(
        empty($_POST['item_name']) || 
        !isset($_POST['stock']) || 
        !isset($_POST['reorder_level'])
    ){
        echo json_encode(["status" => "error", "message" => "Required fields missing"]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO inventory 
        (item_name, category, supplier, unit, stock, cost, reorder_level)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $_POST['item_name'],
        $_POST['category'] ?? '', // Kung walang laman, gawing walang laman ('')
        $_POST['supplier'] ?? '',
        $_POST['unit'] ?? '',
        $_POST['stock'],
        $_POST['cost'] ?? 0, // Kung walang nilagay na presyo, automatic 0
        $_POST['reorder_level']
    ]);

    echo json_encode(["status" => "success"]);
}

/* ================= UPDATE ITEM ================= */
if ($action == "update") {
    $stmt = $pdo->prepare("UPDATE inventory SET
        item_name=?, category=?, supplier=?, unit=?, stock=?, cost=?, reorder_level=?
        WHERE id=?");

    $stmt->execute([
        $_POST['item_name'],
        $_POST['category'],
        $_POST['supplier'],
        $_POST['unit'],
        $_POST['stock'],
        $_POST['cost'],
        $_POST['reorder_level'],
        $_POST['id']
    ]);

    echo json_encode(["status" => "updated"]);
}

/* ================= DELETE ITEM ================= */
if ($action == "delete") {
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE id=?");
    $stmt->execute([$_POST['id']]);

    echo json_encode(["status" => "deleted"]);
}
?>