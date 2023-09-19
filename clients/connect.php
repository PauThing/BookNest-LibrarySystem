<?php
$serverName = "LAPTOP-FOGH91GN"; // Change this to your SQL Server instance name or IP address.
$connectionOptions = array(
    "Database" => "LibrarySystem",
    "Uid" => "sa",
    "PWD" => "sqlserverPT2001"
);

//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Connection failed: " . sqlsrv_errors());
}
?>