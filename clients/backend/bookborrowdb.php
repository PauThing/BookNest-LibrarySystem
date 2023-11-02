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

	if (isset($_POST["brwconfirm"])) {
		$url = $_GET['cURL'];
		$userid = $_SESSION['userid'];
		
		$bisbn = $_POST['bisbn'];
		$brwDate = $_POST['brwdate'];
		$dueDate = $_POST['duedate'];
		$fullname = $_POST['uname'];

		$status = "On Loan";
		$latefee = 0.00;

		//set time zone
        date_default_timezone_set('Asia/Kuala_Lumpur');
        $update = date('Y-m-d H:i:s');

		//check if the current URL already contains query parameters
		$containsQuestionMark = (strpos($url, '?') !== false);
		$containsAmpersand = (strrpos($url, '&') !== false);

		if ($containsQuestionMark && $containsAmpersand) {
			$separator = '&'; // Both ? and & are present
		} else {
			$separator = ($containsQuestionMark) ? '&' : '?';
		}

		$query = "SELECT * FROM [borrowinghistory] WHERE [ISBN] = ? AND [borrow_at] = ? AND [user_id] = ? AND [status] = 'On Loan'";
		$array = [$bisbn, $brwDate, $userid];
        $statement = sqlsrv_query($conn, $query, $array);

		if ($statement === false) {
			die(print_r(sqlsrv_errors(), true));
		}

		if (sqlsrv_has_rows($statement)) {
			$_SESSION['message'] = "You have already borrowed this book today.";
			header("location: ". $url . $separator . "st=error");
		} else {
			$query2 = "SELECT * FROM [bookcatalog] WHERE [ISBN] = ?";
			$array2 = [$bisbn];
			$statement2 = sqlsrv_query($conn, $query2, $array2);
			$row2 = sqlsrv_fetch_array($statement2);
			$catalogid = $row2['catalog_id'];
			$qty = $row2['available_qty'];

			if ($qty > 0) {
				//calculate the new quantity
				$newQty = $qty - 1;
			}

			//insert the data into database
			$query2 = "INSERT INTO [borrowinghistory] ([user_id], [catalog_id], [ISBN], [borrow_at], [due_at], [status], [late_fees]) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$array2 = [$userid, $catalogid, $bisbn, $brwDate, $dueDate, $status, $latefee];
			$statement2 = sqlsrv_query($conn, $query2, $array2);

			//update the data in database
			$query3 = "UPDATE [bookcatalog] SET [available_qty] = ?, [updated_at] = ? WHERE [catalog_id] = ? AND [ISBN] = ?";
			$array3 = [$newQty, $update, $catalogid, $bisbn];
			$statement3 = sqlsrv_query($conn, $query3, $array3);

			//check if the statement executed successfully
			if ($statement2 && $statement3) {
				$query4 = "SELECT
							bh.*,
							b.*,
							u.*
						FROM [borrowinghistory] bh
						LEFT JOIN [book] b ON bh.[ISBN] = b.[ISBN]
						LEFT JOIN [user] u ON bh.[user_id] = u.[user_id]
						WHERE bh.[ISBN] = '$bisbn' AND bh.[user_id] = '$userid'";
				$statement4 = sqlsrv_query($conn, $query4);
				$row4 = sqlsrv_fetch_array($statement4);

				$email = $row4['user_email'];

				$borrow = $row4['borrow_at'];
				$brwDate = $borrow->format('Y-m-d');

				$due = $row4['due_at'];
				$dueDate = $due->format('Y-m-d');

				$booktitle = $row4['book_title'];

				//sender and recipient
				$mail->setFrom('booknest.online@gmail.com', 'BookNest Library');
				$mail->addAddress($email);

				//content
				$mail->isHTML(true);
				$mail->Subject = 'Book Borrowed SUCCESS';
				$mail->Body = "<span>Hello <b>" . $fullname . "</b>, </span>
				<br /><br />
				<span>You have successfully borrowed a book from our library.</span><br />
				<label>ISBN: <b>" . $bisbn . "</b></label><br />
				<label>Book: <b>" . $booktitle . "</b></label><br />
				<label>Borrow Date: <b>" . $brwDate . "</b></label><br />
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
				die(print_r(sqlsrv_errors(), true));
				$_SESSION['message'] = "Failed to borrow this book.";
				header("location: ". $url . $separator . "st=error");
			}
		}
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Unable to borrow any book.";
		header("location: ". $url . $separator . "st=error");
	}
?>