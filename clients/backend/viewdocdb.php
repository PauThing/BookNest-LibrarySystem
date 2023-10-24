<?php
	session_start();
	include('../../clients/connect.php');

    if (isset($_GET['eptitle'])) {
        $programme = $_GET['epprogramme'];
        $eptitle = $_GET['eptitle'];

        //retrieve the PDF data from the database based on the title
        $query = "SELECT * FROM [exampaper] WHERE [filename] = ?";
        $array = [$eptitle];
        $statement = sqlsrv_query($conn, $query, $array);

        if ($statement === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($statement);

        if ($row) {
            $docData = $row['filedata'];
            $docType = $row['filetype'];

            //set appropriate headers for serving a PDF
            header("Content-Type: " . $docType);
            header("Content-Disposition: inline; filename=" . $eptitle . ".pdf");
            echo $docData; //output the PDF data

            sqlsrv_free_stmt($statement);
        } else {
            $_SESSION['message'] = "No PDF found.";
			header("location: ../../clients/pastyearlist.php?programme=" . $programme . "&st=error");
        }
    } else if (isset($_GET['sptitle'])) {
        $sptitle = $_GET['sptitle'];

        //retrieve the PDF data from the database based on the title
        $query = "SELECT * FROM [studentproject] WHERE [title] = ?";
        $array = [$sptitle];
        $statement = sqlsrv_query($conn, $query, $array);

        if ($statement === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($statement);

        if ($row) {
            $docData = $row['filedata'];
            $docType = $row['filetype'];

            //set appropriate headers for serving a PDF
            header("Content-Type: " . $docType);
            header("Content-Disposition: inline; filename=" . $sptitle . ".pdf");
            echo $docData; //output the PDF data

            sqlsrv_free_stmt($statement);
        } else {
            $_SESSION['message'] = "No PDF found.";
			header("location: ../../clients/stuprojectlist.php?st=error");
        }
    }
?>