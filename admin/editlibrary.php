<?php
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


    <title>About Library</title>

    <script src="tinymce/js/tinymce/tinymce.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tinymce.init({
            selector: '#librarian-info',
            plugins: 'searchreplace autoresize image table lists',
        });

        tinymce.init({
            selector: '#member-info',
            plugins: 'searchreplace autoresize image table lists',
        });

        tinymce.init({
            selector: '#ophour-info',
            plugins: 'searchreplace autoresize image table lists',
        });
    </script>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>About Library</h3>
        </div>

        <div class="container-row">
            <?php
            // SQL Query to retrieve data from a table
            $sql = "SELECT * FROM [libraryinfo] WHERE [info_type] = 'librarian'";

            // Execute the SQL query
            $query = sqlsrv_query($conn, $sql);

            // Check if the query was successful
            if ($query === false) {
                die("Query failed: " . print_r(sqlsrv_errors(), true));
            }

            // Fetch and display data from the result set
            while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
            ?>

                <div class="librarian-container">
                    <form class="librarian-form" id="librarian-form" method="post" action="./backend/elibrarydb.php">
                        <div class="wrap">
                            <div class="header">
                                <h4>The Librarians</h4>
                            </div>

                            <div class="edit-librarian-text">
                                <textarea name="librarian-info" id="librarian-info"><?php echo $row['info_text']; ?></textarea>
                            </div>

                            <div class="editlibrarian-btn">
                                <input type="submit" name="editlibrarian" id="editlibrarian" class="editlibrarian" value="Save" />
                            </div>
                        </div>
                    </form>
                </div>

            <?php }

            // SQL Query to retrieve data from a table
            $sql = "SELECT * FROM [libraryinfo] WHERE [info_type] = 'openinghour'";

            // Execute the SQL query
            $query = sqlsrv_query($conn, $sql);

            // Check if the query was successful
            if ($query === false) {
                die("Query failed: " . print_r(sqlsrv_errors(), true));
            }

            // Fetch and display data from the result set
            while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
            ?>

                <br /><br />

                <div class="ophours-container">
                    <form class="ophours-form" id="ophours-form" method="post" action="./backend/eophourdb.php">
                        <div class="wrap">
                            <div class="header">
                                <h4>Library Opening Hours</h4>
                            </div>

                            <div class="edit-ophour-text">
                                <textarea name="ophour-info" id="ophour-info"><?php echo $row['info_text']; ?></textarea>
                            </div>

                            <div class="editophour-btn">
                                <input type="submit" name="editophour" id="editophour" class="editophour" value="Save" />
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>
        </div>

        <?php

        // SQL Query to retrieve data from a table
        $sql = "SELECT * FROM [libraryinfo] WHERE [info_type] = 'membership'";

        // Execute the SQL query
        $query = sqlsrv_query($conn, $sql);

        // Check if the query was successful
        if ($query === false) {
            die("Query failed: " . print_r(sqlsrv_errors(), true));
        }

        // Fetch and display data from the result set
        while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
        ?>

            <br /><br />

            <div class="membership-container">
                <form class="member-form" id="member-form" method="post" action="./backend/ememberdb.php">
                    <div class="wrap">
                        <div class="header">
                            <h4>Membership</h4>
                        </div>

                        <div class="edit-member-text">
                            <textarea name="member-info" id="member-info"><?php echo $row['info_text']; ?></textarea>
                        </div>

                        <div class="editmember-btn">
                            <input type="submit" name="editmember" id="editmember" class="editmember" value="Save" />
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