<?php
require 'db_config.php';
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit();
}

$username = $_SESSION['username'];

$target_dir = "pictures/";
$response = array();

if (empty($_FILES["fileToUpload"]["name"])) {
  $_SESSION['error_message'] = "Válassz ki egy képet mielőtt feltöltenéd.";
  header("Location: profile.php");
  exit();
}

$target_dir = "pictures/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
if ($check === false) {
  $_SESSION['error_message'] = "A fájl nem kép.";
  header("Location: profile.php");
  exit();
}

if ($_FILES["fileToUpload"]["size"] > 500000) {
  $_SESSION['error_message'] = "A kép túl nagy.";
  header("Location: profile.php");
  exit();
}

if (!in_array($imageFileType, array("jpg", "png", "jpeg", "gif"))) {
  $_SESSION['error_message'] = "Csak JPG, PNG, JPEG és GIF formátomú fájlokat lehet feltölteni.";
  header("Location: profile.php");
  exit();
}

if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
  $photoName = htmlspecialchars(basename($_FILES["fileToUpload"]["name"]));

  try {
    $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions); // Modified database connection
    $stmt = $pdo->prepare("UPDATE users SET pic_name = ? WHERE username = ?");
    $stmt->execute([$photoName, $username]);
    $_SESSION['success_message'] = "$photoName Sikeresen feltöltődöt.";
    header("Location: profile.php");
    exit();
  } catch (PDOException $e) {
    $_SESSION['error_message'] = "Hiba a kép feltöltésével kapcsolatban: " . $e->getMessage();
    header("Location: profile.php");
    exit();
  }
} else {
  $_SESSION['error_message'] = "Hiba történt a kép feltöltése során.";
  header("Location: profile.php");
  exit();
}
