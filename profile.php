<?php
require_once 'db_config.php';
require_once 'functions.php';
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

$username = $_SESSION['username'];


try {
  $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
  $userData = getAdminData($username, $pdo);
  if (!$userData || count($userData) == 0) {
    throw new Exception("No data found for username: $username");
  }
} catch (PDOException $e) {
  echo "Database Error: " . $e->getMessage();
  exit();
} catch (Exception $e) {
  echo "Error: " . $e->getMessage();
  exit();
}

$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : "";
unset($_SESSION['error_message']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
  $newUsername = $_POST['new_username'];
  $newFirstName = $_POST['new_firstname'];
  $newLastName = $_POST['new_lastname'];
  $newEmail = $_POST['new_email'];
  $newPhoneNum = $_POST['new_phone_num'];
  $newPassword = $_POST['new_password'];

  $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

  try {
    updateAdminData($username, $newUsername, $newFirstName, $newLastName, $newEmail, $newPhoneNum, $hashedPassword, $pdo);

    if ($newUsername !== $username) {
      $_SESSION['username'] = $newUsername;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
  } catch (PDOException $e) {
    echo "Error updating profile: " . $e->getMessage();
  }
}

$profilePicName = $userData[0]['pic_name'];
$profilePicURL = "pictures/" . $profilePicName;

?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title> Profile </title>
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
                        <div class="col-xl-10 col-lg-5 col-md-5">
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
            <div class="col-12">
              <div class="hero-cap hero-cap2 pt-70 text-center">
                <?php if ($userData && count($userData) > 0) : ?>
                  <h2 class="text-uppercase">Üdvözöljük, <?php echo $userData[0]['username']; ?> !</h2>
                  <?php if (!empty($error_message)) : ?>
                    <div class="error">
                      <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                      </div>
                    </div>
                  <?php endif; ?>
                <?php else : ?>
                  <p>Nem sikerült megjeleníteni az adatokat.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="gallery-area section-padding30">
      <div class="container">
        <div class="barbers">
          <div class="container">
            <div class="row">
              <div class="col-lg-6">
                <div id="profile-container">
                  <div id="profile-info">

                    <h1 class="fw-bold fs-4 mb-4">Jelenlegi felhasználónév: <?php echo $userData[0]['username']; ?></h1>
                    <h1 class="fw-bold fs-4 mb-4">Jelenlegi email: <?php echo $userData[0]['email']; ?></h1>

                    <br>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                      <h2 for="new_username">Új felhasználónév:</h2>
                      <input class=" form-control form-control-lg mx-auto" type="text" id="new_username" name="new_username" value="<?php echo $userData[0]['username']; ?>">
                      <br>
                      <h2 for="new_firstname">Új Keresznév: </h2>
                      <input class="form-control form-control-lg mx-auto" type="text" id="new_firstname" name="new_firstname" value="<?php echo $userData[0]['first_name']; ?>">
                      <br>
                      <h2 for="new_lastname">Új Vezetéknév: </h2>
                      <input class="form-control form-control-lg mx-auto" type="text" id="new_lastname" name="new_lastname" value="<?php echo $userData[0]['last_name']; ?>">
                      <br>
                      <h2 for="new_email">Új email: </h2>
                      <input class="form-control form-control-lg mx-auto" type="email" id="new_email" name="new_email" value="<?php echo $userData[0]['email']; ?>">
                      <br>
                      <h2 for="new_phone_num">Új telefonszám:</h2>
                      <input class="form-control form-control-lg mx-auto" type="text" id="new_phone_num" name="new_phone_num" value="<?php echo $userData[0]['mobile']; ?>">
                      <br>
                      <h2 for="new_password">Új jelszó: </h2>
                      <input class="form-control form-control-lg mx-auto" type="password" id="new_password" name="new_password">
                      <br>
                      <input class="btn header-btn" type="submit" name="update_profile" value="Profil frissítése">
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-lg-1"></div>
              <div class="col-lg-5 mt-3">
                <div class="text-center" id="profile-pic-container">
                  <img class="rounded-circle p-4 img-fluid" src="<?php echo $profilePicURL; ?>" alt="Még nincs profilkép" id="profile-pic">
                  <form action="upload.php" method="post" enctype="multipart/form-data">
                    <h2>Válasszon egy képet feltöltésre:</h2>
                    <div class=" mt-5">
                      <input class="form-control" type="file" name="fileToUpload" id="fileToUpload" style="width: 50%;">
                      <input class="btn header" type="submit" value="Kép feltöltése" name="submit">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
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