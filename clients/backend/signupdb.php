<?php
	session_start();
	include('../../clients/connect.php');

	if (isset($_POST["signup"])) {
		$userid = $_POST['uID'];

		$query = sqlsrv_query($conn, "SELECT * FROM [user] WHERE [user_id] = ?");
		$array = [$userid];
		$statement = sqlsrv_query($conn, $query, $array);

		if (sqlsrv_num_rows($statement) == 1) {
			$_SESSION['message'] = "The user ID already exists!";
			header("location: ../../clients/signup.php");
		} else {
			$fullname = $_POST['fname'];
			$email = $_POST['uEmail'];

			//password encrypt - hashing
			$password = $_POST['password'];
			$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

			//student ID card
			$simgPath = $_FILES['file-upload-field']['tmp_name'];
			//read the image file as binary data
			$simgBinary = file_get_contents($simgPath);

			if ($simgBinary === false) {
				die("Failed to read the image file.");
			}

			//default profile image
			$pimgPath = '../assets/default_profile.jpg';
			//read the image file as binary data
			$pimgBinary = file_get_contents($pimgPath);

			if ($pimgBinary === false) {
				die("Failed to read the image file.");
			}

			$usertype = "Student";
			$status = "Pending";

			//set time zone
			date_default_timezone_set('Asia/Kuala_Lumpur');
			$register = date('Y-m-d H:i:s');
			$update = date('Y-m-d H:i:s');

			if (!preg_match("/^[a-zA-Z-' ]*$/", $fullname)) {
				$_SESSION['message'] = "Your name can only contain letters and white space.";
				header("location: ../../clients/signup.php?st=error");
			} else {
				//insert the data into database
				$query = "INSERT INTO [user] ([user_id], [fullname], [user_email], [user_password], [stu_img], [profile_img], [usertype], [acc_status], [registered_at], [updated_at]) VALUES (?, ?, ?, ?, CONVERT(varbinary(max), ?), CONVERT(varbinary(max), ?), ?, ?, ?, ?)";
				$array = [$userid, $fullname, $email, $hashedPassword, $simgBinary, $pimgBinary, $usertype, $status, $register, $update];
				$statement = sqlsrv_query($conn, $query, $array);

				//check if the statement executed successfully
				if ($statement) {
					$_SESSION['message'] = "Successfully registered. Please wait for the approval.";
					header("location: ../../clients/signup.php?st=success");
				} else {
					//die(print_r(sqlsrv_errors(), true));
					$_SESSION['message'] = "Failed to register. Please try again.";
					header("location: ../../clients/signup.php?st=error");
				}
			}
		}
	} else {
		$_SESSION['message'] = "Failed to register. Please ensure every input is correct.";
		header("location: ../../clients/signup.php?st=error");
	}
?>