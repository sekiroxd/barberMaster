<?php
//session_start();

require_once "phpmailer/PHPMailerAutoload.php";
require 'db_config.php';

function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateToken()
{
    return bin2hex(random_bytes(32)); // Generate a 64-character hex token
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = sanitize($_POST['userName']);
    $lastName = sanitize($_POST['lastName']);
    $firstName = sanitize($_POST['firstName']);
    $mobile = $_POST['mobile'];
    $email = sanitize($_POST['email']);
    $password = sanitize($_POST['password']);

    $token = generateToken();

    try {
        $conn = new PDO($dsn, PARAMS['USER'], PARAMS['PASSWORD'], $pdoOptions);

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            echo '<script>alert("Ez az email már foglalt!");</script>';
            echo 'window.location.href = "register.php";';
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['errors'] = "Error: " . $e->getMessage();
        header('Location: register.php');
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (username, last_name, first_name, mobile, email, password, token, active) 
            VALUES (:username, :last_name, :first_name, :mobile, :email, :password, :token, 0)");

        $stmt->bindParam(':username', $userName);
        $stmt->bindParam(':last_name', $lastName);
        $stmt->bindParam(':first_name', $firstName);
        $stmt->bindParam(':mobile', $mobile);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':token', $token);

        $stmt->execute();

        // Send verification email
        sendVerificationEmail($email, $token, $lastName, $firstName);

        echo '<script>alert("Regisztráció sikeres, kérlek nézd meg az emailjeid.");</script>';
        echo '<script>window.location = "index.php";</script>';
        exit;
    } catch (PDOException $e) {
        $_SESSION['errors'] = "Hiba: " . $e->getMessage();
        header('Location: register.php');
        exit;
    } finally {
        $conn = null;
    }
}

function sendVerificationEmail($email, $token, $lastName, $firstName)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';

        $mail->Username = 'vtsfrizer@gmail.com';
        $mail->Password = 'uyuy ltjk rzfe mwvl';

        //Recipients
        $mail->setFrom('vtsfrizer@gmail.com', '');
        $mail->addAddress($email);

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Email validation';
        $mail->Body = "Hello $lastName $firstName! Kérlek kattints a linkre a fiókod aktiváláshoz! :
      https://localhost/BarBerMaster/activate.php?token=" . $token;

        $mail->send();
    } catch (Exception $e) {
        // Handle mail sending error, if needed
        echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
}
