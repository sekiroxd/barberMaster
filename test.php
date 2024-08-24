<?php
session_start();
require_once 'phpmailer/PHPMailerAutoload.php';
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$user_id = $_SESSION['user_id'];

if (isset($_SESSION['salon_owner_id'])) {
  $salon_id_owner = $_SESSION['salon_owner_id'];
} else {
  echo '<script>alert("No salon_owner_id found in session.");</script>';
  exit(); // Terminate script if session variable not set
}

$dsn = "mysql:host=" . PARAMS['HOST'] . ";dbname=" . PARAMS['DB'] . ";charset=" . PARAMS['CHARSET'];
$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false
];

try {
  $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $options);
} catch (PDOException $e) {
  die('Connection failed: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $filter = isset($_POST['filter']) ? $_POST['filter'] : 'all';

  $query = "SELECT r.date, u.username AS user_id, f.frizer_name AS frizer_id, t.treatment_name AS treatment_id, r.comment, r.cancelled
            FROM reservations r
            JOIN users u ON r.user_id = u.user_id
            JOIN frizers f ON r.frizer_id = f.frizer_id
            JOIN treatment t ON r.treatment_id = t.treatment_id";

  if ($filter === 'active') {
    $query .= " WHERE r.cancelled = 0 AND r.date > NOW()";
  } elseif ($filter === 'archived') {
    $query .= " WHERE r.cancelled = 0 AND r.date <= NOW()";
  } elseif ($filter === 'cancelled') {
    $query .= " WHERE r.cancelled = 1";
  }

  $stmt = $pdo->prepare($query);
  $stmt->execute();
  $reservations = $stmt->fetchAll();

  $data = [];
  foreach ($reservations as $reservation) {
    $data[] = [
      'date' => $reservation['date'],
      'user_id' => $reservation['user_id'],
      'frizer_id' => $reservation['frizer_id'],
      'treatment_id' => $reservation['treatment_id'],
      'comment' => $reservation['comment'],
      'cancelled' => $reservation['cancelled']
    ];
  }

  echo json_encode(['data' => $data]);
  exit();
}

try {
  $query = "
    SELECT t.treatment_id, t.salon_id, t.treatment_name, t.treatment_price
    FROM treatment t
    JOIN salon_owner so ON t.salon_id = so.salon_owner_id
    WHERE so.salon_owner_id = :salon_owner_id
";

  $stmt = $pdo->prepare($query);
  $stmt->bindParam(':salon_owner_id', $salon_id_owner, PDO::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}



$currentDateTime = date('Y-m-d H:i:s');

$query = "
  SELECT r.reservation_id, u.username, r.date, r.start_time, r.end_time, u.email 
  FROM reservations r 
  JOIN users u ON r.user_id = u.user_id 
  WHERE r.store_id = ? AND CONCAT(r.date, ' ', r.end_time) > ? AND r.cancelled = 0
";
$stmt = $pdo->prepare($query);
$stmt->execute([$salon_id_owner, $currentDateTime]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $reservationId = $_POST['reservation_id'];
  $customMessage = $_POST['message'];

  $query = "
    SELECT u.email, u.first_name, u.last_name 
    FROM reservations r 
    JOIN users u ON r.user_id = u.user_id 
    WHERE r.reservation_id = ?
  ";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$reservationId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    $email = $user['email'];
    $firstName = $user['first_name'];
    $lastName = $user['last_name'];

    sendVerificationEmail($email, $customMessage, $lastName, $firstName);

    // Update reservation to cancelled
    $updateQuery = "UPDATE reservations SET cancelled = 1 WHERE reservation_id = ?";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([$reservationId]);

    echo "Reservation cancelled successfully.";
  }
}

function sendVerificationEmail($email, $customMessage, $lastName, $firstName)
{
  $mail = new PHPMailer(true);

  try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';

    $mail->Username = 'vtsfrizer@gmail.com';
    $mail->Password = 'uyuy ltjk rzfe mwvl';

    $mail->setFrom('vtsfrizer@gmail.com', '');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Reservation Cancellation';
    $mail->Body = "
            Hello $lastName $firstName,<br><br>
            $customMessage<br><br>
            Your reservation has been canceled.
        ";

    $mail->send();
    echo "Email sent successfully.";
  } catch (Exception $e) {
    echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
  }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reservations</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
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
        <!-- Reservations Section -->
        <div class="row">
            <div class="col-12">
                <div class="text-center mb-4">
                    <button id="btn-active" class="btn header-btn">Active Reservations</button>
                    <button id="btn-archived" class="btn header-btn">Archived Reservations</button>
                    <button id="btn-cancelled" class="btn header-btn">Cancelled Reservations</button>
                </div>
                
                <br><br>
                <table id="reservations-table" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Frizer ID</th>
                            <th>Treatment ID</th>
                            <th>Comment</th>
                            <th>Cancelled</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Extra margin here -->
        <div class="row my-5"></div>
        <!-- Extra margin ends -->
        
        <!-- Treatments Section -->
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-4">SZOLGÁLTATÁSOK ADATAI</h2>
                <div class="text-center mb-4">
                    <button class="btn header-btn" data-toggle="modal" data-target="#addTreatmentModal">Szolgáltatás Hozzáadása</button>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Salon ID</th>
                            <th>Szolgáltatás neve</th>
                            <th>Ár</th>
                            <th>Művelet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['salon_id']) ?></td>
                                <td><?= htmlspecialchars($row['treatment_name']) ?></td>
                                <td><?= htmlspecialchars($row['treatment_price']) ?> $</td>
                                <td>
                                    <button class="btn header-btn" data-toggle="modal" data-target="#editModal" data-id="<?= $row['treatment_id'] ?>" data-name="<?= htmlspecialchars($row['treatment_name']) ?>" data-price="<?= $row['treatment_price'] ?>">Szerkesztes</button>
                                    <button class="btn header-btn delete-btn" data-id="<?= $row['treatment_id'] ?>">Törlés</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Nem mukodik -->
        <!-- Add Treatment Modal -->
        <div class="modal fade" id="addTreatmentModal" tabindex="-1" role="dialog" aria-labelledby="addTreatmentModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="insert_treatment.php" method="POST">
                            <div class="form-group">
                                <label for="addTreatmentName">Szolgáltatás neve</label>
                                <input type="text" class="form-control" id="addTreatmentName" name="treatment_name" required>
                            </div>
                            <div class="form-group">
                                <label for="addTreatmentPrice">Ár</label>
                                <input type="number" class="form-control" id="addTreatmentPrice" name="treatment_price" step="0.01" required>
                            </div>
                            <input type="hidden" name="salon_id" value="<?= htmlspecialchars($salon_id_owner); ?>"> 
                            <button type="submit" class="btn">Mentés</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik -->
       <!-- Edit Treatment Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="edit_treatment.php" method="POST">
                    <input type="hidden" id="editTreatmentId" name="treatment_id">
                    <div class="form-group">
                        <label for="editTreatmentName">Szolgáltatás neve</label>
                        <input type="text" class="form-control" id="editTreatmentName" name="treatment_name" required>
                    </div>
                    <div class="form-group">
                        <label for="editTreatmentPrice">Ár</label>
                        <input type="number" class="form-control" id="editTreatmentPrice" name="treatment_price" step="0.01" required>
                    </div>
                    <button type="submit" class="btn">Mentés</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik -->

<!-- Treatments Section -->
<div class="row my-5"></div>
<div class="row">
    <div class="col-12">
        <h2 class="text-center mb-4">Cancel</h2>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Date</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($reservation['username']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['date']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($reservation['end_time']); ?></td>
                        <td>
                            <button class="btn" onclick="openCancelModal('<?php echo $reservation['reservation_id']; ?>', '<?php echo $reservation['email']; ?>')">Cancel</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik -->
<!-- Modal -->


<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cancelModalLabel">Cancel Reservation</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Sending email to: <span id="userEmail"></span></p>
          <form method="POST">
            <input type="hidden" id="reservationId" name="reservation_id">
            <div class="form-group">
              <label for="message">Message:</label>
              <textarea class="form-control" id="message" name="message" rows="4" maxlength="500" placeholder="Write your message here (max 500 characters)"></textarea>
              <small class="form-text text-muted">Maximum 500 characters.</small>
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </form>
        </div>
      </div>
    </div>
  </div>


</main>


<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
  </script>
  <script>
    function openCancelModal(reservationId, userEmail) {
      $('#reservationId').val(reservationId);
      $('#userEmail').text(userEmail);
      $('#cancelModal').modal('show');
    }
  </script>

<!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik --><!-- Nem mukodik -->





<!-- Scripts -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        // Edit button functionality
        $('.edit-btn').on('click', function() {
            var treatmentId = $(this).data('id');
            var treatmentName = $(this).data('name');
            var treatmentPrice = $(this).data('price');

            $('#editTreatmentId').val(treatmentId);
            $('#editTreatmentName').val(treatmentName);
            $('#editTreatmentPrice').val(treatmentPrice);
        });

        // Delete button functionality
        $('.delete-btn').on('click', function() {
            var treatmentId = $(this).data('id');
            var confirmation = confirm('Are you sure you want to permanently delete this treatment?');

            if (confirmation) {
                window.location.href = 'delete_treatment.php?id=' + treatmentId;
            }
        });

        // Reservations table
        var table = $('#reservations-table').DataTable({
            "ajax": {
                "url": "",
                "type": "POST",
                "data": function(d) {
                    d.filter = filter;
                }
            },
            "columns": [
                {"data": "date"},
                {"data": "user_id"},
                {"data": "frizer_id"},
                {"data": "treatment_id"},
                {"data": "comment"},
                {"data": "cancelled"}
            ]
        });

        var filter = 'all';

        $('#btn-active').click(function() {
            filter = 'active';
            table.ajax.reload();
        });

        $('#btn-archived').click(function() {
            filter = 'archived';
            table.ajax.reload();
        });

        $('#btn-cancelled').click(function() {
            filter = 'cancelled';
            table.ajax.reload();
        });
    });
</script>

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
