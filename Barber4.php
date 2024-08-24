<?php
require 'db_config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
try {
    $conn = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT treatment_name, treatment_price FROM treatment WHERE salon_id = 4";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $treatments = $stmt->fetchAll();
} catch (PDOException $e) {
    echo 'Query failed: ' . $e->getMessage();
    die();
}

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Timeless Trims</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
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
    <link rel="stylesheet" href="assets/css/barber4.css">
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
        <div class="header-area header-transparent">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-xl-2 col-lg-2 col-md-1">
                            <div class="logo">
                                <a href="index.php"><img src="assets/img/logo/logo.png" alt=""></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                        <li><a href="index.php">Kezdőlap</a></li>
                                            <li><a href="barbers.php">Szalonok</a></li>
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
                                        <a href="login.php" class="btn header-btn">Login</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Main-menu -->
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <!--? Hero Start -->
        <div class="slider-area2">
            <div class="slider-height2 d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="hero-cap hero-cap2 pt-70 text-center">
                                <h2>Timeless Trims</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero End -->
        <!--? About Area Start -->
        <section class="about-area section-padding30 position-relative">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-11">
                        <!-- about-img -->
                        <div class="about-img ">
                            <img src="assets/img/gallery/team4.png" alt="">
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <div class="about-caption">
                            <!-- Section Tittle -->
                            <div class="section-tittle section-tittle3 mb-35">
                            <span>Rólunk</span>
                        <h2>15 Év Tapasztalat a Hajvágásban!</h2>
                        <p class="mb-30 pera-bottom">A Timeless Trims egy kis borbélyboltként kezdte pályafutását, mindössze két borbélyjal és egy székkel. Ma már egy elismert és széles körben ismert helyszínné váltunk, több mint 2000 havi ügyféllel, akik közül sokan az első naptól kezdve visszatérnek!</p>
                            <p class="pera-top mb-50">Ha a hajad már megjárta a poklot, itt az ideje, hogy belépjen a Timeless Trimsbe.</p>

                            <img src="assets/img/gallery/signature.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <!-- About Shape -->
            <div class="about-shape">
                <img src="assets/img/gallery/about-shape.png" alt="">
            </div>
        </section>
        <!-- About-2 Area End -->
        <!--? Services Area Start -->
        <section class="service-area pb-170">
            <div class="container">
                <!-- Section Tittle -->
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-7 col-lg-8 col-md-11 col-sm-11">
                        <div class="section-tittle text-center mb-90">
                        <span>Szolgáltatásaink</span>
<h2>A legjobb szolgáltatások, amiket kínálunk Neked</h2>

                        </div>
                    </div>
                </div>
                <!-- Section caption -->
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="services-caption text-center mb-30">
                            <div class="service-icon">
                                <i class="flaticon-healthcare-and-medical"></i>
                            </div>
                            <div class="service-cap">
                            <h4><a>Stílusos Frizura</a></h4>
                            <p>Válasszon több mint 30 különböző frizura közül, férfiak és nők számára egyaránt. Boltunkban díjnyertes fodrászok várják!</p>

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="services-caption active text-center mb-30">
                            <div class="service-icon">
                                <i class="flaticon-healthcare-and-medical"></i>
                            </div>
                            <div class="service-cap">
                            <h4><a>Testmasszázs</a></h4>
                            <p>Így van! Nem csak a haját vágjuk, hanem testmasszázst is adunk utána! Ez egy olyan pihentető élmény, amit mindenkinek legalább egyszer ki kell próbálnia az élete során!</p>

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="services-caption text-center mb-30">
                            <div class="service-icon">
                                <i class="flaticon-healthcare-and-medical"></i>
                            </div>
                            <div class="service-cap">
                            <h4><a>Szakáll Stílus</a></h4>
                            <p>Ha úgy szeretnél kinézni, mint egy kalóz vagy egy úriember, választhatsz bármit, amit csak szeretnél! Sokféle szakállstílus közül választhatsz, sőt, mellkas szőrt is formázunk!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Services Area End -->
        <!-- Best Pricing Area Start -->
        <div class="best-pricing section-padding1">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="section-tittle mb-50 text-center">
                        <span>Legjobb Áraink</span>
                        <h2>A legjobb árakat kínáljuk<br> a városban!</h2>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="pricing-list">
                                    <ul>
                                        <?php foreach ($treatments as $index => $treatment) : ?>
                                            <?php if ($index % 2 == 0) : ?>
                                                <li>
                                                    <?php echo htmlspecialchars($treatment['treatment_name']); ?>
                                                    <span style="float: right;">$<?php echo htmlspecialchars($treatment['treatment_price']); ?></span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="pricing-list">
                                    <ul>
                                        <?php foreach ($treatments as $index => $treatment) : ?>
                                            <?php if ($index % 2 != 0) : ?>
                                                <li>
                                                    <?php echo htmlspecialchars($treatment['treatment_name']); ?>
                                                    <span style="float: right;">$<?php echo htmlspecialchars($treatment['treatment_price']); ?></span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Best Pricing Area End -->
        <!--? Gallery Area Start -->
        <div class="gallery-area section-padding30">
            <div class="container">
                <!-- Section Tittle -->
                <div class="row justify-content-center">

                    <div class="section-tittle text-center mb-100">
                        <div class="col-md">
                            <div>
                                <button class="reservation-button"><a href="appointment4/reservation.php" style="text-decoration: none; color: white;">Foglalás</a></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="box snake mb-30">
                            <div class="gallery-img " style="background-image: url(assets/img/gallery/gallery1.png);"></div>
                            <div class="overlay"></div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-6 col-sm-6">
                        <div class="box snake mb-30">
                            <div class="gallery-img " style="background-image: url(assets/img/gallery/gallery2.png);"></div>
                            <div class="overlay"></div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-6 col-sm-6">
                        <div class="box snake mb-30">
                            <div class="gallery-img " style="background-image: url(assets/img/gallery/gallery3.png);"></div>
                            <div class="overlay"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="box snake mb-30">
                            <div class="gallery-img " style="background-image: url(assets/img/gallery/gallery4.png);"></div>
                            <div class="overlay"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Gallery Area End -->


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
    <div id="back-top">
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