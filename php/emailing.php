<?php

session_start();
include('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// EMAIL CONFIRMATION

function admin_verify($name, $email, $verify_token) {
    $mail = new PHPMailer(true);

    $mail -> isSMTP();
    $mail -> SMTPAuth = true;

    $mail -> Host = 'smtp.gmail.com';
    $mail -> Username = 'cdssa@rtu.edu.ph';
    $mail -> Password = 'rizaltech';
    $mail -> SMTPSecure = 'tls';
    $mail -> Port = 587;

    $mail -> setFrom('cdssa@rtu.edu.ph', 'noreply');
    $mail -> addAddress($email);

    $mail -> isHTML(true);
    $mail -> Subject = 'Email Verification from CDSSA';

    $email_template = "
    <h2>You have registered with CDSSA</h2>
    <h5>Verify your email address to login by clicking the link below.</h5>
    <br/><br/>
    <a href='http://localhost/cdssa/backend/admin-verify.php?token=$verify_token'> Click Here </a>
";

    $mail -> Body = $email_template;
    $mail -> send();
}

?>