<?php
session_start();

require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$user_id = $_SESSION['user_id'];

try {
  $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
} catch (PDOException $e) {
  echo "Error connecting to the database: " . $e->getMessage();
  exit;
}

if (isset($_POST['delete']) && isset($_POST['reservation_id'])) {
  $reservation_id = $_POST['reservation_id'];
  $updateQuery = "UPDATE reservations SET cancelled = 1 WHERE reservation_id = ? AND user_id = ? AND TIMESTAMP(date, start_time) > DATE_ADD(NOW(), INTERVAL 1 HOUR)";
  $updateStmt = $pdo->prepare($updateQuery);
  $updateStmt->execute([$reservation_id, $user_id]);
}

$queryPast = "SELECT r.reservation_id, r.date, r.start_time, r.end_time, r.user_id, f.frizer_name, t.treatment_name, r.comment
              FROM reservations r
              JOIN frizers f ON r.frizer_id = f.frizer_id
              JOIN treatment t ON r.treatment_id = t.treatment_id
              WHERE r.user_id = ? AND (r.date < CURDATE() OR (r.date = CURDATE() AND r.end_time <= CURTIME())) AND r.cancelled = 0";
$stmtPast = $pdo->prepare($queryPast);
$stmtPast->execute([$user_id]);
$pastAppointments = $stmtPast->fetchAll(PDO::FETCH_ASSOC);

$queryActive = "SELECT r.reservation_id, r.date, r.start_time, r.end_time, r.user_id, f.frizer_name, t.treatment_name, r.comment
                FROM reservations r
                JOIN frizers f ON r.frizer_id = f.frizer_id
                JOIN treatment t ON r.treatment_id = t.treatment_id
                WHERE r.user_id = ? AND (r.date > CURDATE() OR (r.date = CURDATE() AND r.end_time > CURTIME())) AND r.cancelled = 0";
$stmtActive = $pdo->prepare($queryActive);
$stmtActive->execute([$user_id]);
$activeAppointments = $stmtActive->fetchAll(PDO::FETCH_ASSOC);

$queryCancelled = "SELECT r.reservation_id, r.date, r.start_time, r.end_time, r.user_id, f.frizer_name, t.treatment_name, r.comment
                   FROM reservations r
                   JOIN frizers f ON r.frizer_id = f.frizer_id
                   JOIN treatment t ON r.treatment_id = t.treatment_id
                   WHERE r.user_id = ? AND r.cancelled = 1";
$stmtCancelled = $pdo->prepare($queryCancelled);
$stmtCancelled->execute([$user_id]);
$cancelledAppointments = $stmtCancelled->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/ollo.jpg">
    <!-- CSS here -->
	<link rel="stylesheet" href="assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="assets/css/owl.carousel.min.css">
	<link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/gijgo.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/animated-headline.css">
	<link rel="stylesheet" href="assets/css/magnific-popup.css">
	<link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
	<link rel="stylesheet" href="assets/css/themify-icons.css">
	<link rel="stylesheet" href="assets/css/slick.css">
	<link rel="stylesheet" href="assets/css/nice-select.css">
	<link rel="stylesheet" href="assets/css/style.css">




    
    
</head>
<body>
    <!-- ? Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="assets/img/logo/loder.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader Start -->
    <header>
        <!--? Header Start -->
        <div class="header-area header-transparent pt-20">
            <div class="main-header ">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <!-- Logo -->
                        <div class="col-xl-2 col-lg-2 col-md-1">
                            <div class="logo">
                                <a href="index.php"><img src="assets/img/logo/logo.png" alt=""></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <!-- Main-menu -->
                                <div class="main-menu f-right d-none d-lg-block">
                                <nav>
                                        <ul id="navigation">
                                            <li><a href="index.php">Kezdőlap</a></li>
                                            <li><a href="barbers.php">Szalonok</a></li>
                                            <li><a href="admin_login.php">Admin</a></li>
                                            <li><a href="salon_login.php">Szalon tulajdonos</a></li>
                                            <li><a href="show_reservation.php">Foglalások</a></li>
                                            <li><a href="profile.php">Profil</a></li>
                                        </ul>
                                    </nav>
                                </div>
                                <div class="header-right-btn f-right d-none d-lg-block ml-30">
                                    <?php if ($isLoggedIn) : ?>
                                        <a href="logout.php" class="btn header-btn">Kijelentkezés</a>
                                    <?php else : ?>
                                        <a href="register.php" class="btn header-btn">Csatlakozz hozzánk!</a>
                                        <a href="login.php" class="btn header-btn">login</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Mobile Menu -->
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>
    <main>
    <div class="slider-area2">
            <div class="slider-height2 d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="hero-cap hero-cap2 pt-70 text-center">
                                <h2>Időpontok</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container my-5">
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center mb-4">Aktiv időpontok</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Dátum</th>
                                <th>Foglalá</th>
                                <th>Foglalás vége</th>
                                <th>Fodrász neve</th>
                                <th>Kezelés</th>
                                <th>Kommentár</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activeAppointments as $appointment) : ?>
                                <?php
                                $canDelete = (strtotime($appointment['date'] . ' ' . $appointment['start_time']) > strtotime('+1 hour'));
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['start_time']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['end_time']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['frizer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['treatment_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['comment']); ?></td>
                                    <td>
                                        <form method="post" action="">
                                        <input type="hidden" name="reservation_id" value="<?php echo $appointment['reservation_id']; ?>">
                                        <button type="submit" name="delete" class="btn header btn-sm" <?php if (!$canDelete) echo 'disabled'; ?>>Időpont törlése</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Extra margin here -->
            <div class="row my-5"></div>
            <!-- Extra margin ends -->
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center mb-4">Lejárt időpontok</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Dátum</th>
                                <th>Foglalá</th>
                                <th>Foglalás vége</th>
                                <th>Fodrász neve</th>
                                <th>Kezelés</th>
                                <th>Kommentár</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pastAppointments as $appointment) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['start_time']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['end_time']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['frizer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['treatment_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['comment']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row my-5"></div>
            <!-- Extra margin ends -->
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center mb-4">Lemondott időpontok</h1>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Dátum</th>
                                <th>Foglalá</th>
                                <th>Foglalás vége</th>
                                <th>Fodrász neve</th>
                                <th>Kezelés</th>
                                <th>Kommentár</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cancelledAppointments as $appointment) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['date']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['start_time']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['end_time']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['frizer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['treatment_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['comment']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <!--? Footer Start-->
        <div class="footer-area section-bg" data-background="assets/img/gallery/footer_bg.png">
            <div class="container">
                <div class="footer-top footer-padding">
                    <div class="row d-flex justify-content-between">
                        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-8">
                            <div class="single-footer-caption mb-50">
                                <!-- logo -->
                                <div class="footer-logo">
                                    <a href="index.php"><img src="assets/img/logo/logo2_footer.png" alt=""></a>
                                </div>
                                <div class="footer-tittle">
                                    <div class="footer-pera">
                                        <p class="info1">Bármilyen kérdése van, forduljon hozzánk bizalommal</p>
                                    </div>
                                </div>
                                <div class="footer-number">
                                    <h4><span>+381 </span>45678912</h4>
                                    <p>vtsfrizer@gmail.com</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-3 col-sm-5">
                            <div class="single-footer-caption mb-50">
                                <div class="footer-tittle">
                                    <h4>SZALONJAINK</h4>
                                    <ul>
                                        <li><a href="Barber1.php">Hairway to Heaven</a></li>
                                        <li><a href="Barber2.php">Trim Trends</a></li>
                                        <li><a href="Barber3.php">Hair Hub</a></li>
                                        <li><a href="Barber4.php">Timeless Trims</a></li>
                                        <li><a href="Barber5.php">Vajers Place</a></li>
                                        <li><a href="Barber6.php">Bark and Bath Boutique</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-3 col-sm-5">
                            <div class="single-footer-caption mb-50">
                                <div class="footer-tittle">
                                    <h4>Fedezze fel</h4>
                                    <ul>
                                        <li><a href="index.php">Kezdőlap</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End-->
    </footer>
    <!-- Scroll Up -->
    <div id="back-top" >
        <a title="Go to Top" href="#"> <i class="fas fa-level-up-alt"></i></a>
    </div>

    <!-- JS here -->

    <script src="./assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <!-- Jquery, Popper, Bootstrap -->
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <!-- Jquery Mobile Menu -->
    <script src="./assets/js/jquery.slicknav.min.js"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/slick.min.js"></script>
    <!-- One Page, Animated-HeadLin -->
    <script src="./assets/js/wow.min.js"></script>
    <script src="./assets/js/animated.headline.js"></script>
    <script src="./assets/js/jquery.magnific-popup.js"></script>

    <!-- Date Picker -->
    <script src="./assets/js/gijgo.min.js"></script>
    <!-- Nice-select, sticky -->
    <script src="./assets/js/jquery.nice-select.min.js"></script>
    <script src="./assets/js/jquery.sticky.js"></script>
    
    <!-- counter , waypoint,Hover Direction -->
    <script src="./assets/js/jquery.counterup.min.js"></script>
    <script src="./assets/js/waypoints.min.js"></script>
    <script src="./assets/js/jquery.countdown.min.js"></script>
    <script src="./assets/js/hover-direction-snake.min.js"></script>

    <!-- contact js -->
    <script src="./assets/js/contact.js"></script>
    <script src="./assets/js/jquery.form.js"></script>
    <script src="./assets/js/jquery.validate.min.js"></script>
    <script src="./assets/js/mail-script.js"></script>
    <script src="./assets/js/jquery.ajaxchimp.min.js"></script>
    
    <!-- Jquery Plugins, main Jquery -->	
    <script src="./assets/js/plugins.js"></script>
    <script src="./assets/js/main.js"></script>
    </body>
</html>
