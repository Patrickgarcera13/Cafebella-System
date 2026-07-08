<?php
// ✅ Connect to YOUR database.php (same folder)
require __DIR__ . '/database.php';
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all menu items sorted
        $stmt = $pdo->query("SELECT * FROM menu_items ORDER BY category, name");
        $menu = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $menu[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'category' => $row['category'],
                'price' => (float)$row['price'],
                'image' => $row['image']
            ];
        }
        echo json_encode($menu);
        break;

    case 'POST':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $name = trim($_POST['name']);
        $category = trim($_POST['category']);
        $price = (float)$_POST['price'];

        // Keep existing image if no new one uploaded
        $image_path = isset($_POST['current_image']) ? trim($_POST['current_image']) : 'IMAGES/MENU/default.jpg';

        // Match the file field name from admin.php: "newImg" uses "image_file" here
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $target_dir = __DIR__ . '/../IMAGES/MENU/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_name = time() . '_' . basename($_FILES["image_file"]["name"]);
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
                // Save relative path for database
                $image_path = 'IMAGES/MENU/' . $file_name;
            }
        }

        try {
            if ($id) {
                // Update existing item
                $stmt = $pdo->prepare("UPDATE menu_items SET name = ?, category = ?, price = ?, image = ? WHERE id = ?");
                $stmt->execute([$name, $category, $price, $image_path, $id]);
            } else {
                // Add new item
                $stmt = $pdo->prepare("INSERT INTO menu_items (name, category, price, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $category, $price, $image_path]);
            }
            echo json_encode(["status" => "success"]);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = (int)($data['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(["status" => "success"]);
            } catch (PDOException $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        }
        break;
}
?>
