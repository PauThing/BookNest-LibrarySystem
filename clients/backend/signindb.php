<?php
if (isset($_POST['signin'])) {
    session_start();
    include('../connect.php');

    $userid = $_POST['uID'];
    $password = $_POST['password'];

    $query = sqlsrv_query($conn, "SELECT * FROM [user] WHERE [user_id] = '$userid'");

    // Fetch the result
    $row = sqlsrv_fetch_array($query);

    $hashedPassword = $row['user_password'];

    if ($row) {
        $status = $row['acc_status'];
        if ($status == 'Approved') {
            if (password_verify($password, $hashedPassword)) {
                $usertype = $row['usertype'];

                switch ($usertype) {
                    case 'Student':
                        $_SESSION['userid'] = $row['user_id'];
                        $_SESSION['loggedin'] = true;
                        $_SESSION['message'] = "Sign in successful!";
                        header('location: ../index.php');
                        break;

                    case 'Admin':
                        $_SESSION['userid'] = $row['user_id'];
                        $_SESSION['loggedin'] = true;
                        $_SESSION['message'] = "Sign in successful!";
                        header('location: ../index.php');
                        break;
                }
            } else {
                $_SESSION['message'] = "Sign in failed. Incorrect password.";
                header('location: ../signin.php');
            }
        } else {
            $_SESSION['message'] = "Sign in failed. Please wait for the approval from administrator.";
            header('location: ../signin.php');
        }
    } else {
        $_SESSION['message'] = "Sign in failed. User not found.";
        header('location: ../signin.php');
    }
} else {
    $_SESSION['message'] = "Please enter your username and password.";
    header('location: ../signin.php');
}
?>