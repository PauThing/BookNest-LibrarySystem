<?php
//set the session timeout to 8 hours (8 hours * 60 minutes * 60 seconds)
ini_set('session.gc_maxlifetime', 8 * 60 * 60);
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
        function openNewBook() {
            window.location.href = '../admin/addbook.php';
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
        <div class="add-book" onclick="openNewBook()">
            <i class="fa fa-plus"></i> New Book
        </div>

        <div class="tab-container">
            <form method="post" action="../admin/ebookcatalog.php">
                <div class="search-box">
                    <input type="text" name="searchInput" id="searchInput" class="searchInput" placeholder="Search by book title or book category" onkeyup="searchApproved(event)">
                    <input type="hidden" name="currentPage" id="currentPage" value="1">
                    <button class="search-btn" name="search-btn" style="display: none;"></button>
                </div>
            </form>

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
                    $itemsPerPage = 10;
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($currentPage - 1) * $itemsPerPage;

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['searchInput']) && !empty($_POST['searchInput'])) {
                        $search = '%' . $_POST['searchInput'] . '%';
                        $array = [$search, $search];

                        //get the current URI
                        $currentURL = $_SERVER['REQUEST_URI'];

                        //check if the current URL already contains query parameters
                        $separator = (strpos($currentURL, '?') !== false) ? '&' : '?';

                        $query = "SELECT
                                bc.*,
                                b.*
                            FROM [bookcatalog] bc
                            LEFT JOIN [book] b ON bc.ISBN = b.ISBN
                            WHERE b.category LIKE ? OR b.book_title LIKE ?";
                        $statement = sqlsrv_query($conn, $query, $array);

                        $norecord = true;
                        while ($row = sqlsrv_fetch_array($statement)) {
                            $isbn = $row['ISBN'];
                            $bktitle = $row['book_title'];
                    ?>
                            <div class="book">
                                <div class="book-detail">
                                    <div class="coverimage">
                                        <?php //check if there is image data in the row
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
                                        <h4><?php echo $row['book_title']; ?></h4>
                                        <label id="author" class="author"><?php echo $row['author']; ?></label><br />
                                        <label id="publiyear" class="publiyear">Published on <?php echo $row['publication_year']; ?></label><br />
                                        <label id="location" class="location"><?php echo $row['book_location']; ?></label>
                                    </div>

                                    <div class="edit-action" onclick="openForm('<?php echo $isbn; ?>', '<?php echo $currentURL . $separator; ?>')">
                                        <i class="fa fa-edit"></i>
                                    </div>

                                    <div class="del-action">
                                        <a href="../admin/backend/delbookdb.php?bktitle=<?php echo $bktitle; ?>&ISBN=<?php echo $isbn; ?>&currentURL=<?php echo $currentURL . $separator; ?>" class="del" onclick="return confirm('Are you sure you want to delete this book?');"><i class="fa fa-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php
                            $norecord = false;
                        }

                        if ($norecord) {
                            echo "No record.";
                        }
                    } else if (isset($_GET['bcategory'])) {
                        $bcategory = $_GET['bcategory'];

                        $cquery = "SELECT COUNT(*) AS ttlrecord FROM [book] WHERE [category] = ?";
                        $carray = [$bcategory];
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

                        $query2 = "SELECT
                                bc.*,
                                b.*
                            FROM [bookcatalog] bc
                            LEFT JOIN [book] b ON bc.ISBN = b.ISBN
                            WHERE b.category = ?
                            ORDER BY [book_title] ASC OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                        $array2 = [$bcategory];
                        $statement2 = sqlsrv_query($conn, $query2, $array2);

                        $norecord = true;
                        while ($row2 = sqlsrv_fetch_array($statement2)) {
                            $isbn = $row2['ISBN'];
                            $bktitle = $row2['book_title'];
                        ?>
                            <div class="book">
                                <div class="book-detail">
                                    <div class="coverimage">
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

                                    <div class="detail">
                                        <h4><?php echo $row2['book_title']; ?></h4>
                                        <label id="author" class="author"><?php echo $row2['author']; ?></label><br />
                                        <label id="publiyear" class="publiyear">Published on <?php echo $row2['publication_year']; ?></label><br />
                                        <label id="location" class="location"><?php echo $row2['book_location']; ?></label>
                                    </div>

                                    <div class="edit-action" onclick="openForm('<?php echo $isbn; ?>', '<?php echo $currentURL . $separator; ?>')">
                                        <i class="fa fa-edit"></i>
                                    </div>

                                    <div class="del-action">
                                        <a href="../admin/backend/delbookdb.php?bktitle=<?php echo $bktitle; ?>&ISBN=<?php echo $isbn; ?>&currentURL=<?php echo $currentURL . $separator; ?>" class="del" onclick="return confirm('Are you sure you want to delete this book?');"><i class="fa fa-trash"></i></a>
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
                        $cquery = "SELECT COUNT(*) AS ttlrecord FROM [bookcatalog]";
                        $cstatement = sqlsrv_query($conn, $cquery);
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

                        $query3 = "SELECT
                                bc.*,
                                b.*
                            FROM [bookcatalog] bc
                            LEFT JOIN [book] b ON bc.ISBN = b.ISBN
                            ORDER BY [book_title] ASC OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                        $statement3 = sqlsrv_query($conn, $query3);

                        $norecord = true;
                        while ($row3 = sqlsrv_fetch_array($statement3, SQLSRV_FETCH_ASSOC)) {
                            $isbn = $row3['ISBN'];
                            $bktitle = $row3['book_title'];
                        ?>
                            <div class="book">
                                <div class="book-detail">
                                    <div class="coverimage">
                                        <?php
                                        //check if there is image data in the row
                                        if ($row3['cover_img']) {
                                            //get the image data from the row
                                            $imageBinary = $row3['cover_img'];

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
                                        <h4><?php echo $row3['book_title']; ?></h4>
                                        <label id="author" class="author"><?php echo $row3['author']; ?></label><br />
                                        <label id="publiyear" class="publiyear">Published on <?php echo $row3['publication_year']; ?></label><br />
                                        <label id="location" class="location"><?php echo $row3['book_location']; ?></label>
                                    </div>

                                    <div class="edit-action" onclick="openForm('<?php echo $isbn; ?>', '<?php echo $currentURL . $separator; ?>')">
                                        <i class="fa fa-edit"></i>
                                    </div>

                                    <div class="del-action">
                                        <a href="../admin/backend/delbookdb.php?bktitle=<?php echo $bktitle; ?>&ISBN=<?php echo $isbn; ?>&currentURL=<?php echo $currentURL . $separator; ?>" class="del" onclick="return confirm('Are you sure you want to delete this book?');"><i class="fa fa-trash"></i></a>
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
    </div>

    <div class="overlay-bg" id="overlay-bg"></div>

    <div class="edit-book-container" id="edit-book-container"></div>

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
        //search function
        function searchApproved(event) {
            if (event.key === 'Enter') {
                //prevent form submission (if within a form)
                event.preventDefault();

                const searchInput = document.getElementById('searchInput').value;

                document.getElementById('search-btn').click();
            }
        }

        function openForm(bisbn, currentURL) {
            $.ajax({
                type: 'GET',
                url: '../admin/ebookdetail.php',
                data: {
                    isbn: bisbn,
                    cURL: currentURL
                },
                success: function(response) {
                    $('#edit-book-container').html(response);
                    document.getElementById("edit-book-container").style.display = "block";
                    document.getElementById("overlay-bg").style.display = "block";
                },
                error: function() {
                    alert('Failed to load user details.');
                }
            });
        }

        function closeForm() {
            document.getElementById("edit-book-container").style.display = "none";
            document.getElementById("overlay-bg").style.display = "none";
        }
    </script>
</body>

</html>