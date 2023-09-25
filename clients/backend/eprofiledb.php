<?php
session_start();
include('../connect.php');

if (isset($_POST["editprofile"])) {
	$userid = $_SESSION['userid'];

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

		//Student ID Card
		$simgPath = $_FILES['file-upload-field']['tmp_name'];
		$simgFormat = pathinfo($simgPath, PATHINFO_EXTENSION);
		//Read the image file as binary data
		$simgBinary = file_get_contents($simgPath);

		if ($simgBinary === false) {
			die("Failed to read the image file.");
		}

		//Profile Image
		$pimgPath = '../assets/default_profile.jpg';
		$pimgFormat = pathinfo($pimgPath, PATHINFO_EXTENSION);
		//Read the image file as binary data
		$pimgBinary = file_get_contents($pimgPath);

		if ($pimgBinary === false) {
			die("Failed to read the image file.");
		}
		
		$usertype = "Student";
		$status = "Pending";
		
		//Set time zone
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$register = date('Y-m-d H:i:s');
		$update = date('Y-m-d H:i:s');

		//Insert the data into database
		$query = "INSERT INTO [user] ([user_id], [fullname], [user_email], [user_password], [stu_img], [stuimg_format], [profile_img], [profile_format], [usertype], [acc_status], [registered_at], [updated_at]) VALUES (?, ?, ?, ?, CONVERT(varbinary(max), ?), ?, CONVERT(varbinary(max), ?), ?, ?, ?, ?, ?)";
		$array = [$userid, $fullname, $email, $hashedPassword, $simgBinary, $simgFormat, $pimgBinary, $pimgFormat, $usertype, $status, $register, $update];
		$statement = sqlsrv_query($conn, $query, $array);

		//Check if the statement executed successfully
		if ($statement) {
			$_SESSION['message'] = "Successfully registered. Please wait for the approval.";
			header("location: ../signup.php");
		} else {
			$_SESSION['message'] = "Failed to register. Please try again.";
			header("location: ../signup.php");
		}
	}
} else {
	$_SESSION['message'] = "Failed to register. Please make sure every input is correct.";
	header("location: ../signup.php");
}
?>