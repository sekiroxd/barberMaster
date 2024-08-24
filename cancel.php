<?php
require_once 'phpmailer/PHPMailerAutoload.php';
require_once 'db_config.php';

session_start();

if (isset($_SESSION['salon_owner_id'])) {
  $salon_id_owner = $_SESSION['salon_owner_id'];
} else {
  echo "No salon_owner_id found in session.";
  exit();
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
  <title>Active Reservations</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }

    button {
      padding: 5px 10px;
    }
  </style>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>

<body>

  <table>
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
            <button class="btn btn-danger" onclick="openCancelModal('<?php echo $reservation['reservation_id']; ?>', '<?php echo $reservation['email']; ?>')">Cancel</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

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

  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
  </script>
  <script>
    function openCancelModal(reservationId, userEmail) {
      $('#reservationId').val(reservationId);
      $('#userEmail').text(userEmail);
      $('#cancelModal').modal('show');
    }
  </script>
</body>

</html>