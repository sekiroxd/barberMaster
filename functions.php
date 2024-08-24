<?php
function getReservationsData($date)
{
    global $dsn, $pdoOptions;
    try {
        $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
        $stmt = $pdo->prepare("SELECT start_time AS reservation_time FROM reservations WHERE date = ? AND cancelled = 0 AND store_id = 1");
        $stmt->execute([$date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error fetching reservations: " . $e->getMessage());
    }
}

function getAllReservations()
{
    global $dsn, $pdoOptions;
    try {
        $pdo = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);
        $query = "SELECT * FROM reservations WHERE cancelled = 0";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}


function reservationOverlaps($reservationStart, $buttonStart, $buttonEnd)
{
    $reservationStartTimestamp = strtotime($reservationStart);
    $buttonStartTime = strtotime($buttonStart);
    $buttonEndTime = strtotime($buttonEnd);

    return ($reservationStartTimestamp >= $buttonStartTime && $reservationStartTimestamp < $buttonEndTime);
}

function timeslots($duration, $cleanup, $start, $end)
{
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT" . $duration . "M");
    $cleanupInterval = new DateInterval("PT" . $cleanup . "M");
    $slots = array();

    for ($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupInterval)) {
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if ($endPeriod >= $end) {
            break;
        }
        $slots[] = array(
            'start' => $intStart->format("H:i"),
            'end' => $endPeriod->format("H:i")
        );
    }
    return $slots;
}
