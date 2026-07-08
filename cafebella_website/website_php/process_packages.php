<?php
// Koneksyon sa database
require __DIR__ . '/database.php';

// Siguradong tamang output at walang extra character
ob_clean();
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    // --- GET: Kunin lahat ng packages ---
    case 'GET':
        try {
            $stmt = $pdo->query("SELECT * FROM event_packages ORDER BY id DESC");
            $packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // I-format ang data para siguradong tama ang format
            $result = [];
            foreach ($packages as $row) {
                $result[] = [
                    'id' => (int)$row['id'],
                    'service_name' => $row['service_name'],
                    'title' => $row['title'],
                    'display_price' => $row['display_price'],
                    'image' => $row['image'],
                    'description' => $row['description'],
                    'base_price' => (float)$row['base_price'],
                    'min_guests' => (int)$row['min_guests'],
                    'max_guests' => (int)$row['max_guests'],
                    'break_point' => (int)$row['break_point'],
                    'break_price' => (float)$row['break_price'],
                    'extra_per_guest' => (float)$row['extra_per_guest'],
                    'max_total' => (float)$row['max_total'],
                    'status' => $row['status']
                ];
            }
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    // --- POST: Magdagdag / Mag-update ---
    case 'POST':
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        
        // Kunin ang lahat ng input
        $service_name = trim($_POST['service_name'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $display_price = trim($_POST['display_price'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $base_price = (float)($_POST['base_price'] ?? 0);
        $min_guests = (int)($_POST['min_guests'] ?? 30);
        $max_guests = (int)($_POST['max_guests'] ?? 100);
        $break_point = (int)($_POST['break_point'] ?? 0);
        $break_price = (float)($_POST['break_price'] ?? 0);
        $extra_per_guest = (float)($_POST['extra_per_guest'] ?? 0);
        $max_total = (float)($_POST['max_total'] ?? 0);
        $status = trim($_POST['status'] ?? 'active');

        // Gamitin ang dating larawan kung walang bagong in-upload
        $image_path = trim($_POST['current_image'] ?? 'IMAGES/PACKAGES/default.jpg');

        // Proseso ng pag-upload ng larawan
        if (isset($_FILES['package_file']) && $_FILES['package_file']['error'] === UPLOAD_ERR_OK) {
            $target_dir = __DIR__ . '/../IMAGES/PACKAGES/';
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

            // Payagan lang ang mga uri ng larawan
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES["package_file"]["name"], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Tanging larawan lang ang pinapayagan."]);
                exit;
            }

            $file_name = time() . '_pkg_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES["package_file"]["name"]));
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["package_file"]["tmp_name"], $target_file)) {
                $image_path = 'IMAGES/PACKAGES/' . $file_name;
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Hindi ma-save ang larawan."]);
                exit;
            }
        }

        // I-save sa database
        try {
            if ($id) {
                $stmt = $pdo->prepare("UPDATE event_packages 
                    SET service_name = ?, title = ?, display_price = ?, description = ?, image = ?,
                        base_price = ?, min_guests = ?, max_guests = ?, break_point = ?, break_price = ?,
                        extra_per_guest = ?, max_total = ?, status = ?
                    WHERE id = ?");
                $stmt->execute([
                    $service_name, $title, $display_price, $description, $image_path,
                    $base_price, $min_guests, $max_guests, $break_point, $break_price,
                    $extra_per_guest, $max_total, $status, $id
                ]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO event_packages 
                    (service_name, title, display_price, image, description,
                     base_price, min_guests, max_guests, break_point, break_price,
                     extra_per_guest, max_total, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $service_name, $title, $display_price, $image_path, $description,
                    $base_price, $min_guests, $max_guests, $break_point, $break_price,
                    $extra_per_guest, $max_total, $status
                ]);
            }
            echo json_encode(["status" => "success"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        break;

    // --- DELETE: Burahin ang package ---
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = (int)($data['id'] ?? 0);
        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM event_packages WHERE id = ?");
                $stmt->execute([$id]);
                echo json_encode(["status" => "success"]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid ID"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}
