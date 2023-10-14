<?php
session_start();
include('../../clients/connect.php');

if (isset($_POST["new-db"])) {
	$title = $_POST['dbtitle'];

	$query = sqlsrv_query($conn, "SELECT * FROM [onlinedb] WHERE [title] = ?");
	$array = [$title];
	$statement = sqlsrv_query($conn, $query, $array);

	if (sqlsrv_num_rows($statement) == 1) {
		$_SESSION['message'] = "The title already exists!";
		header("location: ../../admin/eonlinedblist.php");
	} else {
		$title = $_POST['dbtitle'];
		$url = $_POST['dburl'];
		$category = $_POST['dbcat'];

		//set time zone
		date_default_timezone_set('Asia/Kuala_Lumpur');
		$create = date('Y-m-d H:i:s');

		if (!preg_match("/^[a-zA-Z-' ]*$/", $title)) {
			$_SESSION['message'] = "The title can only contain letters and white space.";
			header("location: ../../admin/eonlinedblist.php?st=error");
		} else {
			//insert the data into database
			$query = "INSERT INTO [onlinedb] ([title], [db_url], [category], [created_at]) VALUES (?, ?, ?, ?)";
			$array = [$title, $url, $category, $create];
			$statement = sqlsrv_query($conn, $query, $array);

			//check if the statement executed successfully
			if ($statement) {
				$_SESSION['message'] = "Successfully added a new online database.";
				header("location: ../../admin/eonlinedblist.php?st=success");
			} else {
				$_SESSION['message'] = "Failed to add new online database. Please try again.";
				header("location: ../../admin/eonlinedblist.php?st=error");
			}
		}
	}
} else {
	$_SESSION['message'] = "Failed to add new online database. Please make sure every input is correct.";
	header("location: ../../admin/eonlinedblist.php?st=error");
}
?>