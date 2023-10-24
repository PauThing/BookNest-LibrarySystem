<?php
    session_start();
    include('../../clients/connect.php');

    if (isset($_POST["setting"])) {
        $roomnum = $_POST['droom'];
        $status = $_POST['status'];

        $query = "SELECT [status] FROM [discussionroom] WHERE [droom_num] = ?";
        $array = [$roomnum];
        $statement = sqlsrv_query($conn, $query, $array);
        $row = sqlsrv_fetch_array($statement);

        if ($row['status'] == $status) {
            $_SESSION['message'] = "The status is already set as " . $status;
            header("location: ../../admin/ediscussionr.php");
        } else {
            //update the data in database
            $query2 = "UPDATE [discussionroom] SET [status] = ? WHERE [droom_num] = ?";
            $array2 = [$status, $roomnum];
            $statement2 = sqlsrv_query($conn, $query2, $array2);

            if ($statement) {
                header("location: ../../admin/ediscussionr.php?st=success");
            } else {
                //die(print_r(sqlsrv_errors(), true));
                $_SESSION['message'] = "Failed to update the status of the discussion room.";
                header("location: ../../admin/ediscussionr.php?st=error");
            }
        }
    } else {
        $_SESSION['message'] = "Unable to update.";
        header("location: ../../admin/ediscussionr.php?st=error");
    }
?>
