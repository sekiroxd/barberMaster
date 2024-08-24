<?php
require_once 'db_config.php';

session_start();

if (isset($_SESSION['salon_owner_id'])) {
  $salon_id_owner = $_SESSION['salon_owner_id'];
  // echo "Salon ID Owner: " . $salon_id_owner; // For testing purposes
} else {
  echo "No salon_owner_id found in session.";
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
  $salon_id_owner = $_SESSION['salon_owner_id'];

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

  // Prepare the statement
  $stmt = $pdo->prepare($query);

  $stmt->bindParam(':salon_owner_id', $salon_id_owner, PDO::PARAM_INT);

  $stmt->execute();

  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Reservations</title>
  <link rel="stylesheet" href="assets/css/salon.css">
  <script src="assets/css/salon.css"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="shortcut icon" type="image/x-icon" href="assets/img/ollo.jpg">

  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 20px;
      padding: 0;
      text-align: center;
      /* Center align text content */
    }

    h1 {
      margin-bottom: 20px;
      /* Add some space below the heading */
    }

    button {
      margin: 10px;
      padding: 8px 16px;
      font-size: 14px;
      cursor: pointer;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
    }

    button:hover {
      background-color: #0056b3;
    }

    #reservations-table {
      width: 90%;
      /* Adjust table width */
      margin: 20px auto;
      /* Center align the table */
      border-collapse: collapse;
    }

    #reservations-table th,
    #reservations-table td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: center;
    }
  </style>
</head>
</head>

<body>
  <h1>Reservations</h1>
  <button id="btn-active">Active Reservations</button>
  <button id="btn-archived">Archived Reservations</button>
  <button id="btn-cancelled">Cancelled Reservations</button>
  <br><br>
  <a href="cancel.php">Cancel</a>

  <table id="reservations-table" class="display">
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

  <br>
  <h2>SZOLGÁLTATÁSOK ADATAI</h2>
  <span><button class="btn btn-primary addTreatmentBtn" data-toggle="modal" data-target="#addTreatmentModal">Szolgáltatás Hozzáadása</button></span>
  <br>


  <table class="table">
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
            <button class="btn btn-primary edit-btn" data-toggle="modal" data-target="#editModal" data-id="<?= $row['treatment_id'] ?>" data-name="<?= htmlspecialchars($row['treatment_name']) ?>" data-price="<?= $row['treatment_price'] ?>">
              Szerkesztes
            </button>
            <button class="btn btn-danger delete-btn" data-id="<?= $row['treatment_id'] ?>">
              Törlés
            </button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <!-- Add Treatment Modal -->
  <div class="modal fade" id="addTreatmentModal" tabindex="-1" role="dialog" aria-labelledby="addTreatmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTreatmentModalLabel">Add New Treatment</h5>
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
            <button type="submit" class="btn btn-primary">Add Treatment</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Treatment</h5>
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
            <button type="submit" class="btn btn-primary">Save changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <script>
    $(document).ready(function() {
      $('.edit-btn').on('click', function() {
        var treatmentId = $(this).data('id');
        var treatmentName = $(this).data('name');
        var treatmentPrice = $(this).data('price');

        $('#editTreatmentId').val(treatmentId);
        $('#editTreatmentName').val(treatmentName);
        $('#editTreatmentPrice').val(treatmentPrice);
      });
    });
  </script>

  <script>
    $(document).ready(function() {
      $('.delete-btn').on('click', function() {
        var treatmentId = $(this).data('id');
        var confirmation = confirm('Are you sure you want to permanently delete this treatment?');

        if (confirmation) {
          window.location.href = 'delete_treatment.php?id=' + treatmentId;
        }
      });
    });
  </script>


  <script>
    $(document).ready(function() {
      var table = $('#reservations-table').DataTable({
        "ajax": {
          "url": "",
          "type": "POST",
          "data": function(d) {
            d.filter = filter;
          }
        },
        "columns": [{
            "data": "date"
          },
          {
            "data": "user_id"
          },
          {
            "data": "frizer_id"
          },
          {
            "data": "treatment_id"
          },
          {
            "data": "comment"
          },
          {
            "data": "cancelled"
          }
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
</body>

</html>