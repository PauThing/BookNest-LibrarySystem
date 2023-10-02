<?php
// Set the session timeout to 4 hours (4 hours * 60 minutes * 60 seconds)
ini_set('session.gc_maxlifetime', 4 * 60 * 60);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../clients/styles/about.css">


    <title>Rules & Regulation</title>

    <script src="tinymce/js/tinymce/tinymce.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tinymce.init({
            selector: '#rules-info',
            plugins: 'searchreplace autoresize image table lists',
        });
    </script>
</head>

<body>
    <div class="big-container">
        <?php
        // SQL Query to retrieve data from a table
        $sql = "SELECT * FROM [libraryinfo] WHERE [info_type] = 'rules'";

        // Execute the SQL query
        $query = sqlsrv_query($conn, $sql);

        // Check if the query was successful
        if ($query === false) {
            die("Query failed: " . print_r(sqlsrv_errors(), true));
        }

        // Fetch and display data from the result set
        while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        ?>

            <div class="rules-container">
                <form class="rules-form" id="rules-form" method="post" action="./backend/erulesdb.php">
                    <div class="wrap">
                        <div class="header">
                            <h4>Rules & Regulation</h4>
                        </div>

                        <div class="edit-rules-text">
                            <textarea name="rules-info" id="rules-info"><?php echo $row['info_text']; ?></textarea>
                        </div>

                        <div class="editrules-btn">
                            <input type="submit" name="editrules" id="editrules" class="editrules" value="Save" />
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