<?php
	session_start();
	include('../../clients/connect.php');

	$userid = $_SESSION['userid'];
	$inputpass = $_GET['password'];

	$query = "SELECT [user_password] FROM [user] WHERE [user_id] = ?";
	$array = [$userid];
	$statement = sqlsrv_query($conn, $query, $array);
	$row = sqlsrv_fetch_array($statement);

	$hashedPassword = $row['user_password'];

	if (password_verify($inputpass, $hashedPassword)) 
	{
		$sql2 = "DELETE FROM [user] where [user_id] = '".$_GET["userid"]."'";	
						
		if(sqlsrv_query($conn, $sql2))
		{
			$_SESSION['message'] = "This admin has been deleted.";

			header("location: ../../admin/adminlist.php?st=success");
		} else
		{
			$_SESSION['message'] = "Failed to delete this admin";
				
			header("location: ../../admin/adminlist.php?st=error");
		}
	} else
	{
		$_SESSION['message'] = "Unauthorized Access: Password does not match.";
    	header("location: ../../admin/adminlist.php?st=error");
	}
?>
