<?php
if (isset($_POST['signin'])) {
    session_start();
    include('../../clients/connect.php');

    $userid = $_POST['uID'];
    $password = $_POST['password'];

    $query = "SELECT * FROM [user] WHERE [user_id] = ?";
    $array = [$userid];
	$statement = sqlsrv_query($conn, $query, $array);
	$row = sqlsrv_fetch_array($statement);

    $hashedPassword = $row['user_password'];

    if ($row) {
        $status = $row['acc_status'];
        if ($status == 'Approved') {
            if (password_verify($password, $hashedPassword)) {
                $usertype = $row['usertype'];

                switch ($usertype) {
                    case 'Student':
                        $_SESSION['userid'] = $row['user_id'];
                        $_SESSION['usertype'] = $usertype;
                        $_SESSION['loggedin'] = true;
                        header('location: ../index.php');
                        break;

                    case 'Admin':
                        $_SESSION['userid'] = $row['user_id'];
                        $_SESSION['usertype'] = $usertype;
                        $_SESSION['loggedin'] = true;
                        header('location: ../../admin/index.php');
                        break;

                    case 'SuperAdmin':
                        $_SESSION['userid'] = $row['user_id'];
                        $_SESSION['usertype'] = $usertype;
                        $_SESSION['loggedin'] = true;
                        header('location: ../../admin/index.php');
                        break;
                }
            } else {
                $_SESSION['message'] = "Sign in failed. Incorrect password.";
                header('location: ../../clients/signin.php?st=error');
            }
        } else {
            $_SESSION['message'] = "Sign in failed. Please wait for the approval from administrator.";
            header('location: ../../clients/signin.php?st=error');
        }
    } else {
        $_SESSION['message'] = "Sign in failed. User not found.";
        header('location: ../../clients/signin.php?st=error');
    }
} else {
    $_SESSION['message'] = "Please enter your username and password.";
    header('location: ../../clients/signin.php?st=error');
}
?>