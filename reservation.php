<?php
require_once 'db_config.php';
require_once 'functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';

$user_id = $_SESSION['user_id'];

$duration = 30;
$cleanup = 0;
$start = "09:00";
$end = "15:01";

try {
    $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
} catch (PDOException $e) {
    echo "Error connecting to the database: " . $e->getMessage();
    exit;
}

$allReservations = getAllReservations();

$e = $_GET['e'] ?? 0;
if ($e == 1) {
    echo '<script>alert("Töltsd ki az összes mezőt!")</script>';
}
if ($e == 3) {
    echo '<script>alert("Sikeres foglalás!")</script>';
}

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
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/ollo.jpg">
    <!-- CSS here -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/slicknav.css">
    <link rel="stylesheet" href="../assets/css/flaticon.css">
    <link rel="stylesheet" href="../assets/css/gijgo.css">
    <link rel="stylesheet" href="../assets/css/animate.min.css">
    <link rel="stylesheet" href="../assets/css/animated-headline.css">
    <link rel="stylesheet" href="../assets/css/magnific-popup.css">
    <link rel="stylesheet" href="../assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/css/slick.css">
    <link rel="stylesheet" href="../assets/css/nice-select.css">
    <link rel="stylesheet" href="../assets/css/style.css">






</head>

<body>
    <!-- ? Preloader Start -->
    <div id="preloader-active">
        <div class="preloader d-flex align-items-center justify-content-center">
            <div class="preloader-inner position-relative">
                <div class="preloader-circle"></div>
                <div class="preloader-img pere-text">
                    <img src="../assets/img/logo/loder.png" alt="">
                </div>
            </div>
        </div>
    </div>

    <?php
    $dt = new DateTime;
    if (isset($_GET['year']) && isset($_GET['week'])) {
        $dt->setISODate($_GET['year'], $_GET['week']);
    } else {
        $dt->setISODate($dt->format('o'), $dt->format('W'));
    }
    $year = $dt->format('o');
    $week = $dt->format('W');
    $month = $dt->format('F');
    $year = $dt->format('Y');
    $current_date = new DateTime();
    ?>

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
                                <a href="index.php"><img src="../assets/img/logo/logo.png" alt=""></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <!-- Main-menu -->
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a href="../index.php">Kezdőlap</a></li>
                                            <li><a href="../barbers.php">Szalonok</a></li>
                                            <li><a href="../salon_login.php">Szalon tulajdonos</a></li>
                                            <li><a href="../show_reservation.php">Foglalások</a></li>
                                            <li><a href="../profile.php">Profil</a></li>
                                        </ul>
                                    </nav>
                                </div>
                                <div class="header-right-btn f-right d-none d-lg-block ml-30">
                                    <?php if ($isLoggedIn) : ?>
                                        <a href="../logout.php" class="btn header-btn">Kijelentkezés</a>
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
        <div class="slider-area2">
            <div class="slider-height2 d-flex align-items-center">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="hero-cap hero-cap2 pt-70 text-center">
                                <h2>Foglalás</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-12 text-center">
                    <section>
                        <section>
                            <div class="container-fluid my-5">
                                <div class="row justify-content-center">
                                    <div class="col-md-10">
                                        <div class="text-center mb-3">
                                            <a class="btn btn-primary btn-xs bg-transparent" href="<?php echo $_SERVER['PHP_SELF'] . '?week=' . ($week - 1) . '&year=' . $year; ?>">Előző hét</a>
                                            <a class="btn btn-primary btn-xs bg-transparent" href="reservation.php">Jelenlegi hét</a>
                                            <a class="btn btn-primary btn-xs bg-transparent" href="<?php echo $_SERVER['PHP_SELF'] . '?week=' . ($week + 1) . '&year=' . $year; ?>">Következő hét</a>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-bordered text-center table-dark ">
                                                <thead>
                                                    <tr>
                                                        <?php
                                                        $week_start = clone $dt;
                                                        $week_start->modify('monday this week');
                                                        for ($i = 0; $i < 7; $i++) {
                                                            $style = ($week_start->format('Y-m-d') == $current_date->format('Y-m-d')) ? 'background-color: #d19f68;' : '';
                                                            echo "<th style='$style'>" . $week_start->format('l') . "<br>" . $week_start->format('Y. F d') . "</th>\n";
                                                            $week_start->modify('+1 day');
                                                        }
                                                        ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $timeslots = timeslots($duration, $cleanup, $start, $end);
                                                    for ($j = 0; $j < count($timeslots); $j++) {
                                                        $week_start = clone $dt;
                                                        $week_start->modify('monday this week');
                                                        echo "<tr>";
                                                        for ($i = 0; $i < 7; $i++) {
                                                            $date = $week_start->format('Y-m-d');
                                                            $day = $week_start->format('l');
                                                            $buttonStart = $timeslots[$j]['start'];
                                                            $buttonEnd = $timeslots[$j]['end'];
                                                            $reservationsData = getReservationsData($date);
                                                            $buttonColor = 'grey';
                                                            $disabled = '';

                                                            // Check if the date is in the past or if it's Sunday
                                                            if ($date < date('Y-m-d') || $day == 'Sunday') {
                                                                $buttonColor = ($day == 'Sunday') ? '#ffcccb' : '#d3d3d3'; // Light red for Sundays, light grey for past dates
                                                                $disabled = 'disabled';
                                                                $buttonText = ($day == 'Sunday') ? "We're closed" : "$buttonStart - $buttonEnd";
                                                            } else {
                                                                foreach ($reservationsData as $reservation) {
                                                                    if (reservationOverlaps($reservation['reservation_time'], $buttonStart, $buttonEnd)) {
                                                                        $buttonColor = '#c7bc10';
                                                                        break;
                                                                    }
                                                                }
                                                                $buttonText = "$buttonStart - $buttonEnd";
                                                            }

                                                            echo "<td><button class='btn btn-primary btn-xs timeslot' style='background-color: $buttonColor' $disabled";
                                                            echo " data-toggle='modal' data-target='#exampleModal' data-start='$buttonStart' data-end='$buttonEnd' data-date='$date' data-day='$day'>$buttonText</button></td>";
                                                            $week_start->modify('+1 day');
                                                        }
                                                        echo "</tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </section>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Foglalás létrehozása</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="timeslotDetails"></p>

                        <form id="addReservationForm" method="post" action="add_reservation.php" class="archive-form">
                            <input type="hidden" id="selectedDate" name="selectedDate">
                            <input type="hidden" id="selectedTime" name="selectedTime">
                            <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>">

                            <div class="mb-3">
                                <label for="frizer_id" class="form-label">Válasszon fodrászt:</label>
                                <select name="frizer_id" class="form-select" id="frizer_id">
                                    <?php
                                    $frizers = $pdo->query("SELECT frizer_id, frizer_name FROM frizers WHERE salon_id = 1")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($frizers as $frizer) {
                                        echo "<option value='" . $frizer['frizer_id'] . "'>" . $frizer['frizer_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="treatment_id" class="form-label">Válasszon hajkezelést:</label>
                                <select name="treatment_id" class="form-select" id="treatment_id">
                                    <?php
                                    $treatments = $pdo->query("SELECT treatment_id, treatment_name, treatment_price FROM treatment WHERE salon_id = 1")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($treatments as $treatment) {
                                        echo "<option value='" . $treatment['treatment_id'] . "'>" . $treatment['treatment_name'] . " - $" . $treatment['treatment_price'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label">Megjegyzés:</label>
                                <input type="text" id="comment" name="comment" class="form-control">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-secondary">Hozzáadás</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script>
            document.querySelectorAll('.timeslot').forEach(item => {
                item.addEventListener('click', event => {
                    if (event.target.hasAttribute('disabled')) {
                        return;
                    }
                    const start = event.target.getAttribute('data-start');
                    const end = event.target.getAttribute('data-end');
                    const date = event.target.getAttribute('data-date');
                    const day = event.target.getAttribute('data-day');
                    document.getElementById('selectedDate').value = date;
                    document.getElementById('selectedTime').value = start;

                    document.getElementById('timeslotDetails').innerHTML = `
                <p>Dátum: ${date}</p>
                <p>Nap: ${day}</p>
                <p>Kezdési idő: ${start}</p>
                <p>Fejezési idő: ${end}</p>
            `;
                });
            });

            // Listen for modal close event and reset body padding
            $('#exampleModal').on('hidden.bs.modal', function() {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });
        </script>


    </main>
    <footer>
        <!--? Footer Start-->
        <div class="footer-area section-bg" data-background="../assets/img/gallery/footer_bg.png">
            <div class="container">
                <div class="footer-top footer-padding">
                    <div class="row d-flex justify-content-between">
                        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-8">
                            <div class="single-footer-caption mb-50">
                                <!-- logo -->
                                <div class="footer-logo">
                                    <a href="index.php"><img src="../assets/img/logo/logo2_footer.png" alt=""></a>
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
                                        <li><a href="../Barber1.php">Hairway to Heaven</a></li>
                                        <li><a href="../Barber2.php">Trim Trends</a></li>
                                        <li><a href="../Barber3.php">Hair Hub</a></li>
                                        <li><a href="../Barber4.php">Timeless Trims</a></li>
                                        <li><a href="../Barber5.php">Vajers Place</a></li>
                                        <li><a href="../Barber6.php">Bark and Bath Boutique</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2 col-md-3 col-sm-5">
                            <div class="single-footer-caption mb-50">
                                <div class="footer-tittle">
                                <h4>Fedezze fel</h4>
                                    <ul>
                                        <li><a href="../index.php">Kezdőlap</a></li>
                                        <li><a href="aboutus.php">Rólunk</a></li>
                                    </ul>
                                </div>
                            </div>
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

    <script src="..//assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <!-- Jquery, Popper, Bootstrap -->
    <script src="../assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="../assets/js/popper.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <!-- Jquery Mobile Menu -->
    <script src="../assets/js/jquery.slicknav.min.js"></script>

    <!-- Jquery Slick , Owl-Carousel Plugins -->
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/slick.min.js"></script>
    <!-- One Page, Animated-HeadLin -->
    <script src="../assets/js/wow.min.js"></script>
    <script src="../assets/js/animated.headline.js"></script>
    <script src="../assets/js/jquery.magnific-popup.js"></script>

    <!-- Date Picker -->
    <script src="../assets/js/gijgo.min.js"></script>
    <!-- Nice-select, sticky -->
    <script src="../assets/js/jquery.nice-select.min.js"></script>
    <script src="../assets/js/jquery.sticky.js"></script>

    <!-- counter , waypoint,H    Direction -->
    <script src="../assets/js/jquery.counterup.min.js"></script>
    <script src="..//assets/js/waypoints.min.js"></script>
    <script src="../assets/js/jquery.countdown.min.js"></script>
    <script src="../assets/js/hover-direction-snake.min.js"></script>

    <!-- contact js -->
    <script src="../assets/js/contact.js"></script>
    <script src="../assets/js/jquery.form.js"></script>
    <script src="../assets/js/jquery.validate.min.js"></script>
    <script src="../assets/js/mail-script.js"></script>
    <script src="../assets/js/jquery.ajaxchimp.min.js"></script>

    <!-- Jquery Plugins, main Jquery -->
    <script src="../assets/js/plugins.js"></script>
    <script src="../assets/js/main.js"></script>
</body>

</html>