<?php
session_start();
require_once 'db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'];
    $payment_method = $_POST['payment_method'];
    $total_amount = $_POST['total_amount'];
    $user_id = $_SESSION['user_id'];

    // Bắt đầu transaction
    $conn->begin_transaction();

    try {
        // Thêm thông tin thanh toán
        $sql = "INSERT INTO payments (booking_id, user_id, amount, payment_method, payment_date, status) 
                VALUES (?, ?, ?, ?, NOW(), 'completed')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iids", $booking_id, $user_id, $total_amount, $payment_method);
        $stmt->execute();

        // Cập nhật trạng thái booking
        $sql = "UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $booking_id, $user_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        // Chuyển hướng đến trang thành công
        header('Location: booking_success.php?booking_id=' . $booking_id);
        exit;
    } catch (Exception $e) {
        // Rollback nếu có lỗi
        $conn->rollback();
        $error = 'Có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại sau.';
    }
}
?> 