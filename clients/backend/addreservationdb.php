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

	if (isset($_POST["reserve"])) {
		$userid = $_SESSION['userid'];
		
		$member = $_POST['member'];
		$droomid = $_POST['droom'];
		$slot = $_POST['slot'];

		//set time zone
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$create = date('Y-m-d');

		$query = "SELECT * FROM [reservation] WHERE [time_slot] = ? AND [created_at] = ?";
		$array = [$slot, $create];
        $statement = sqlsrv_query($conn, $query, $array);

		if (sqlsrv_has_rows($statement)) {
			$_SESSION['message'] = "This slot has been reserved. Please select another slot.";
			header("location: ../../clients/discussionr.php?st=error");
		} else {
			//insert the data into database
			$query2 = "INSERT INTO [reservation] ([user_id], [droom_id], [member], [time_slot], [created_at]) VALUES (?, ?, ?, ?, ?)";
			$array2 = [$userid, $droomid, $member, $slot, $create];
			$statement2 = sqlsrv_query($conn, $query2, $array2);

			//check if the statement executed successfully
			if ($statement2) {
				$query3 = "SELECT * FROM [user] WHERE [user_id] = '$userid'";
				$statement3 = sqlsrv_query($conn, $query3);
				$row3 = sqlsrv_fetch_array($statement3);

				$fullname = $row3['fullname'];
				$email = $row3['user_email'];

				//sender and recipient
				$mail->setFrom('booknest.online@gmail.com', 'BookNest Library');
				$mail->addAddress($email);

				//content
				$mail->isHTML(true);
				$mail->Subject = 'Discussion Room Reserved SUCCESS';
				$mail->Body = "<span>Hello <b>" . $fullname . "</b>, </span><br /><br />
				<span>You have successfully reserved a discussion room (<b>" . $droomid . "</b>). The time is " . $slot . ".</span>
				<br />
				<span>Have a nice day!</span>
				<br /><br />
				<span>Regards,</span><br />
				<span><i>BookNest Library</i></span>";

				//send email
				$mail->send();

				header("location: ../../clients/discussionr.php?st=success");
			} else {
				//die(print_r(sqlsrv_errors(), true));
				$_SESSION['message'] = "Failed to reserve a discussion room.";
				header("location: ../../clients/discussionr.php?st=error");
			}
		}
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Unable to reserve a discussion room.";
		header("location: ../../clients/discussionr.php?st=error");
	}
?>