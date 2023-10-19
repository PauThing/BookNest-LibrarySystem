<?php
	session_start();
	include('../../clients/connect.php');

	if (isset($_POST["new-exampaper"])) {
		$programme = $_GET['programme'];

        $semester = $_POST['date'];
		$formattedSemester = date('m-Y', strtotime($semester));

		if (isset($_FILES['file-upload-field']) && is_array($_FILES['file-upload-field']['name'])) {
			for ($i = 0; $i < count($_FILES['file-upload-field']['name']); $i++) {
				$docName = $_FILES['file-upload-field']['name'][$i];
				$docType = $_FILES['file-upload-field']['type'][$i];
				$docData = file_get_contents($_FILES['file-upload-field']['tmp_name'][$i]);

				$query = "SELECT * FROM [exampaper] WHERE [filename] = ? AND [programme] = ? AND [created_at] = ?";
				$array = [$docName, $programme, $formattedSemester];
				$statement = sqlsrv_query($conn, $query, $array);

				if (sqlsrv_has_rows($statement)) {
					$_SESSION['message'] = "The exam paper already exists!";
					header("location: ../../admin/addpastyear.php?programme=" . $programme);
				} else {
					//insert the data into database
					$query2 = "INSERT INTO [exampaper] ([filename], [filetype], [filedata], [programme], [created_at]) VALUES (?, ?, CONVERT(varbinary(max), ?), ?, ?)";
					$array2 = [$docName, $docType, $docData, $programme, $formattedSemester];
					$statement2 = sqlsrv_query($conn, $query2, $array2);

					//check if the statement executed successfully
					if ($statement2) {
						header("location: ../../admin/addpastyear.php?programme=" . $programme . "&st=success");
					} else {
						//die(print_r(sqlsrv_errors(), true));
						$_SESSION['message'] = "Failed to add new exam papers. Please try again.";
						header("location: ../../admin/addpastyear.php?programme=" . $programme . "&st=error");
					}
				}
			}
		} else {
            $_SESSION['message'] = "No PDF files selected.";
            header("location: ../../admin/addpastyear.php?programme=" . $programme . "&st=error");
        }
	} else {
		//die(print_r(sqlsrv_errors(), true));
		$_SESSION['message'] = "Failed to do any action.";
		header("location: ../../admin/index.php?st=error");
	}
?>