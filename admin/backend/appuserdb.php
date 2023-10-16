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

    if (isset($_POST["user-approve"])) {
        $userid = $_SESSION['uid'];

        $status = "Approved";

        //set time zone
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $update = date('Y-m-d H:i:s');

        //update the data in database
        $query = "UPDATE [user] SET [acc_status] = ?, [updated_at] = ? WHERE [user_id] = '$userid'";
        $array = [$status, $update];
        $statement = sqlsrv_query($conn, $query, $array);

        //check if the statement executed successfully
        if ($statement) {
            $query2 = "SELECT * FROM [user] WHERE [user_id] = '$userid'";
            $statement2 = sqlsrv_query($conn, $query2);
            $row2 = sqlsrv_fetch_array($statement2);

            $fullname = $row2['fullname'];
            $email = $row2['user_email'];

            //sender and recipient
            $mail->setFrom('booknest.online@gmail.com', 'BookNest Library');
            $mail->addAddress($email);

            //content
            $mail->isHTML(true);
            $mail->Subject = 'Account Registration Approved';
            $mail->Body = "<span>Hello <b>" . $fullname . "</b>, </span><br /><br />
            <span>We're excited to inform you that your user account registration for BookNest Library has been <b>approved</b>! You can now log in and explore our library.</span>
            <br />
            <span>Happy reading!</span>
            <br /><br />
            <span>Regards,</span><br />
            <span><i>BookNest Library</i></span>";

            //send email
            $mail->send();

            $_SESSION['message'] = "Successfully approve the user. An email will be sending out as notification to the user.";
            header("location: ../../admin/userlist.php");
        } else {
            //die(print_r(sqlsrv_errors(), true));
            $_SESSION['message'] = "Failed to approve the user.";
            header("location: ../../admin/userlist.php?st=error");
        }
    } else if (isset($_POST["user-reject"])) {
        $userid = $_SESSION['uid'];

        $status = "Rejected";

        //set time zone
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $update = date('Y-m-d H:i:s');

        //update the data in database
        $query = "UPDATE [user] SET [acc_status] = ?, [updated_at] = ? WHERE [user_id] = '$userid'";
        $array = [$status, $update];
        $statement = sqlsrv_query($conn, $query, $array);

        //check if the statement executed successfully
        if ($statement) {
            $query2 = "SELECT * FROM [user] WHERE [user_id] = '$userid'";
            $statement2 = sqlsrv_query($conn, $query2);
            $row2 = sqlsrv_fetch_array($statement2);

            $fullname = $row2['fullname'];
            $email = $row2['user_email'];

            //sender and recipient
            $mail->setFrom('booknest.online@gmail.com', 'BookNest Library');
            $mail->addReplyTo('booknest.online@gmail.com', 'BookNest Library');
            $mail->addAddress($email);

            //content
            $mail->isHTML(true);
            $mail->Subject = 'Account Registration Rejected';
            $mail->Body = "<span>Hello <b>" . $fullname . "</b>, </span><br /><br />
            <span>We've decided not to proceed with your account registration at this time. If you have any questions or concerns, please feel free to reach out to us.</span>
            <br />
            <span>Happy reading!</span>
            <br /><br />
            <span>Regards,</span><br />
            <span><i>BookNest Library</i></span>";

            //send email
            $mail->send();
            $_SESSION['message'] = "Rejected the user. An email will be sending out as notification to the user.";
            header("location: ../../admin/userlist.php");
        } else {
            //die(print_r(sqlsrv_errors(), true));
            $_SESSION['message'] = "Failed to reject the user.";
            header("location: ../../admin/userlist.php?st=error");
        }
    } else {
        $_SESSION['message'] = "Failed to do any action.";
        header("location: ../../admin/userlist.php?st=error");
    }
?>
