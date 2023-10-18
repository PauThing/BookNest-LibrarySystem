<?php
	session_start();
	include('../../clients/connect.php');

    if (isset($_GET['eptitle'])) {
        $title = $_GET['eptitle'];

        //retrieve the PDF data from the database based on the title
        $query = "SELECT [filetype], [filedata] FROM [exampaper] WHERE [title] = '$title'";
        $statement = sqlsrv_query($conn, $query);

        if ($statement === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($statement);

        if ($row) {
            $docData = $row['filedata'];
            $docType = $row['filetype'];

            //set appropriate headers for serving a PDF
            header("Content-Type: " . $docType);
            header("Content-Disposition: inline; filename=" . $title . ".pdf");
            echo $docData; //output the PDF data

            sqlsrv_free_stmt($statement);
        } else {
            $_SESSION['message'] = "No pdf found.";
			header("location: ../../admin/epastyearlist.php?programme=<?php echo $pg; ?>&st=error");
        }
    } else if (isset($_GET['sptitle'])) {
        $title = $_GET['sptitle'];

        //retrieve the PDF data from the database based on the title
        $query = "SELECT [filetype], [filedata] FROM [exampaper] WHERE [title] = '$title'";
        $statement = sqlsrv_query($conn, $query);

        if ($statement === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($statement);

        if ($row) {
            $docData = $row['filedata'];
            $docType = $row['filetype'];

            //set appropriate headers for serving a PDF
            header("Content-Type: " . $docType);
            header("Content-Disposition: inline; filename=" . $title . ".pdf");
            echo $docData; //output the PDF data

            sqlsrv_free_stmt($statement);
        } else {
            $_SESSION['message'] = "No pdf found.";
			header("location: ../../admin/epastyearlist.php?programme=<?php echo $pg; ?>&st=error");
        }
    }
?>