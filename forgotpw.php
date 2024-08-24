<?php
require 'vendor/autoload.php';
require 'db_config.php';
session_start();
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
 
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
                    alert('Nem található ez az email');
                    window.location.href = 'forgotpw.php'
                </script>";
            exit;
        } elseif ($existingUser["active"] == 0) {
            echo "
                <script>
                    alert('A fiókodnak aktiválva kell, hogy legyen mielőtt az elfelejtett jelszót használnád!');
                    window.location.href = 'forgotpw.php'
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
            $mail->Body = "<b>Kedves felhaználó!</b>
            <p>Az alábbi linkre kattintva megváltoztathatja jelszavát:</p>
            https://localhost/iskola/frizer/updatepw.php?token=" . $pw_token;
 
            if (!$mail->send()) {
                echo "
                    <script>
                        alert('Érvénytelen email cím');
                        window.location.href = 'forgotpw.php'
                    </script>";
                exit;
            } else {
                echo "
                    <script>
                        alert('Kérjük, ellenőrizze az emailjeit a jelszó megváltoztatására vonatkozó utasításokért!');
                        window.location.href = 'login.php';
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
<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title> PASSWORD RESET </title>
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
    <main>
        <!--? Hero Start -->
        <div class="slider-area2">
            <div class="slider-height2 d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="hero-cap hero-cap2 pt-70 text-center">
                                <h2>Elfelejtett jelszó</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Hero End -->
        
        <div class="pt-5 pb-5 text-center">
            <div class="container d-flex justify-content-center pt-5 pb-5">
                <div class="form-container mx-auto form-heading py-5">
                    <h2 class="form-heading h1">Elfelejtett jelszó</h2>
                    <div id="success-message" class="text-success"></div>
                    <div id="error-message" class="text-danger"></div>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email: </label>
                                    <input type="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <input type="submit" name="send-link" class="submit-btn">
                                </div>
                            </form>
                            <div class="pt-5">
                            <a href="login.php" class="submit-btn">Vissza </a>
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

