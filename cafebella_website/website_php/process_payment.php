<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once "database.php";

// ✅ Simpleng check lang
if (!isset($_SESSION['booking_data'])) {
    echo json_encode(["status" => "error", "message" => "Walang booking data"]);
    exit;
}

$data = $_SESSION['booking_data'];
$payment_reference = trim($_POST['payment_reference'] ?? '');

if (empty($payment_reference)) {
    echo json_encode(["status" => "error", "message" => "Ilagay ang reference number"]);
    exit;
}

// ✅ Huwag munang mag-check kung naisave na — ayusin muna ang save
$booking_status = ($data['payment_type'] === "Full Payment") ? "Accepted" : "Pending";

try {
    // ✅ SIGURADUHIN NA KASAMA ANG LAHAT — KASAMA ANG payment_method!
    $sql = "INSERT INTO bookings (
        receipt_code, full_name, email, facebook, contact_number,
        service_type, guest_count, total_amount, event_date, event_time,
        province, city, barangay, zip_code, street_address,
        payment_type, payment_method, payment_reference, additional_notes, booking_status
    ) VALUES (
        :receipt_code, :full_name, :email, :facebook, :contact_number,
        :service_type, :guest_count, :total_amount, :event_date, :event_time,
        :province, :city, :barangay, :zip_code, :street_address,
        :payment_type, :payment_method, :payment_reference, :additional_notes, :booking_status
    )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':receipt_code'      => $data['receipt_code'],
        ':full_name'         => $data['full_name'],
        ':email'             => $data['email'],
        ':facebook'          => $data['facebook'],
        ':contact_number'    => $data['contact_number'],
        ':service_type'      => $data['service_type'],
        ':guest_count'       => $data['guest_count'],
        ':total_amount'      => $data['total_amount'],
        ':event_date'        => $data['event_date'],
        ':event_time'        => $data['event_time'],
        ':province'          => $data['province'],
        ':city'              => $data['city'],
        ':barangay'          => $data['barangay'],
        ':zip_code'          => $data['zip_code'],
        ':street_address'    => $data['street_address'],
        ':payment_type'      => $data['payment_type'],
        ':payment_method'    => $data['payment_method'], // ✅ DATI KULANG ITO!
        ':payment_reference' => $payment_reference,
        ':additional_notes'  => $data['additional_notes'],
        ':booking_status'    => $booking_status
    ]);

    // ✅ I-update ang session
    $new_id = $pdo->lastInsertId();
    $_SESSION['booking_data']['id'] = $new_id;
    $_SESSION['booking_data']['payment_reference'] = $payment_reference;
    $_SESSION['receipt_code'] = $data['receipt_code'];
    $_SESSION['booking_saved'] = true;

    echo json_encode([
        "status" => "success",
        "redirect" => "Receipt.php"
    ]);

} catch (PDOException $e) {
    // ✅ Ipakita ang eksaktong error para malaman natin
    echo json_encode(["status" => "error", "message" => "Database Error: " . $e->getMessage()]);
}
?>
