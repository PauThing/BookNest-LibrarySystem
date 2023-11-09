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

	if (isset($_POST["renew"])) {
		$url = $_GET['cURL'];

		$bisbn = $_POST['bisbn'];
		$userid = $_POST['userid'];
		$brwDate = $_POST['brwdate'];
		$newDate = $_POST['newdate'];
		$dueDate = $_POST['renewdate'];
		$fullname = $_POST['uname'];

		//set time zone
        date_default_timezone_set('Asia/Kuala_Lumpur');
		$currDate = date('Y-m-d');

		//check if the current URL already contains query parameters
		$containsQuestionMark = (strpos($url, '?') !== false);
		$containsAmpersand = (strrpos($url, '&') !== false);

		if ($containsQuestionMark && $containsAmpersand) {
			$separator = '&'; // Both ? and & are present
		} else {
			$separator = ($containsQuestionMark) ? '&' : '?';
		}

		$query = "SELECT * FROM [borrowinghistory] WHERE [ISBN] = ? AND [borrow_at] = ? AND [user_id] = ?";
		$array = [$bisbn, $brwDate, $userid];
        $statement = sqlsrv_query($conn, $query, $array);
		$row = sqlsrv_fetch_array($statement);

		$due = $row['due_at'];
		$dueD = $due->format('Y-m-d');

		if (strtotime($dueD) < strtotime($currDate)) {
			$_SESSION['message'] = "You already late to return the book. Please return it asap.";
			header("location: ". $url . $separator . "st=error");
		} else {
			//update the data in database
			$query2 = "UPDATE [borrowinghistory] SET [due_at] = ? WHERE [ISBN] = ? AND [user_id] = ? AND [status] = 'On Loan'";
			$array2 = [$dueDate, $bisbn, $userid];
			$statement2 = sqlsrv_query($conn, $query2, $array2);

			//check if the statement executed successfully
			if ($statement2) {
				$query3 = "SELECT
							bh.*,
							b.*,
							u.*
						FROM [borrowinghistory] bh
						LEFT JOIN [book] b ON bh.[ISBN] = b.[ISBN]
						LEFT JOIN [user] u ON bh.[user_id] = u.[user_id]
						WHERE bh.[ISBN] = '$bisbn' AND bh.[user_id] = '$userid'";
				$statement3 = sqlsrv_query($conn, $query3);
				$row3 = sqlsrv_fetch_array($statement3);

				$email = $row3['user_email'];

				$borrow = $row3['borrow_at'];
				$brwDate = $borrow->format('Y-m-d');

				$dueD = $row3['due_at'];
				$dueDate = $dueD->format('Y-m-d');

				$booktitle = $row3['book_title'];

				//sender and recipient
				$mail->setFrom('booknest.online@gmail.com', 'BookNest Library');
				$mail->addAddress($email);

				//content
				$mail->isHTML(true);
				$mail->Subject = 'Book Renewed SUCCESS';
				$mail->Body = "<span>Hello <b>" . $fullname . "</b>, </span>
				<br /><br />
				<span>You have successfully renewed the borrowing.</span><br />
				<label>ISBN: <b>" . $bisbn . "</b></label><br />
				<label>Book: <b>" . $booktitle . "</b></label><br />
				<label>Renew Date: <b>" . $newDate . "</b></label><br />
				<label>Due Date: <b>" . $dueDate . "</b></label>
				<br /><br />
				<span>Good luck on you reading! Have a nice day!</span>
				<br /><br />
				<span>Regards,</span><br />
				<span><i>BookNest Library</i></span>";

				//send email
				$mail->send();

				header("location: ". $url . $separator . "st=success");
			} else {
				//die(print_r(sqlsrv_errors(), true));
				$_SESSION['message'] = "Failed to renew this book.";
				header("location: ". $url . $separator . "st=error");
			}
		}
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Unable to renew any book.";
		header("location: ". $url . $separator . "st=error");
	}
?>