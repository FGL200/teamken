<?php

session_start();
include('dbcon.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $verify_query = "SELECT confirmation_token, confirmation FROM users WHERE confirmation_token='$token' LIMIT 1";
    $verify_query_run = mysqli_query($con, $verify_query);

    if (mysqli_num_rows($verify_query_run) > 0) {
        $row = mysqli_fetch_array($verify_query_run);
        
        if ($row['confirmation'] == 0) {
            $clicked_token = $row['confirmation_token'];
            $update_query = "UPDATE users SET confirmation='1' WHERE confirmation_token='$clicked_token' LIMIT 1";
            $update_query_run = mysqli_query($con, $update_query);

            if ($update_query_run) {
                $_SESSION['status'] = "Application Confirmed.";
                $_SESSION['status_code'] = "success";
                header("Location: ../index.php");
                exit(0);
            }
            
            else {
                $_SESSION['status'] = "Confirmation Error.";
                $_SESSION['status_code'] = "error";
                header("Location: ../index.php");
                exit(0);
            }
        }

        else {
            $_SESSION['status'] = "Can't process request.";
            $_SESSION['status_code'] = "error";
            header("Location: ../index.php");
            exit(0);
        }
    }

    else {
        $_SESSION['status'] = "Can't process request.";
        $_SESSION['status_code'] = "error";
        header("Location: ../index.php");
        exit(0);
    }
}

else {
    $_SESSION['status'] = "Can't process request.";
    $_SESSION['status_code'] = "error";
    header("Location: ../index.php");
    exit(0);
}

?>