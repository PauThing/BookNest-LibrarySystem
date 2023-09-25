<?php
if (isset($_POST["signup"])) {
	session_start();
	include('../connect.php');

	$userid = $_POST['uID'];

	$query = sqlsrv_query($conn, "SELECT * FROM [user] WHERE [user_id] = '$userid'");

	if (sqlsrv_num_rows($query) == 1) {
		$_SESSION['message'] = "<script>alert('The user ID already exists!')</script>";
		header("location: ../signup.php");
	} else {
		$fullname = $_POST['fname'];
		$email = $_POST['uEmail'];

		//Password Encrypt
		$password = $_POST['password'];
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

		//Student card image
		$stuimage = $_FILES['file-upload-field']['tmp_name'];

		// Read the image file as binary data
		$imageBinary = file_get_contents($stuimage);

		if ($imageBinary === false) {
			die("Failed to read the image file.");
		}

		// Encode the binary data as VARBINARY
		$stucimg = '0x' . bin2hex($imageBinary);

		$profileimg	= null;
		$usertype = "Student";
		$status = "Pending";
		
		//Set time zone
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$register = date('Y-m-d H:i:s');
		$update = date('Y-m-d H:i:s');

		// Insert the data into database
		$query = "INSERT INTO [user] (user_id, fullname, email, user_password, stu_img, profile_img, usertype, acc_status, registered_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$statement = sqlsrv_prepare($conn, $query, array(&$userid, &$fullname, &$email, &$hashedPassword, &$stucimg, &$profileimg, &$usertype, &$status, &$register, &$update));

		// Check if the prepared statement executed successfully
		if ($statement) {
			if (sqlsrv_execute($statement)) {
				$_SESSION['message'] = "Successfully registered. Please wait for the approval.";
				header("location: ../signup.php");
			} else {
				$_SESSION['message'] = "<script>alert('Failed to register. Try again.');</script>";
				header("location: ../signup.php?st=failure");
			}
		} else {
			$_SESSION['message'] = "Failed to execute the query.";
			header("location: ../signup.php?st=failure");
		}
	}
} else {
	echo "<script>alert('Failed to register. Try again.')</script>";
	header("location: ../signup.php?st=failure");
}
?>