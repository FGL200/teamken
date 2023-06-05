<?php

session_start();
include('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// EMAIL CONFIRMATION

function synergy($surname, $firstname, $middlename, $email, $phone, $reference) {
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

    $email_template = "
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css' rel='stylesheet'>
        Good day, $firstname $middlename $surname! <br><br>

        This email serves as confirmation that we have received your application for Converge. <br>
        Click the link below if you confirm, else disregard this email. <br><br>

        Thank you!

        <center><a class='btn btn-primary' href='http://localhost/teamken/php/confirmation.php?token=$reference'> Confirm Application </a></center>
    ";

    $mail -> Body = $email_template;
    $mail -> send();
}


if(isset($_POST['apply_btn'])) {
    $surname = $_POST['surname'];
    $firstname = $_POST['fname'];
    $middlename = $_POST['mname'];
    $birthday = $_POST['bday'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $reference = md5(rand(15));

    $valphone = (strlen($phone) < 11 || strlen($phone) > 11);

    $check_email_query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
    $check_email_query_run = mysqli_query($con, $check_email_query);

    $check_phone_query = "SELECT phone FROM users WHERE phone='$phone' LIMIT 1";
    $check_phone_query_run = mysqli_query($con, $check_phone_query);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status'] = "Invalid Email Address.";
        $_SESSION['status_code'] = "error";
        header("Location: emailtester.php");
    }

    else if ($valphone) {
        $_SESSION['status'] = "Invalid Phone Number.";
        $_SESSION['status_code'] = "error";
        header("Location: emailtester.php");
    }

    else if (mysqli_num_rows($check_email_query_run) > 0) {
        $_SESSION['status'] = "Email Address has already exists.";
        $_SESSION['status_code'] = "error";
        header("Location: emailtester.php");
    }

    else if (mysqli_num_rows($check_phone_query_run) > 0) {
        $_SESSION['status'] = "Phone Number has already exists.";
        $_SESSION['status_code'] = "error";
        header("Location: emailtester.php");
    }

    else {
        $query = "INSERT INTO users (surname, firstname, middlename, birthday, address, email, phone, reference) VALUES ('$surname', '$firstname', '$middlename', '$birthday', '$address', '$email', '$phone', '$reference')";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            synergy($surname, $firstname, $middlename, $email, $phone, $reference);
            $_SESSION['status'] = "Application Successful. Check you Email for confirmation.";
            $_SESSION['status_code'] = "success";
            header("Location: emailtester.php");
        }

        else {
            $_SESSION['status'] = "Something went wrong.";
            $_SESSION['status_code'] = "error";
            header("Location: emailtester.php");
        }
    }
}

?>