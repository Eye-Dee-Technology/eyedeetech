<?php

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$service = $_POST['service'];
$date = $_POST['date'];
$time = $_POST['time'];
$message = $_POST['message'];

$to = "hello@eyedeetech.com";

$subject = "New Appointment Booking";

$body = "
New Appointment Request

Name: $name
Email: $email
Phone: $phone
Service: $service
Date: $date
Time: $time

Message:
$message
";

$headers = "From: $email";

mail($to, $subject, $body, $headers);

header("Location: success.html");

?>