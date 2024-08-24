<?php

require 'db_config.php';


function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    $rating = sanitize($_POST['rating']);

    try{

        $conn = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);

    $stmt = $conn->prepare("SELECT * FROM salons WHERE name = :name AND address = :address AND city = :city");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->execute();
    $existingSalon = $stmt->fetch();

    if ($existingSalon) {
        echo '<script>alert("Ez a szalon már létezik!");</script>';
        echo '<script>window.location.href = "salons.php";</script>';
        exit;
    }

    // Szalon beszúrása az adatbázisba
    $stmt = $conn->prepare("INSERT INTO salons (name, address, city, rating) VALUES (:name, :address, :city, :rating)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':rating', $rating);

    if ($stmt->execute()) {
        echo '<script>alert("Szalon sikeresen hozzáadva.");</script>';
        echo '<script>window.location.href = "add_salon_form.html";</script>';
    } else {
        echo '<script>alert("Hiba történt a szalon hozzáadása során.");</script>';
        echo '<script>window.location.href = "add_salon_form.html";</script>';
    }
} catch (PDOException $e) {
    echo '<script>alert("Hiba: ' . $e->getMessage() . '");</script>';
    echo '<script>window.location.href = "add_salon_form.html";</script>';
    exit;
}

    }

   


