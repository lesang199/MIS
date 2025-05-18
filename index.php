<?php
include 'db.php';

$sql = "SELECT * FROM movies";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CGV Cinemas - Đặt vé xem phim</title>
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
                        <a class="nav-link active" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Phim đang chiếu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Phim sắp chiếu</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php
                    session_start();
                    if(isset($_SESSION['user_id'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="book_ticket.php">Đặt vé</a></li>';
                        echo '<li class="nav-item"><a class="nav-link" href="logout.php">Đăng xuất</a></li>';
                    } else {
                        echo '<li class="nav-item"><a class="nav-link" href="login.php">Đăng nhập</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div id="heroCarouselTop" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="images/anhnen.jpg" class="d-block w-100" alt="Ảnh nền">
                    <!-- Optional: Add a very basic caption if needed, or leave empty -->
                    <div class="carousel-caption d-none d-md-block">
                         <h5>Welcome to CGV Cinemas</h5>
                         <p>Book your tickets now!</p>
                     </div>
                </div>
                <!-- Add more basic carousel items here if needed for a simple top slider -->
            </div>
            <!-- Optional: Add basic controls or indicators if you want a simple slider at the top -->
            <!--
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarouselTop" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarouselTop" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
            -->
        </div>
    </div>

    <!-- Now Showing Movies - Transformed to Carousel -->
    <div class="now-showing-full-width my-5">
        <h2 class="text-center mb-4">Phim Đang Chiếu</h2>
        <div id="nowShowingCarousel" class="carousel slide" data-bs-ride="carousel">
             <div class="carousel-indicators">
                <?php
                require_once 'db.php';
                $sql = "SELECT id FROM movies WHERE status = 'now_showing'";
                $result_indicator = $conn->query($sql);
                if ($result_indicator->num_rows > 0) {
                    for ($i = 0; $i < $result_indicator->num_rows; $i++) {
                        echo '<button type="button" data-bs-target="#nowShowingCarousel" data-bs-slide-to="' . $i . '"' . ($i == 0 ? ' class="active" aria-current="true"' : '') . ' aria-label="Slide ' . ($i + 1) . '"></button>';
                    }
                }
                // Note: $conn is closed at the end of the file, so reuse it here.
                $sql = "SELECT * FROM movies WHERE status = 'now_showing'";
                $result = $conn->query($sql);
                ?>
            </div>
            <div class="carousel-inner">
                <?php
                if ($result->num_rows > 0) {
                    $first_item = true;
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="carousel-item' . ($first_item ? ' active' : '') . '">';
                        echo '<img src="' . $row['poster'] . '" class="d-block w-100" alt="' . $row['title'] . '">';
                        echo '<div class="carousel-caption text-start">
                                <span class="badge bg-warning text-dark">NOW SHOWING</span>
                                <h1 class="display-4">' . $row['title'] . '</h1>
                                <p class="hero-metadata">Thời lượng: ' . $row['duration'] . '  Thể loại: ' . $row['genre'] . '</p>
                                <p class="hero-description">' . substr($row['description'], 0, 150) . '... <a href="movie_detail.php?id=' . $row['id'] . '" class="text-warning">See more</a></p>
                                
                                <a href="movie_detail.php?id=' . $row['id'] . '" class="btn btn-warning">Đặt vé ngay</a>
                              </div>';
                        echo '</div>';
                        $first_item = false;
                    }
                }
                ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#nowShowingCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#nowShowingCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
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

<?php
$conn->close();
?>
