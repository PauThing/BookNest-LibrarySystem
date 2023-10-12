<?php
	session_start();
	include('../../clients/connect.php');

	$userid = $_SESSION['userid'];
	$inputpass = $_GET['password'];

	$query = "SELECT * FROM [user] WHERE [user_id] = ?";
	$array = [$userid];
	$statement = sqlsrv_query($conn, $query, $array);
	$row = sqlsrv_fetch_array($statement);

	$hashedPassword = $row['user_password'];

	if (password_verify($inputpass, $hashedPassword)) 
	{
		$uid = $_GET['userid'];
		$query2 = "SELECT * FROM [user] WHERE [user_id] = ?";
		$array2 = [$uid];
		$statement2 = sqlsrv_query($conn, $query2, $array2);
		$row2 = sqlsrv_fetch_array($statement2);

		$usertype = $row2['usertype'];

		if ($usertype == "Admin") {
			$query3 = "DELETE FROM [user] where [user_id] = '$uid'";	
							
			if(sqlsrv_query($conn, $query3))
			{
				$_SESSION['message'] = "This admin has been deleted.";

				header("location: ../../admin/adminlist.php?st=success");
			} else
			{
				$_SESSION['message'] = "Failed to delete this admin.";
					
				header("location: ../../admin/adminlist.php?st=error");
			}
		} else if ($usertype == "Student") {
			$query4 = "DELETE FROM [user] where [user_id] = '$uid'";	
							
			if(sqlsrv_query($conn, $query4))
			{
				$_SESSION['message'] = "This user has been deleted.";

				header("location: ../../admin/userlist.php?st=success");
			} else
			{
				$_SESSION['message'] = "Failed to delete this user.";
					
				header("location: ../../admin/userlist.php?st=error");
			}
		}
	} else
	{
		$_SESSION['message'] = "Unauthorized Access: Password does not match.";
    	header("location: ../../admin/adminlist.php?st=error");
	}
?>
