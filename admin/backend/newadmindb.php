<?php
session_start();
include('../../clients/connect.php');

if (isset($_POST["new-admin"])) {
	$adminid = $_POST['uID'];

	$query = "SELECT * FROM [user] WHERE [user_id] = ? AND [usertype] = 'Admin'";
	$array = [$adminid];
	$statement = sqlsrv_query($conn, $query, $array);

	if (sqlsrv_has_rows($statement)) {
		$_SESSION['message'] = "The admin ID already exists!";
		header("location: ../../admin/adminlist.php");
	} else {
		$fullname = $_POST['fname'];
		$email = $_POST['uEmail'];

		//password encrypt - hashing
		$password = $_POST['password'];
		$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

		//default profile image
		$pimgPath = '../../clients/assets/default_profile.jpg';
		//read the image file as binary data
		$pimgBinary = file_get_contents($pimgPath);

		if ($pimgBinary === false) {
			die("Failed to read the image file.");
		}
		
		$usertype = "Admin";
		$status = "Approved";
		
		//set time zone
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$register = date('Y-m-d H:i:s');
		$update = date('Y-m-d H:i:s');

		if (!preg_match("/^[a-zA-Z-' ]*$/", $fullname)) {
			$_SESSION['message'] = "The name can only contain letters and white space.";
			header("location: ../../admin/adminlist.php?st=error");
		} else if (!preg_match("/^[a-zA-Z0-9]*$/", $adminid)) {
			$_SESSION['message'] = "The admin ID can only contain letters and numbers.";
			header("location: ../../admin/adminlist.php?st=error");
		} else {
			//insert the data into database
			$query = "INSERT INTO [user] ([user_id], [fullname], [user_email], [user_password], [profile_img], [usertype], [acc_status], [registered_at], [updated_at]) VALUES (?, ?, ?, ?, CONVERT(varbinary(max), ?), ?, ?, ?, ?)";
			$array = [$adminid, $fullname, $email, $hashedPassword, $pimgBinary, $usertype, $status, $register, $update];
			$statement = sqlsrv_query($conn, $query, $array);

			//check if the statement executed successfully
			if ($statement) {
				header("location: ../../admin/adminlist.php?st=success");
			} else {
				//die(print_r(sqlsrv_errors(), true));
				$_SESSION['message'] = "Failed to register new admin. Please try again.";
				header("location: ../../admin/adminlist.php?st=error");
			}
		}
	}
} else {
	//die(print_r(sqlsrv_errors(), true));
	$_SESSION['message'] = "Failed to register new admin. Please ensure every input is correct.";
	header("location: ../../admin/adminlist.php?st=error");
}
?>