<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Kunin lahat ng data mula sa form
$full_name        = trim($_POST['full_name'] ?? '');
$email            = trim($_POST['email'] ?? '');
$facebook         = trim($_POST['facebook'] ?? '');
$contact_number   = trim($_POST['contact_number'] ?? '');
$service_type     = trim($_POST['service'] ?? '');
$guest_count      = intval($_POST['guest_count'] ?? 0);
$total_amount     = floatval($_POST['total_amount'] ?? 0);
$event_date       = trim($_POST['event_date'] ?? '');
$event_time       = trim($_POST['event_time'] ?? '');
$province         = trim($_POST['province'] ?? '');
$city             = trim($_POST['city'] ?? '');
$barangay         = trim($_POST['barangay'] ?? '');
$zip_code         = trim($_POST['zip_code'] ?? '');
$street_address   = trim($_POST['street_address'] ?? '');
$payment_type     = trim($_POST['payment_type'] ?? '');
$payment_method   = trim($_POST['payment_method'] ?? '');
$additional_notes = trim($_POST['additional_notes'] ?? '');

// Validation
if (
    empty($full_name) || empty($email) || empty($contact_number) ||
    empty($service_type) || $guest_count < 1 || $total_amount <= 0 ||
    empty($event_date) || empty($event_time) || empty($province) ||
    empty($city) || empty($barangay) || empty($zip_code) || empty($street_address) ||
    empty($payment_type) || empty($payment_method)
) {
    echo json_encode(["status" => "error", "message" => "Punan lahat ng kailangang patlang"]);
    exit;
}

// Unique receipt code
$receipt_code = 'CB-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

// ✅ IHO-HOLD LANG SA SESSION — WALANG DATABASE DITO!
$_SESSION['booking_data'] = [
    'receipt_code'       => $receipt_code,
    'full_name'          => $full_name,
    'email'              => $email,
    'facebook'           => $facebook,
    'contact_number'     => $contact_number,
    'service_type'       => $service_type,
    'guest_count'        => $guest_count,
    'total_amount'       => $total_amount,
    'event_date'         => $event_date,
    'event_time'         => $event_time,
    'province'           => $province,
    'city'               => $city,
    'barangay'           => $barangay,
    'zip_code'           => $zip_code,
    'street_address'     => $street_address,
    'payment_type'       => $payment_type,
    'payment_method'     => $payment_method,
    'payment_reference'  => '',
    'additional_notes'   => $additional_notes,
    'booking_status'     => 'Pending'
];

// Itakda kung saang pahina pupunta
$next_page = ($payment_method === "CASH") ? "Paymentcash.php" : "Payment.php";

echo json_encode([
    "status" => "success",
    "next_page" => $next_page,
    "receipt_code" => $receipt_code
]);
?>
