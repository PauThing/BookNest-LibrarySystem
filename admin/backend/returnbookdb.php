<?php
	session_start();
	include('../../clients/connect.php');

	if (isset($_POST["returnb"])) {
		$bisbn = $_POST['bisbn'];
		$userid = $_POST['uid'];
		$dueDate = $_POST['duedate'];
		$returnDate = $_POST['returndate'];

		$status = "Returned";
		$latefee = 0.00;

		//set time zone
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$currDate = date('Y-m-d');
		$update = date('Y-m-d H:i:s');

		$query = "SELECT * FROM [bookcatalog] WHERE [ISBN] = ?";
		$array = [$bisbn];
		$statement = sqlsrv_query($conn, $query, $array);
		$row = sqlsrv_fetch_array($statement);
		$catalogid = $row['catalog_id'];
		$qty = $row['available_qty'];

		if ($qty >= 0) {
			//calculate the new quantity
			$newQty = $qty + 1;
		}

		//update the data in database
		$query2 = "UPDATE [borrowinghistory] SET [return_at] = ?, [status] = ?, [late_fees] = ? WHERE [ISBN] = ? AND [user_id] = ? AND [due_at] = ?";
		$array2 = [$currDate, $status, $latefee, $bisbn, $userid, $dueDate];
		$statement2 = sqlsrv_query($conn, $query2, $array2);

		//update the data in database
		$query3 = "UPDATE [bookcatalog] SET [available_qty] = ?, [updated_at] = ? WHERE [catalog_id] = ? AND [ISBN] = ?";
		$array3 = [$newQty, $update, $catalogid, $bisbn];
		$statement3 = sqlsrv_query($conn, $query3, $array3);

		//check if the statement executed successfully
		if ($statement2 && $statement3) {
			header("location: ../../admin/eonloanbook.php?st=success");
		} else {
			die(print_r(sqlsrv_errors(), true));
			$_SESSION['message'] = "Failed to return this book.";
			header("location: ../../admin/eonloanbook.php?st=error");
		}
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Unable to return any book.";
		header("location: ../../admin/eonloanbook.php?st=error");
	}
?>