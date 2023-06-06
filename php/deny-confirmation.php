<?php

session_start();
include('dbcon.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $deny_query = "SELECT confirmation_token, confirmation FROM users WHERE confirmation_token='$token' LIMIT 1";
    $deny_query_run = mysqli_query($con, $deny_query);

    if (mysqli_num_rows($deny_query_run) > 0) {
        $row = mysqli_fetch_array($deny_query_run);
        
        if ($row['confirmation'] == 0) {
            $clicked_token = $row['confirmation_token'];
            $delete_query = "DELETE FROM users WHERE confirmation_token='$token' LIMIT 1";
            $delete_query_run = mysqli_query($con, $delete_query);

            if ($delete_query_run) {
                $_SESSION['status'] = "Application Denied. (Informations are removed from database)";
                $_SESSION['status_code'] = "success";
                header("Location: emailtester.php");
                exit(0);
            }
            
            else {
                $_SESSION['status'] = "Denial Error.";
                $_SESSION['status_code'] = "error";
                header("Location: emailtester.php");
                exit(0);
            }
        }

        else {
            $_SESSION['status'] = "Can't process request.";
            $_SESSION['status_code'] = "error";
            header("Location: emailtester.php");
            exit(0);
        }
    }

    else {
        $_SESSION['status'] = "Can't process request.";
        $_SESSION['status_code'] = "error";
        header("Location: emailtester.php");
        exit(0);
    }
}

else {
    $_SESSION['status'] = "Can't process request.";
    $_SESSION['status_code'] = "error";
    header("Location: emailtester.php");
    exit(0);
}

?>