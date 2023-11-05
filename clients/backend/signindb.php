<?php
session_start();
include('../../clients/connect.php');

//import PHPMailer classes into global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//load composer's autoloader
require '../../vendor/autoload.php';

$mail = new PHPMailer(true);

//server settings
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->SMTPAuth = true;
$mail->Username = 'booknest.online@gmail.com';
$mail->Password = 'dqht hncw makb ktmj';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;

function calLateFees($conn)
{
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $currDate = date('Y-m-d');

    $status = "On Loan";

    // Query the database to get books that are overdue
    $query = "SELECT * FROM [borrowinghistory] WHERE [status] = ?";
    $array = [$status];
    $statement = sqlsrv_query($conn, $query, $array);

    if ($statement === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    while ($row = sqlsrv_fetch_array($statement)) {
        $bisbn = $row['ISBN'];

        $dueDate = $row['due_at'];
        $due = $dueDate->format('Y-m-d');
        $dueD = date_create($due);

        $currD = date_create($currDate);

        if ($dueD < $currD) {
            //calculate the fine based on the number of days overdue
            $daysOverdue = date_diff($currD, $dueD)->format('%a');
            $fineAmount = $daysOverdue * 0.50;

            //format the fine amount to have 2 decimal places
            $fineAmount = number_format($fineAmount, 2);

            //update the database with the fine amount
            $query2 = "UPDATE [borrowinghistory] SET [late_fees] = ? WHERE [ISBN] = ? AND [due_at] = ? AND [status] = ?";
            $array2 = [$fineAmount, $bisbn, $due, $status];
            $statement2 = sqlsrv_query($conn, $query2, $array2);

            if ($statement2 === false) {
                die(print_r(sqlsrv_errors(), true));
            }
        }
    }
}

if (isset($_POST['signin'])) {
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

                        calLateFees($conn);

                        $pythonScript = '../../python/recommendation.py';
                        shell_exec("python $pythonScript");

                        header('location: ../../admin/index.php');
                        break;

                    case 'SuperAdmin':
                        $_SESSION['userid'] = $row['user_id'];
                        $_SESSION['usertype'] = $usertype;
                        $_SESSION['loggedin'] = true;

                        calLateFees($conn);
                        
                        $pythonScript = '../../python/recommendation.py';
                        shell_exec("python $pythonScript");

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
