<?php
if (isset($_POST['signin'])) {
    session_start();
    include('../connect.php');

    $userid = $_POST['uID'];
    $password = $_POST['password'];

    $query = sqlsrv_query($conn, "SELECT * FROM [user] WHERE [user_id] = '$userid' AND [user_password] = '$password'");

    // Fetch the result
    $row = sqlsrv_fetch_array($query);

    if ($row) {
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
        $_SESSION['message'] = "Sign in failed. Please check your user ID and password.";
        header('location: ../signin.php');
    }
} else {
    $_SESSION['message'] = "Please enter your username and password.";
    header('location: ../signin.php');
}
?>