<?php
function getActiveAdminData()
{
  global $pdo;
  $stmt = $pdo->prepare("SELECT * FROM admin ORDER BY archived ASC");
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAdminData($username, $pdo)
{
  $sql = "SELECT * FROM users WHERE username = ?";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([$username]);
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateAdminData($username, $newUsername, $newFirstName, $newLastName, $newEmail, $newPhoneNum, $hashedPassword, $pdo)
{
  if ($hashedPassword !== '') {
    $sql = "UPDATE users SET username = ?, first_name = ?, last_name = ?,  email = ?, mobile = ?, password = ? WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newUsername, $newFirstName, $newLastName, $newEmail, $newPhoneNum, $hashedPassword, $username]);
  } else {
    $sql = "UPDATE users SET username = ?, email = ? WHERE username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$newUsername, $newEmail, $username]);
  }
}
