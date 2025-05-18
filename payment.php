<?php
session_start();
require_once 'db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Kiểm tra booking_id
if (!isset($_GET['booking_id'])) {
    header('Location: index.php');
    exit;
}

$booking_id = $_GET['booking_id'];
$user_id = $_SESSION['user_id'];

// Lấy thông tin đặt vé
$sql = "SELECT b.*, m.title as movie_title, m.poster, s.show_date, s.show_time,
        GROUP_CONCAT(bs.seat_number) as seats
        FROM bookings b
        JOIN movies m ON b.movie_id = m.id
        JOIN showtimes s ON b.showtime_id = s.id
        JOIN booked_seats bs ON b.id = bs.booking_id
        WHERE b.id = ? AND b.user_id = ? AND b.status = 'pending'
        GROUP BY b.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - CGV Cinemas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://www.cgv.vn/skin/frontend/cgv/default/images/cgvlogo.png" alt="CGV Logo" height="30">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Phim đang chiếu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Phim sắp chiếu</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Payment Section -->
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0">Thông tin đặt vé</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="<?php echo $booking['poster']; ?>" alt="<?php echo $booking['movie_title']; ?>" class="img-fluid">
                            </div>
                            <div class="col-md-8">
                                <h5><?php echo $booking['movie_title']; ?></h5>
                                <p>
                                    <strong>Suất chiếu:</strong><br>
                                    <?php echo date('d/m/Y', strtotime($booking['show_date'])); ?> - 
                                    <?php echo date('H:i', strtotime($booking['show_time'])); ?>
                                </p>
                                <p>
                                    <strong>Ghế:</strong><br>
                                    <?php echo $booking['seats']; ?>
                                </p>
                                <p>
                                    <strong>Tổng tiền:</strong><br>
                                    <?php echo number_format($booking['total_price'], 0, ',', '.'); ?> VNĐ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Phương thức thanh toán</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="process_payment.php">
                            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
                            <input type="hidden" name="total_amount" value="<?php echo $booking['total_price']; ?>">
                            
                            <div class="mb-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="momo" value="momo" checked>
                                    <label class="form-check-label" for="momo">
                                        <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo" height="30">
                                        Ví MoMo
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="zalopay" value="zalopay">
                                    <label class="form-check-label" for="zalopay">
                                        <img src="https://upload.wikimedia.org/wikipedia/vi/thumb/7/7c/ZaloPay_logo.svg/1200px-ZaloPay_logo.svg.png" alt="ZaloPay" height="30">
                                        ZaloPay
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card">
                                    <label class="form-check-label" for="credit_card">
                                        <i class="fas fa-credit-card"></i> Thẻ tín dụng/ghi nợ
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Thanh toán <?php echo number_format($booking['total_price'], 0, ',', '.'); ?> VNĐ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Lưu ý</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-info-circle text-primary"></i>
                                Vui lòng thanh toán trong vòng 15 phút để giữ chỗ.
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-ticket-alt text-primary"></i>
                                Vé điện tử sẽ được gửi qua email sau khi thanh toán thành công.
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-clock text-primary"></i>
                                Vui lòng đến rạp trước giờ chiếu 30 phút để nhận vé.
                            </li>
                            <li>
                                <i class="fas fa-phone text-primary"></i>
                                Nếu cần hỗ trợ, vui lòng liên hệ hotline: 1900 6017
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>CGV Cinemas</h5>
                    <p>Hệ thống rạp chiếu phim hiện đại nhất Việt Nam</p>
                </div>
                <div class="col-md-4">
                    <h5>Liên kết</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Về chúng tôi</a></li>
                        <li><a href="#" class="text-light">Điều khoản sử dụng</a></li>
                        <li><a href="#" class="text-light">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Liên hệ</h5>
                    <p>Email: contact@cgv.vn</p>
                    <p>Hotline: 1900 6017</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 