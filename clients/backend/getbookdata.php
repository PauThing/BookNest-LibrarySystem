<?php
    session_start();
    include('../../clients/connect.php');

    if (isset($_GET['isbn']) && isset($_GET['userid'])){
        $uid = $_GET['userid'];
        $isbn = $_GET['isbn'];

        if ($uid === $_SESSION['userid']) {
            $query = "SELECT
                    bc.[ISBN],
                    b.[book_title], 
                    b.[cover_img]
                FROM [bookcatalog] bc
                LEFT JOIN [book] b ON bc.[ISBN] = b.[ISBN]
                WHERE bc.[ISBN] = ?";
            $array = [$isbn];
            $statement = sqlsrv_query($conn, $query, $array);
            while ($row = sqlsrv_fetch_array($statement)) {
                $coverImage = base64_encode($row['cover_img']);
                echo json_encode(['ISBN' => $row['ISBN'], 'book_title' => $row['book_title'], 'cover_img' => $coverImage]);
            }
        } else {
            echo json_encode(['error' => 'Access denied']); 
        }
        sqlsrv_close($conn);
    }
?>