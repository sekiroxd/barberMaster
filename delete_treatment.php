<?php
require_once 'db_config.php';
session_start();

if (isset($_SESSION['salon_owner_id']) && isset($_GET['id'])) {
  $salon_id_owner = $_SESSION['salon_owner_id'];
  $treatment_id = (int) $_GET['id'];

  try {
    $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $options);

    $stmt = $pdo->prepare("DELETE FROM treatment WHERE treatment_id = :treatment_id AND salon_id = :salon_id");
    $stmt->bindParam(':treatment_id', $treatment_id, PDO::PARAM_INT);
    $stmt->bindParam(':salon_id', $salon_id_owner, PDO::PARAM_INT);

    if ($stmt->execute()) {
      header('Location: edit_salon1.php'); // Redirect to the page where the table is displayed
      exit();
    } else {
      echo "Error deleting treatment.";
    }
  } catch (PDOException $e) {
    die("Error: " . $e->getMessage());
  }
} else {
  echo "Invalid request.";
}
