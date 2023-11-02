<?php
    session_start();
    include('../../clients/connect.php');

    //set time zone
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $selectedYear = $_GET['year'];
    $selectedMonth = $_GET['month'];

    $query = "SELECT
            b.[category],
            COUNT(bh.[bh_id]) as borrowrecords
        FROM [borrowinghistory] bh
        LEFT JOIN [book] b ON bh.[ISBN] = b.[ISBN]
        WHERE MONTH(bh.[borrow_at]) = ? AND YEAR(bh.[borrow_at]) = ?
        GROUP BY b.[category]";
    $array = [$selectedMonth, $selectedYear];
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