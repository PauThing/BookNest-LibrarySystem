<?php
$serverName = "LAPTOP-FOGH91GN";
$connectionOptions = array(
    "Database" => "LibrarySystem",
    "Uid" => "sa",
    "PWD" => "sqlserverPT2001"
);

//establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Connection failed: " . sqlsrv_errors());
}
?>