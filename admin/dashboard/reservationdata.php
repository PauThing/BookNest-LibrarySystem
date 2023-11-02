<?php
    session_start();
    include('../../clients/connect.php');
    
    //set time zone
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $selectedYear = $_GET['year'];

    $query = "SELECT
            MONTH(r.[created_at]) AS month,
            COUNT(r.[reservation_id]) as reserverecords
        FROM [reservation] r
        WHERE YEAR(r.[created_at]) = ?
        GROUP BY MONTH(r.[created_at])";
    $array = [$selectedYear];
    $statement = sqlsrv_query($conn, $query, $array);

    $statements = array();

    if ($statement === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    while ($row = sqlsrv_fetch_array($statement, SQLSRV_FETCH_ASSOC)) {
        $statements[] = $row;
    }

    sqlsrv_close($conn);

    //send the data to the client-side JavaScript
    header('Content-Type: application/json');
    echo json_encode($statements);
?>