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
    $mail -> Username = 'synergyteamken@gmail.com';
    $mail -> Password = 'synergyteamken101';
    $mail -> SMTPSecure = 'tls';
    $mail -> Port = 587;

    $mail -> setFrom('synergyteamken@gmail.com', 'noreply');
    $mail -> addAddress($email);

    $mail -> isHTML(true);
    $mail -> Subject = 'Email Verification from CDSSA';

    $email_template = "";

    $mail -> Body = $email_template;
    $mail -> send();
}

?>