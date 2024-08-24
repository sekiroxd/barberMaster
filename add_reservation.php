<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['selectedDate']) || empty($_POST['selectedDate']) || !isset($_POST['selectedTime']) || empty($_POST['selectedTime']) || !isset($_POST['comment']) || empty($_POST['comment'])) {
        header('Location: reservation.php?e=1');
        exit;
    }

    $selectedDate = $_POST['selectedDate'];
    $selectedTime = $_POST['selectedTime'];
    $comment = $_POST['comment'];
    $frizer_id = $_POST['frizer_id'];
    $treatment_id = $_POST['treatment_id'];
    $user_id = $_POST['user_id'];
    $cancelled = 0;

    $duration = 30;
    $selectedTimeFormatted = $selectedTime;
    $startDateTime = new DateTime("$selectedDate $selectedTimeFormatted");
    $endDateTime = clone $startDateTime;
    $endDateTime->modify("+$duration minutes");
    $endTime = $endDateTime->format('H:i');

    try {
        $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
        $query = "INSERT INTO reservations (store_id, date, start_time, end_time, user_id, frizer_id, treatment_id, comment) VALUES (1, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$selectedDate, $selectedTimeFormatted, $endTime, $user_id, $frizer_id, $treatment_id, $comment]);

        header('Location: reservation.php?e=3');
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}
