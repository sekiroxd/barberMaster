<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['treatment_id'])) {
  $treatmentId = $_POST['treatment_id'];
  $treatmentName = $_POST['treatment_name'];
  $treatmentPrice = $_POST['treatment_price'];

  try {
    $stmt = $pdo->prepare("UPDATE treatment SET treatment_name = :treatment_name, treatment_price = :treatment_price WHERE treatment_id = :treatmentId");
    $stmt->bindParam(':treatmentId', $treatmentId, PDO::PARAM_INT);
    $stmt->bindParam(':treatment_name', $treatmentName, PDO::PARAM_STR);
    $stmt->bindParam(':treatment_price', $treatmentPrice, PDO::PARAM_STR);

    if ($stmt->execute()) {
      header('Location: edit_salon1.php');
      exit();
    } else {
      echo "Error updating treatment.";
    }
  } catch (PDOException $e) {
    die("Error updating the database: " . $e->getMessage());
  }
} else {
  echo "Invalid request.";
}
