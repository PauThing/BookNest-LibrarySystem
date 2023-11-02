<?php
session_start();
include('../../clients/connect.php');

if (isset($_POST["edit-book"])) {
    $currentURL = $_GET['currentURL'];
    $bisbn = $_GET['bisbn'];

    $bauthor = $_POST['author'];
    $publication = $_POST['publication'];
    $pyear = $_POST['pyear'];
    $bcategory = $_POST['bcat'];
    $bamount = $_POST['bamount'];

    //set time zone
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $update = date('Y-m-d H:i:s');

    if (!preg_match("/^[a-zA-Z\s,.']+$/", $bauthor)) {
        $_SESSION['message'] = "The author can only contain letters, comma, dot and white space.";
        header("location:" . $currentURL . "st=error");
    } else if (!preg_match("/^[a-zA-Z\s\-,.:'&]+$/", $publication)) {
        $_SESSION['message'] = "The author can only contain letters, comma, dot, colon, & and white space.";
        header("location:" . $currentURL . "st=error");
    } else {
        //transaction - a sequence of one or more SQL statements that are executed as a single unit of work
        sqlsrv_begin_transaction($conn);

        //insert the data into database
        $query = "UPDATE [book] SET [author] = ?, [publication] = ?, [publication_year] = ?, [category] = ? WHERE [ISBN] = ?";
        $array = [$bauthor, $publication, $pyear, $bcategory, $bisbn];
        $statement = sqlsrv_query($conn, $query, $array);

        $query2 = "SELECT * FROM [bookcatalog] WHERE [ISBN] = ?";
        $array2 = [$bisbn];
        $statement2 = sqlsrv_query($conn, $query2, $array2);
        $row2 = sqlsrv_fetch_array($statement2);

        if ($row2['available_qty'] == $row2['total_qty'] || $row2['available_qty'] > $row2['total_qty']) {
            $query3 = "UPDATE [bookcatalog] SET [total_qty] = ?, [available_qty] = ?, [updated_at] = ? WHERE [ISBN] = ?";
            $array3 = [$bamount, $bamount, $update, $bisbn];
            $statement3 = sqlsrv_query($conn, $query3, $array3);
        } else {
            $query3 = "UPDATE [bookcatalog] SET [total_qty] = ?, [updated_at] = ? WHERE [ISBN] = ?";
            $array3 = [$bamount, $update, $bisbn];
            $statement3 = sqlsrv_query($conn, $query3, $array3);
        }

        //check if the statement executed successfully
        if ($statement && $statement3) {
            sqlsrv_commit($conn);
            header("location:" . $currentURL . "&st=success");
        } else {
            die(print_r(sqlsrv_errors(), true));
            sqlsrv_rollback($conn);
            $_SESSION['message'] = "Failed to update the details. Please try again.";
            header("location:" . $currentURL . "&st=error");
        }
    }
} else {
    $_SESSION['message'] = "Failed to update the details. Please ensure every input is correct.";
    header("location:" . $currentURL . "&st=error");
}
