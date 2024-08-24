<?php
require_once 'db_config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve form data
  $treatmentName = $_POST['treatment_name'];
  $treatmentPrice = $_POST['treatment_price'];
  $salonId = $_POST['salon_id'];

  try {
    // Prepare and execute the insert query
    $stmt = $pdo->prepare("INSERT INTO treatment (salon_id, treatment_name, treatment_price) VALUES (:salon_id, :treatment_name, :treatment_price)");
    $stmt->bindParam(':salon_id', $salonId, PDO::PARAM_INT);
    $stmt->bindParam(':treatment_name', $treatmentName, PDO::PARAM_STR);
    $stmt->bindParam(':treatment_price', $treatmentPrice, PDO::PARAM_STR);

    if ($stmt->execute()) {
      // Redirect or display success message
      header('Location: edit_salon1.php'); // Replace with your actual page
      exit();
    } else {
      echo "Error adding treatment.";
    }
  } catch (PDOException $e) {
    die("Error inserting into the database: " . $e->getMessage());
  }
} else {
  echo "Invalid request.";
}
