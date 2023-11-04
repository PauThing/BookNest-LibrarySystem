<?php
//set the session timeout to 8 hours (8 hours * 60 minutes * 60 seconds)
ini_set('session.gc_maxlifetime', 8 * 60 * 60);
session_start();

include('../clients/navbar.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../clients/styles/about.css">

    <title>Rules and Regulation</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="big-container">
        <?php
        $query = "SELECT * FROM [libraryinfo] WHERE [info_type] = 'rules'";
        $statement = sqlsrv_query($conn, $query);

        //check if the query was successful
        if ($statement === false) {
            die("Query failed: " . print_r(sqlsrv_errors(), true));
        }

        //fetch and display data from databse
        while ($row = sqlsrv_fetch_array($statement, SQLSRV_FETCH_ASSOC)) {
        ?>
            <div class="rules-container">
                <form class="rules-form" id="rules-form">
                    <div class="wrap">
                        <div class="header">
                            <h4>Rules & Regulation</h4>
                        </div>

                        <div class="show-rules-text">
                            <?php echo html_entity_decode($row['info_text']); ?>
                        </div>
                    </div>
                </form>
            </div>
        <?php } ?>
    </div>

    <span>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        }

        unset($_SESSION['message']);
        ?>
    </span>
</body>

</html>