<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$message = $_POST['message'] ?? '';

if ($name && $email && $phone && $message) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->setFrom($_ENV['SMTP_USERNAME'], 'Feedback Form');
        $mail->addAddress($_ENV['SMTP_USERNAME']);
        $mail->isHTML(true);
        $mail->Subject = 'New Feedback from ' . $name;
        $mail->Body = "<strong>Name:</strong> $name<br>
                       <strong>Email:</strong> $email<br>
                       <strong>Phone:</strong> $phone<br>
                       <strong>Message:</strong> $message";

        $mail->send();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode(['status' => 'success', 'message' => 'Feedback sent successfully!']);
        }
    } catch (Exception $e) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Error sending email: ' . $mail->ErrorInfo]);
        }
    }
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    }
}
?>

