<?php
	session_start();
	include('../../clients/connect.php');

	if (isset($_POST["new-db"])) {
		$title = $_POST['dbtitle'];
		$category = $_POST['dbcat'];

		$query = "SELECT * FROM [onlinedb] WHERE [title] = ? AND [category] = ?";
		$array = [$title, $category];
		$statement = sqlsrv_query($conn, $query, $array);

		if (sqlsrv_has_rows($statement)) {
			$_SESSION['message'] = "The title already exists!";
			header("location: ../../admin/eonlinedblist.php");
		} else {
			$url = $_POST['dburl'];

			//set time zone
			date_default_timezone_set('Asia/Kuala_Lumpur');
			$create = date('Y-m-d H:i:s');

			if (!preg_match("/^[a-zA-Z-' ]*$/", $title)) {
				$_SESSION['message'] = "The title can only contain letters and white space.";
				header("location: ../../admin/eonlinedblist.php?st=error");
			} else {
				//insert the data into database
				$query2 = "INSERT INTO [onlinedb] ([title], [db_url], [category], [created_at]) VALUES (?, ?, ?, ?)";
				$array2 = [$title, $url, $category, $create];
				$statement2 = sqlsrv_query($conn, $query2, $array2);

				//check if the statement executed successfully
				if ($statement2) {
					header("location: ../../admin/eonlinedblist.php?st=success");
				} else {
					//die(print_r(sqlsrv_errors(), true));
					$_SESSION['message'] = "Failed to add new online database. Please try again.";
					header("location: ../../admin/eonlinedblist.php?st=error");
				}
			}
		}
	} else if (isset($_POST["new-programme"])) {
		$programme = $_POST['programme'];

		$query = "SELECT * FROM [programme] WHERE [programme] = ?";
		$array = [$programme];
		$statement = sqlsrv_query($conn, $query, $array);

		if (sqlsrv_has_rows($statement)) {
			$_SESSION['message'] = "The programme already exists!";
			header("location: ../../admin/eprogrammelist.php");
		} else {
			$programme = $_POST['programme'];
			$department = $_POST['school'];

			if (!preg_match("/^[a-zA-Z-' ]*$/", $programme)) {
				$_SESSION['message'] = "The programme can only contain letters.";
				header("location: ../../admin/eprogrammelist.php?st=error");
			} else if ($department == "") {
				$_SESSION['message'] = "Please select a suitable department for the programme.";
				header("location: ../../admin/eprogrammelist.php?st=error");
			} else {
				//insert the data into database
				$query2 = "INSERT INTO [programme] ([programme], [department]) VALUES (?, ?)";
				$array2 = [$programme, $department];
				$statement2 = sqlsrv_query($conn, $query2, $array2);

				//check if the statement executed successfully
				if ($statement2) {
					header("location: ../../admin/eprogrammelist.php?st=success");
				} else {
					//die(print_r(sqlsrv_errors(), true));
					$_SESSION['message'] = "Failed to add new programme. Please try again.";
					header("location: ../../admin/eprogrammelist.php?st=error");
				}
			}
		}
	} else if (isset($_POST["new-stuproject"])) {
		$modulecode = $_POST['modulecode'];
		$project = $_POST['project'];
		$programme = $_POST['programme'];
		$semester = $_POST['date'];
		$formattedSemester = date('m-Y', strtotime($semester));

		$query = "SELECT * FROM [studentproject] WHERE [title] = ? AND [programme] = ? AND [created_at] = ?";
		$array = [$project, $programme, $formattedSemester];
		$statement = sqlsrv_query($conn, $query, $array);

		if (sqlsrv_has_rows($statement)) {
			$_SESSION['message'] = "The project already exists!";
			header("location: ../../admin/addstuproject.php");
		} else {
			$docName = $_FILES['file-upload-field']['name'];
			$docType = $_FILES['file-upload-field']['type'];
			$docData = file_get_contents($_FILES['file-upload-field']['tmp_name']);

			if (!preg_match("/^[a-zA-Z-_ ]+$/", $project)) {
				$_SESSION['message'] = "The project name can only contain letters, dash and underscore.";
				header("location: ../../admin/addstuproject.php?st=error");
			} else if (!preg_match("/^[A-Za-z0-9]+$/", $modulecode)) {
				$_SESSION['message'] = "The module code can only contain letters and numbers.";
				header("location: ../../admin/addstuproject.php?st=error");
			} else if (!preg_match("/^[a-zA-Z-' ]*$/", $programme)) {
				$_SESSION['message'] = "The programme can only contain letters.";
				header("location: ../../admin/addstuproject.php?st=error");
			} else {
				//insert the data into database
				$query2 = "INSERT INTO [studentproject] ([sp_id], [title], [filename], [filetype], [filedata], [programme], [created_at]) VALUES (?, ?, ?, ?, CONVERT(varbinary(max), ?), ?, ?)";
				$array2 = [$modulecode, $project, $docName, $docType, $docData, $programme, $formattedSemester];
				$statement2 = sqlsrv_query($conn, $query2, $array2);

				//check if the statement executed successfully
				if ($statement2) {
					header("location: ../../admin/addstuproject.php?st=success");
				} else {
					//die(print_r(sqlsrv_errors(), true));
					$_SESSION['message'] = "Failed to add new project. Please try again.";
					header("location: ../../admin/addstuproject.php?st=error");
				}
			}
		}
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Failed to do any action.";
		header("location: ../../admin/index.php?st=error");
	}
?>