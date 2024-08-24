<?php
require_once 'device.repository.php';


$user_agent = $_SERVER['HTTP_USER_AGENT'];
$device_repo = new DeviceRepository($user_agent);







if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

require_once 'db_config.php';


 function collectdata($dsn,$pdoOptions,$device_repo)
    {
        try {
            
            $conn = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "INSERT INTO frizer.user_info (ip, device, os, browser, date) VALUES (:ip, :device, :os, :browser, :date)";
            $stmt =$conn->prepare($query);

            $ip = $device_repo->get_ip();
            $device = $device_repo->get_device();
            $os = $device_repo->get_os();
            $browser = $device_repo->get_browser();
            $date = date('Y-m-d H:i:s');

            $stmt->bindParam("ip", $ip);
            $stmt->bindParam("device", $device);
            $stmt->bindParam("os", $os);
            $stmt->bindParam("browser", $browser);
            $stmt->bindParam("date", $date);

            $stmt->execute();

        } catch (PDOException $exc) {
            echo $exc->getMessage();
        }
    }

 



function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (isset($_POST['login'])) {
    $errors = array();

    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']);

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email';
    }

    if (empty($username)) {
        $errors[] = 'Username is required';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    if (empty($errors)) {
        try {
            $conn = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Ez a querry 2 dolgot néz meg , az egyik az hogy a felhasználó ki van e tiltva, illetve hogy verifikálta-e. 
            $sql = "SELECT * FROM users WHERE email = :email AND active = 1 AND is_blocked = 0";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($stmt->rowCount() > 0) {
                if (password_verify($password, $result['password'])) {
                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['username'] = $result['username'];
                    $_SESSION['email'] = $result['email'];
                    $_SESSION['active'] = $result['active'];
                    header('Location: index.php');
                    collectdata($dsn,$pdoOptions,$device_repo);
                    exit();
                } else {
                    $errors[] = 'Hibás jelszó';
                }
            } else {
                $errors[] = 'A felhasználói fiókod le van tiltva, vagy nem verifikáltad!';
            }
        } catch (PDOException $e) {
            $errors[] = 'Error logging in: ' . $e->getMessage();
        } finally {
            $conn = null;
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <!--? Hero Start -->
        <div class="slider-area2">
            <div class="slider-height2 d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="hero-cap hero-cap2 pt-70 text-center">
                                <h2>Bejelentkezés</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero End -->
        <!--? Gallery Area Start -->
        <div class="pt-5 pb-5 text-center">
            <div class="container d-flex justify-content-center pt-5 pb-5">
                <div class="form-container mx-auto form-heading py-5">
                    <h2 class="form-heading h1">Bejelentkezés</h2>
                    <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])) : ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <?php
                            foreach ($_SESSION['errors'] as $error) {
                                echo $error . '<br>';
                            }
                            unset($_SESSION['errors']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Felhasználónév</label>
                                <input type="text" id="username" name="username">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Jelszó</label>
                                <input type="password" id="password" name="password">
                            </div>
                            <div>
                                <button type="submit" class="submit-btn" name="login">Bejelentkezés</button>
                            </div>
                        </form>
                        <br>
                        <a href="register.php" class="back-btn">Nincs fiókod? Regisztrálj itt</a>
                        <br> <br> <br>
                        <a href="forgotpw.php" class="back-btn">Elfelejtettem a jelszót</a>
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
                                        <li><a href="aboutus.php">Rólunk</a></li>
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


    <?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';

    if (isset($_POST['send-link'])) {
        $email = $_POST['email'];

        try {
            $conn = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);

            // Check if email exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $existingUser = $stmt->fetch();

            if (!$existingUser) {
                echo "
                <script>
                    alert('No such email found!');
                </script>";
                exit;
            } elseif ($existingUser["active"] == 0) {
                echo "
                <script>
                    alert('The account needs to be verified before requesting a password change');
                </script>";
                exit;
            } else {
                $pw_token = bin2hex(random_bytes(50));

                $_SESSION['pw_token'] = $pw_token;
                $_SESSION['email'] = $email;

                // Update the password reset token
                $stmt = $conn->prepare("UPDATE users SET pw_token = :pw_token WHERE email = :email");
                $stmt->bindParam(':pw_token', $pw_token);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                require "phpmailer/PHPMailerAutoload.php";
                $mail = new PHPMailer;

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';

                $mail->Username = 'vtsfrizer@gmail.com';
                $mail->Password = 'uyuy ltjk rzfe mwvl';

                //Recipients
                $mail->setFrom('vtsfrizer@gmail.com', '');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Password Update";
                $mail->Body = "<b>Dear User!</b>
            <p>You can change your password by following the link below</p>
            https://localhost/iskola/frizer/updatepw.php?token=" . $pw_token;

                if (!$mail->send()) {
                    echo "
                    <script>
                        alert('Invalid email');
                    </script>";
                    exit;
                } else {
                    echo "
                    <script>
                        alert('Please check your email for password change instructions!');
                    </script>";
                }
            }
        } catch (PDOException $e) {
            echo "
            <script>
                alert('Error: " . $e->getMessage() . "');
            </script>";
            exit;
        }
    }
    ?>
</body>

</html>