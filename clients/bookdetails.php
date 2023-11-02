<?php
//set the session timeout to 4 hours (4 hours * 60 minutes * 60 seconds)
ini_set('session.gc_maxlifetime', 4 * 60 * 60);
session_start();
if (!isset($_SESSION['userid']) || trim($_SESSION['userid'] == '')) {
    header('location: ../clients/signin.php');
    exit();
}

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
    <link rel="stylesheet" href="../clients/styles/bookcatalog.css">

    <title>Book Catalog</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateCategory(category) {
            //get the current URL without any query parameters
            var currentURL = window.location.pathname;

            //append the selected category to the URL as a query parameter
            var newURL = currentURL + '?bcategory=' + category;

            //redirect to the new URL
            window.location.href = newURL;
        }
    </script>
</head>

<body>
    <div class="big-container">

        <div class="book-detail-container">
            <div class="book">
                <div class="book-detail">
                    <?php
                    //get the current URI
                    $currentURL = $_SERVER['REQUEST_URI'];

                    //check if the current URL already contains query parameters
                    $containsQuestionMark = (strpos($currentURL, '?') !== false);
                    $containsAmpersand = (strrpos($currentURL, '&') !== false);

                    if (isset($_GET['ISBN'])) {
                        $bisbn = $_GET['ISBN'];

                        $query = "SELECT
                                        bc.*,
                                        b.*
                                    FROM [bookcatalog] bc
                                    LEFT JOIN [book] b ON bc.ISBN = b.ISBN
                                    WHERE bc.ISBN = ? AND b.ISBN = ?";
                        $array = [$bisbn, $bisbn];
                        $statement = sqlsrv_query($conn, $query, $array);

                        while ($row = sqlsrv_fetch_array($statement)) {
                            $isbn = $row['ISBN'];
                            $bktitle = $row['book_title'];
                    ?>
                            <div class="coverimage">
                                <?php
                                //check if there is image data in the row
                                if ($row['cover_img']) {
                                    //get the image data from the row
                                    $imageBinary = $row['cover_img'];

                                    //detect the image format
                                    $image = getimagesizefromstring($imageBinary);
                                    if ($image !== false) {
                                        //determine the MIME type based on the detected image format
                                        $mimeType = $image['mime'];
                                ?>
                                        <img src="data:<?php echo $mimeType; ?>;base64,<?php echo base64_encode($imageBinary); ?>" class="cover-img">

                                <?php
                                    } else {
                                        echo "Unable to detect image format.";
                                    }
                                } else {
                                    echo "<img src='../clients/assets/cover_unavailable.png' class='cover-img'>";
                                }
                                ?>
                            </div>

                            <div class="detail">
                                <h3><?php echo $row['book_title']; ?></h3>
                                <br />

                                <label id="author" class="author">Author: <b><?php echo $row['author']; ?></b></label>

                                <br /><br />

                                <label id="publication" class="publication">Publication: <b><?php echo $row['publication']; ?></b></label><br />
                                <label id="publiyear" class="publiyear">Published on <?php echo $row['publication_year']; ?></label>

                                <br /><br />

                                <label id="location" class="location">Location: <?php echo $row['book_location']; ?></label>

                                <br /><br />

                                <label id="status" class="status">Status:
                                    <?php if ($row['available_qty'] > 0) {
                                        echo "Available";
                                    } else {
                                        echo "Unavailable";
                                    } ?>
                                </label>
                            </div>

                            <div class="borrow-action">
                                <?php if ($row['available_qty'] > 0) { ?>
                                    <a href="javascript:void(0);" onclick="openForm('<?php echo $isbn; ?>', '<?php echo $currentURL; ?>')">Borrow</a>
                                <?php } else { ?>
                                    <a disabled>Borrow</a>
                                <?php } ?>
                            </div>
                    <?php
                        }
                    } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="overlay-bg" id="overlay-bg"></div>

    <div class="book-borrow-container" id="book-borrow-container"></div>

    <br /><br />

    <span>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        }

        unset($_SESSION['message']);
        ?>
    </span>

    <script>
        function openForm(bisbn, url) {
            $.ajax({
                type: 'GET',
                url: '../clients/bookborrowing.php',
                data: {
                    isbn: bisbn,
                    currURL: url
                },
                success: function(response) {
                    $('#book-borrow-container').html(response);
                    document.getElementById("book-borrow-container").style.display = "block";
                    document.getElementById("overlay-bg").style.display = "block";
                },
                error: function() {
                    alert('Failed to load borrow information.');
                }
            });
        }

        function closeForm() {
            document.getElementById("book-borrow-container").style.display = "none";
            document.getElementById("overlay-bg").style.display = "none";
        }
    </script>
</body>

</html>