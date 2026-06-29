<?php
session_start();
header('Content-Type: text/plain; charset=utf-8');

// Koneksyon sa database
require_once "database.php";

// Kung walang booking data, ibalik sa simula
if (!isset($_SESSION['booking_data'])) {
    echo "error: No booking found";
    exit;
}

$data = $_SESSION['booking_data'];
$payment_reference = trim($_POST['payment_reference'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? '');

if (empty($payment_reference)) {
    echo "error: Reference number is required";
    exit;
}

// ✅ TAMANG STATUS:
// - Nagsisimula sa Pending kapag reserbasyon lang
// - Confirmed lang kapag Full Payment
if ($data['payment_type'] === "Full Payment") {
    $booking_status = "Confirmed";
} else {
    $booking_status = "Pending"; // Ito ang gusto mo
}

// Bumuo ng unique receipt code
$receipt_code = "CB-" . date('Ymd') . "-" . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));

try {
    $sql = "INSERT INTO bookings (
                receipt_code, full_name, email, facebook, contact_number,
                service_type, guest_count, total_amount, event_date, event_time,
                province, city, barangay, zip_code, street_address,
                payment_type, payment_method, payment_reference, additional_notes, status
            ) VALUES (
                :receipt_code, :full_name, :email, :facebook, :contact_number,
                :service_type, :guest_count, :total_amount, :event_date, :event_time,
                :province, :city, :barangay, :zip_code, :street_address,
                :payment_type, :payment_method, :payment_reference, :additional_notes, :status
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':receipt_code'       => $receipt_code,
        ':full_name'          => $data['full_name'],
        ':email'              => $data['email'],
        ':facebook'           => $data['facebook'],
        ':contact_number'     => $data['contact_number'],
        ':service_type'       => $data['service_type'],
        ':guest_count'        => $data['guest_count'],
        ':total_amount'       => $data['total_amount'],
        ':event_date'         => $data['event_date'],
        ':event_time'         => $data['event_time'],
        ':province'           => $data['province'],
        ':city'               => $data['city'],
        ':barangay'           => $data['barangay'],
        ':zip_code'           => $data['zip_code'],
        ':street_address'     => $data['street_address'],
        ':payment_type'       => $data['payment_type'],
        ':payment_method'     => $payment_method,
        ':payment_reference'  => $payment_reference,
        ':additional_notes'   => $data['additional_notes'],
        ':status'             => $booking_status // ✅ Tamang halaga
    ]);

    // I-save ang receipt code para sa resibo
    $_SESSION['receipt_code'] = $receipt_code;

    echo "success";

} catch (PDOException $e) {
    echo "error: " . $e->getMessage();
}
?>