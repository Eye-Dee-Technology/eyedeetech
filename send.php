<?php

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/mail_errors.log');

// Load PHPMailer
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // SANITIZE INPUTS
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $company = htmlspecialchars($_POST['company'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $budget = htmlspecialchars($_POST['budget'] ?? '');
    $timeline = htmlspecialchars($_POST['timeline'] ?? '');
    $project_type = htmlspecialchars($_POST['project_type'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    // Log the submission
    $log_message = date('Y-m-d H:i:s') . " - Form submitted by: $name ($email)\n";
    error_log($log_message);

    try {
        // Initialize PHPMailer
        $mail = new PHPMailer(true);
        
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'mail.privateemail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@eyedeetech.com';
        $mail->Password = 'Damilola200$$';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->Timeout = 10;
        $mail->SMTPDebug = 0;

        // Sender
        $mail->setFrom('info@eyedeetech.com', 'Eye Dee Tech');
        $mail->addReplyTo($email, $name);

        // Recipient
        $mail->addAddress('info@eyedeetech.com');

        // Subject & Body
        $mail->isHTML(false);
        $mail->Subject = 'New Project Brief from Website';
        $mail->Body = "
Name: $name

Email: $email

Company: $company

Phone: $phone

Budget: $budget

Timeline: $timeline

Project Type: $project_type

Message:
$message
";

        // Handle file attachment
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
            $upload_dir = sys_get_temp_dir();
            $temp_file = $upload_dir . '/' . $_FILES['attachment']['name'];
            move_uploaded_file($_FILES['attachment']['tmp_name'], $temp_file);
            $mail->addAttachment($temp_file, $_FILES['attachment']['name']);
        }

        // SEND EMAIL
        $mail_sent = $mail->send();
        
        error_log(date('Y-m-d H:i:s') . " - Mail sent successfully to info@eyedeetech.com\n");

        // AUTO-REPLY TO CLIENT
        $reply = new PHPMailer(true);
        
        $reply->isSMTP();
        $reply->Host = 'mail.privateemail.com';
        $reply->SMTPAuth = true;
        $reply->Username = 'info@eyedeetech.com';
        $reply->Password = 'Damilola200$$';
        $reply->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $reply->Port = 465;

        $reply->setFrom('info@eyedeetech.com', 'Eye Dee Tech');
        $reply->addAddress($email, $name);
        $reply->isHTML(false);
        $reply->Subject = 'We received your project brief';
        $reply->Body = "Hi $name,

Thank you for contacting Eye Dee Tech.

We have received your project brief and our team will review it shortly.

We'll get back to you soon.

Regards,
Eye Dee Tech";

        try {
            $reply->send();
        } catch (Exception $e) {
            error_log(date('Y-m-d H:i:s') . " - Auto-reply failed: " . $e->getMessage() . "\n");
        }

        // REDIRECT
        header("Location: thank-you.html");
        exit();

    } catch (Exception $e) {
        error_log(date('Y-m-d H:i:s') . " - SMTP Error: " . $e->getMessage() . "\n");
        echo "Error sending email: " . htmlspecialchars($e->getMessage());
    }

}

?>
