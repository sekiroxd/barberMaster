<?php
session_start();
require 'db_config.php';

if (isset($_GET['token'])) {

    $token = $_GET['token'];

    try {
        $conn = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);

        $sql = "UPDATE users SET active = '1' WHERE token = :token";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':token', $token);

        if ($stmt->execute()) {
            echo '<script>alert("Fiókod sikeresen aktiválva lett, mostmár bejelentkezhetsz!");</script>';
            echo '<script>window.location.href = "index.php";</script>';
            exit;
        }
    } catch (PDOException $e) {
        echo '<script>alert("Hiba történt a fiók aktiválása közben: ' . $e->getMessage() . '");</script>';
        echo '<script>window.location.href = "index.php";</script>';
        exit;
    } finally {
        $conn = null;
    }
}
