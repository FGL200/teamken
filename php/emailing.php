<?php

session_start();
include('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// EMAIL CONFIRMATION

function synergy($surname, $firstname, $middlename, $age, $birthday, $address, $email, $reference, $confirmation_token) {
    $mail = new PHPMailer(true);

    $mail -> isSMTP();
    $mail -> SMTPAuth = true;

    $mail -> Host = 'smtp.gmail.com';
    $mail -> Username = 'ceignacio@rtu.edu.ph';
    $mail -> Password = 'cha01000011';
    $mail -> SMTPSecure = 'tls';
    $mail -> Port = 587;

    $mail -> setFrom('synergyteamken@gmail.com', 'noreply');
    $mail -> addAddress($email);

    $mail -> isHTML(true);
    $mail -> Subject = 'Confirmation Email from Converge';

    $email_template = "
        Good day, Mr./Ms./Mrs. $surname! <br><br>

        This email serves as confirmation that we have received your application for Converge. <br><br>

        Thank you! <br><br>

        These are the information you provided. <br><br>

        Surname: <b> $surname </b><br>
        First Name: <b> $firstname </b><br>
        Middle Name: <b> $middlename </b><br>
        Age: <b> $age </b><br>
        Birthday: <b> $birthday </b><br>
        Address: <b> $address </b><br><br>

        Reference Number: <b>$reference </b><br><br>

        Click the link below if you confirm. <br>

        <a href='http://localhost/teamken/php/confirmation.php?token=$confirmation_token'> Click here to Confirm Application </a> <br><br>


        Click the link below if it is not you. (Delete informations from database) <Br>

        <a href='http://localhost/teamken/php/deny-confirmation.php?token=$confirmation_token'> Click here to Deny Application </a>
    ";

    $mail -> Body = $email_template;
    $mail -> send();
}


if(isset($_POST['apply_btn'])) {
    $surname = $_POST['sname'];
    $firstname = $_POST['fname'];
    $middlename = $_POST['mname'];
    $age = $_POST['age'];
    $birthday = $_POST['birthday'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $reference = rand(1000000000, 9999999999);

    $confirmation_token = md5(rand());

    $check_email_query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
    $check_email_query_run = mysqli_query($con, $check_email_query);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['status'] = "Invalid Email Address.";
        $_SESSION['status_code'] = "error";
        header("Location: emailtester.php");
    }

    else if (mysqli_num_rows($check_email_query_run) > 0) {
        $_SESSION['status'] = "Email Address has already exists.";
        $_SESSION['status_code'] = "error";
        header("Location: emailtester.php");
    }

    else {
        $query = "INSERT INTO users (surname, firstname, middlename, age, birthday, address, email, reference, confirmation_token) VALUES ('$surname', '$firstname', '$middlename', '$age', '$birthday', '$address', '$email', '$reference', '$confirmation_token')";
        $query_run = mysqli_query($con, $query);

        if ($query_run) {
            synergy($surname, $firstname, $middlename, $age, $birthday, $address, $email, $reference, $confirmation_token);
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