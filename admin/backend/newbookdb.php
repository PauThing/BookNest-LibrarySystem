<?php
session_start();
include('../../clients/connect.php');

if (isset($_POST["new-book"])) {
	$bisbn = $_POST['bisbn'];
	$btitle = $_POST['btitle'];
	$bauthor = $_POST['author'];
	$publication = $_POST['publication'];
	$pyear = $_POST['pyear'];
	$bcategory = $_POST['bcat'];
	$blocation = "BookNest Library, Rak " . $_POST['blocation'];
	$bamount = $_POST['bamount'];

	if (!empty($_FILES['file-upload-field']['tmp_name']) && $_FILES['file-upload-field']['error'] === UPLOAD_ERR_OK) {
		//book cover image
		$cimgPath = $_FILES['file-upload-field']['tmp_name'];
		//read the image file as binary data
		$cimgBinary = file_get_contents($cimgPath);

		if ($cimgBinary === false) {
			die("Failed to read the image file.");
		}
	} else {
		//default book cover image
		$cimgPath = '../../clients/assets/cover_unavailable.png';
		//read the image file as binary data
		$cimgBinary = file_get_contents($cimgPath);

		if ($cimgBinary === false) {
			die("Failed to read the image file.");
		}
	}

	//set time zone
	date_default_timezone_set('Asia/Kuala_Lumpur');
	$create = date('Y-m-d H:i:s');

	$query = "SELECT * FROM [book] WHERE [ISBN] = ?";
	$array = [$bisbn];
	$statement = sqlsrv_query($conn, $query, $array);

	if (sqlsrv_has_rows($statement)) {
		$_SESSION['message'] = "The book already exists!";
		header("location: ../../admin/addbook.php");
	} else {
		if (!preg_match("/^[a-zA-Z0-9\s\-,.:'+#&]+$/", $btitle)) {
			$_SESSION['message'] = "The book title can only contain letters, numbers, comma, dash, & symbol and white space.";
			header("location: ../../admin/addbook.php?st=error");
		} else if (!preg_match("/^[a-zA-Z\s,.']+$/", trim($bauthor))) {
			$_SESSION['message'] = "The author can only contain letters, comma, dot and white space.";
			header("location: ../../admin/addbook.php?st=error");
		} else if (!preg_match("/^[a-zA-Z\s\-,.:'&]+$/", $publication)) {
			$_SESSION['message'] = "The publication can only contain letters, comma, dot and white space.";
			header("location: ../../admin/addbook.php?st=error");
		} else {
			//transaction - a sequence of one or more SQL statements that are executed as a single unit of work
			sqlsrv_begin_transaction($conn);

			//insert the data into database
			$query = "INSERT INTO [book] ([ISBN], [book_title], [author], [publication], [publication_year], [cover_img], [category], [created_at]) VALUES (?, ?, ?, ?, ?, CONVERT(varbinary(max), ?), ?, ?)";
			$array = [$bisbn, $btitle, $bauthor, $publication, $pyear, $cimgBinary, $bcategory, $create];
			$statement = sqlsrv_query($conn, $query, $array);

			$query2 = "INSERT INTO [bookcatalog] ([ISBN], [book_location], [total_qty], [available_qty], [updated_at]) VALUES (?, ?, ?, ?, ?)";
			$array2 = [$bisbn, $blocation, $bamount, $bamount, $create];
			$statement2 = sqlsrv_query($conn, $query2, $array2);
			
			//check if the statement executed successfully
			if ($statement && $statement2) {
				sqlsrv_commit($conn);
				header("location: ../../admin/addbook.php?st=success");
			} else {
				//die(print_r(sqlsrv_errors(), true));
				sqlsrv_rollback($conn);
				$_SESSION['message'] = "Failed to add new book. Please try again.";
				header("location: ../../admin/addbook.php?st=error");
			}
		}
	}
} else {
	//die(print_r(sqlsrv_errors(), true));
	$_SESSION['message'] = "Failed to add new book. Please ensure every input is correct.";
	header("location: ../../admin/addbook.php?st=error");
}
