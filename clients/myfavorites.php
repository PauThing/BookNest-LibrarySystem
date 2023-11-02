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
        function openDetail(isbn, url) {
            window.location.href = '../clients/bookdetails.php?ISBN=' + isbn + '&currentURL=' + url;
        }

        function delFav(isbn, url) {
            window.location.href = '../clients/backend/delfavoritedb.php?ISBN=' + isbn + '&currentURL=' + url;
        }

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
        <div class="tab-container">
            <div class="tabs">
                <button id="bcategory" class="bcategory" onclick="updateCategory('Computer Science and Information')">Computer Science and Information</button>
                <button id="bcategory" class="bcategory" onclick="updateCategory('Philosophy and Psychology')">Philosophy and Psychology</button>
                <button id="bcategory" class="bcategory" onclick="updateCategory('Science and Technology')">Science and Technology</button>
                <button id="bcategory" class="bcategory" onclick="updateCategory('Literature')">Literature</button>
                <button id="bcategory" class="bcategory" onclick="updateCategory('History and Geography')">History and Geography</button>
                <button id="bcategory" class="bcategory" onclick="updateCategory('Business and Economics')">Business and Economics</button>
            </div>

            <div class="tab-content">
                <div class="catalog-container">
                    <?php
                    $itemsPerPage = 20;
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($currentPage - 1) * $itemsPerPage;

                    if (isset($_GET['bcategory'])) {
                        $bcategory = $_GET['bcategory'];

                        $cquery = "SELECT COUNT(bm.ISBN) AS ttlrecord 
                            FROM [bookmark] bm 
                            LEFT JOIN [book] b ON bm.ISBN = b.ISBN
                            WHERE bm.user_id = ? AND b.category = ?";
                        $carray = [$_SESSION['userid'], $bcategory];
                        $cstatement = sqlsrv_query($conn, $cquery, $carray);

                        $ttlrecord = 0;

                        if ($cstatement) {
                            $crow = sqlsrv_fetch_array($cstatement);
                            $ttlrecord = $crow['ttlrecord'];
                        }
                        // calculate the total number of pages
                        $ttlpages = ceil($ttlrecord / $itemsPerPage);

                        //get the current URI
                        $currentURL = $_SERVER['REQUEST_URI'];

                        //check if the current URL already contains query parameters
                        $separator = (strpos($currentURL, '?') !== false) ? '&' : '?';

                        $query = "SELECT
                                bm.*,
                                bc.*,
                                b.*,
                                u.*
                            FROM [bookmark] bm
                            LEFT JOIN [bookcatalog] bc ON bm.ISBN = bc.ISBN
                            LEFT JOIN [book] b ON bm.ISBN = b.ISBN
                            LEFT JOIN [user] u ON bm.user_id = u.user_id
                            WHERE bm.user_id = ? AND b.category = ?
                            ORDER BY b.book_title ASC OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                        $array = [$_SESSION['userid'], $bcategory];
                        $statement = sqlsrv_query($conn, $query, $array);

                        $norecord = true;
                        while ($row = sqlsrv_fetch_array($statement)) {
                            $isbn = $row['ISBN'];
                            $bktitle = $row['book_title'];
                    ?>
                            <div class="book">
                                <div class="book-detail">
                                    <div class="coverimage" onclick="openDetail('<?php echo $isbn; ?>', '<?php echo $currentURL . $separator; ?>')">
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

                                    <div class="detail" onclick="openDetail('<?php echo $isbn; ?>', '<?php echo $currentURL . $separator; ?>')">
                                        <h4><?php echo $row['book_title']; ?></h4>
                                        <label id="author" class="author"><?php echo $row['author']; ?></label><br />
                                        <label id="publiyear" class="publiyear">Published on <?php echo $row['publication_year']; ?></label><br />
                                        <label id="location" class="location"><?php echo $row['book_location']; ?></label>
                                    </div>

                                    <div class="del-action" onclick="delFav('<?php echo $isbn; ?>', '<?php echo $currentURL . $separator; ?>')">
                                        <i class="fa fa-remove"></i>
                                    </div>
                                </div>
                            </div>
                        <?php
                            $norecord = false;
                        }

                        if ($norecord) {
                            echo "No record.";
                        }
                    } else {
                        $cquery = "SELECT COUNT(*) AS ttlrecord FROM [bookmark] WHERE [user_id] = ?";
                        $carray = [$_SESSION['userid']];
                        $cstatement = sqlsrv_query($conn, $cquery, $carray);
                        $ttlrecord = 0;

                        if ($cstatement) {
                            $crow = sqlsrv_fetch_array($cstatement);
                            $ttlrecord = $crow['ttlrecord'];
                        }
                        //calculate the total number of pages
                        $ttlpages = ceil($ttlrecord / $itemsPerPage);

                        //get the current URI
                        $currentURL = $_SERVER['REQUEST_URI'];

                        //check if the current URL already contains query parameters
                        $separator = (strpos($currentURL, '?') !== false) ? '&' : '?';

                        $query2 = "SELECT
                                bm.*,
                                bc.*,
                                b.*,
                                u.*
                            FROM [bookmark] bm
                            LEFT JOIN [bookcatalog] bc ON bm.ISBN = bc.ISBN
                            LEFT JOIN [book] b ON bm.ISBN = b.ISBN
                            LEFT JOIN [user] u ON bm.user_id = u.user_id
                            WHERE bm.user_id = ?
                            ORDER BY [book_title] ASC OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                        $array2 = [$_SESSION['userid']];
                        $statement2 = sqlsrv_query($conn, $query2, $array2);

                        $norecord = true;
                        while ($row2 = sqlsrv_fetch_array($statement2)) {
                            $isbn = $row2['ISBN'];
                            $bktitle = $row2['book_title'];
                        ?>
                            <div class="book">
                                <div class="book-detail">
                                    <div class="coverimage" onclick="openDetail('<?php echo $isbn; ?>', '<?php echo $currentURL . $separator; ?>')">
                                        <?php
                                        //check if there is image data in the row
                                        if ($row2['cover_img']) {
                                            //get the image data from the row
                                            $imageBinary = $row2['cover_img'];

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

                                    <div class="detail" onclick="openDetail('<?php echo $isbn; ?>', '<?php echo $currentURL . $separator; ?>')">
                                        <h4><?php echo $row2['book_title']; ?></h4>
                                        <label id="author" class="author"><?php echo $row2['author']; ?></label><br />
                                        <label id="publiyear" class="publiyear">Published on <?php echo $row2['publication_year']; ?></label><br />
                                        <label id="location" class="location"><?php echo $row2['book_location']; ?></label>
                                    </div>

                                    <div class="del-action" onclick="delFav('<?php echo $isbn; ?>', '<?php echo $currentURL . $separator; ?>')">
                                        <i class="fa fa-remove"></i>
                                    </div>
                                </div>
                            </div>
                    <?php
                            $norecord = false;
                        }

                        if ($norecord) {
                            echo "No record.";
                        }
                    } ?>
                </div>

                <!-- Pagination - page by page -->
                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['searchInput']) && !empty($_POST['searchInput'])) { ?>
                    <div class="pagination-container" style="display: none;"></div>
                <?php } else {
                    //get the current URI
                    $currentURL = $_SERVER['REQUEST_URI'];

                    //check if the current URL already contains query parameters
                    $separator = (strpos($currentURL, '?') !== false) ? '&' : '?'; ?>
                    <div class="pagination-container">
                        <ul class="pagination">
                            <?php
                            if ($currentPage > 1) {
                                $previousPage = $currentURL . $separator . 'page=' . ($currentPage - 1);
                                echo "<li><a href='$previousPage'>Previous</a></li>";
                            } else {
                                echo "<li><span>Previous</span></li>";
                            }

                            if ($currentPage < $ttlpages) {
                                $nextPage = $currentURL . $separator . 'page=' . ($currentPage + 1);
                                echo "<li><a href='$nextPage'>Next</a></li>";
                            } else {
                                echo "<li><span>Next</span></li>";
                            }
                            ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>
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

    <script>

    </script>
</body>

</html>