<?php
include('navbar.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./styles/about.css">

    <title>About Library</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>About Library</h3>
        </div>

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
                <form class="librarian-form" id="librarian-form" action="">
                    <div class="wrap">
                        <div class="header">
                            <h4>The Librarians</h4>

                            <br />

                            <div class="librarian-details">
                                <?php
                                if ($row['info_img'] == null) {
                                    echo '<div class="show-librarian-text">' . $row['info_text'] . '</div>';
                                } elseif ($row['info_text'] == null) {
                                    // Get the image data from the row
                                    $imageBinary = $row['info_img'];

                                    // Detect the image format
                                    $image = getimagesizefromstring($imageBinary);
                                    if ($image !== false) {
                                        // Determine the MIME type based on the detected image format
                                        $mimeType = $image['mime'];
                                        echo '<div class="show-librarian-pic">
                                                <img src="data:' . $mimeType . ';base64,' . base64_encode($imageBinary) . '" name="show-librarian" id="show-librarian" class="show-librarian" />
                                            </div>';
                                    }
                                } elseif ($row['info_text'] != null && $row['info_img'] != null) {
                                    // Get the image data from the row
                                    $imageBinary = $row['info_img'];

                                    // Detect the image format
                                    $image = getimagesizefromstring($imageBinary);
                                    if ($image !== false) {
                                        // Determine the MIME type based on the detected image format
                                        $mimeType = $image['mime'];
                                        echo '<div class="show-librarian-pic">
                                                    <img src="data:' . $mimeType . ';base64,' . base64_encode($imageBinary) . '" name="show-librarian" id="show-librarian" class="show-librarian" />
                                            </div> <br />';
                                    }
                                    echo '<div class="show-librarian-text">' . $row['info_text'] . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        <?php } ?>

        <br /><br />

        <div class="membership-container">
            <form class="member-form" id="member-form" action="">
                <div class="wrap">
                    <div class="header">
                        <h4>Membership</h4>
                    </div>
                </div>
            </form>
        </div>

        <br /><br />

        <div class="ophours-container">
            <form class="ophours-form" id="ophours-form" action="">
                <div class="wrap">
                    <div class="header">
                        <h4>Opening Hours</h4>
                    </div>
                </div>
            </form>
        </div>
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