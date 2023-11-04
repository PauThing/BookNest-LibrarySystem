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
        function openDetail(isbn) {
            window.location.href = '../clients/bookdetails.php?ISBN=' + isbn;
        }

        function openFav(isbn, url) {
            window.location.href = '../clients/backend/bookfavoritedb.php?ISBN=' + isbn + '&currentURL=' + url;
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
            <form method="post" action="../clients/bookcatalog.php">
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
            <div id="recommendation-container" class="recommendation-container">
                <h5>You May Interested: </h5>

                <div class="books-row"></div>
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
                        $containsQuestionMark = (strpos($currentURL, '?') !== false);
                        $containsAmpersand = (strrpos($currentURL, '&') !== false);

                        if ($containsQuestionMark && $containsAmpersand) {
                            $separator = '&'; // Both ? and & are present
                        } else {
                            $separator = ($containsQuestionMark) ? '&' : '?';
                        }

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
                                    <div class="coverimage" onclick="openDetail('<?php echo $isbn; ?>')">
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

                                    <div class="detail" onclick="openDetail('<?php echo $isbn; ?>')">
                                        <h4><?php echo $row['book_title']; ?></h4>
                                        <label id="author" class="author"><?php echo $row['author']; ?></label><br />
                                        <label id="location" class="location"><?php echo $row['book_location']; ?></label>
                                    </div>

                                    <div class="fav-action" onclick="openFav('<?php echo $isbn; ?>', '<?php echo $currentURL; ?>')">
                                        <?php
                                        $query2 = "SELECT * FROM [bookmark] WHERE [user_id] = ? AND [ISBN] = ?";
                                        $array2 = [$_SESSION['userid'], $isbn];
                                        $statement2 = sqlsrv_query($conn, $query2, $array2);

                                        if (sqlsrv_has_rows($statement2)) {
                                        ?>
                                            <i class="fa fa-star" style="color: #c6c6c6d1;"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-star"></i>
                                        <?php } ?>
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
                        $containsQuestionMark = (strpos($currentURL, '?') !== false);
                        $containsAmpersand = (strrpos($currentURL, '&') !== false);

                        if ($containsQuestionMark && $containsAmpersand) {
                            $separator = '&'; // Both ? and & are present
                        } else {
                            $separator = ($containsQuestionMark) ? '&' : '?';
                        }

                        $query3 = "SELECT
                                bc.*,
                                b.*
                            FROM [bookcatalog] bc
                            LEFT JOIN [book] b ON bc.ISBN = b.ISBN
                            WHERE b.category = ?
                            ORDER BY [book_title] ASC OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                        $array3 = [$bcategory];
                        $statement3 = sqlsrv_query($conn, $query3, $array3);

                        $norecord = true;
                        while ($row3 = sqlsrv_fetch_array($statement3)) {
                            $isbn = $row3['ISBN'];
                            $bktitle = $row3['book_title'];
                        ?>
                            <div class="book">
                                <div class="book-detail">
                                    <div class="coverimage" onclick="openDetail('<?php echo $isbn; ?>')">
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

                                    <div class="detail" onclick="openDetail('<?php echo $isbn; ?>')">
                                        <h4><?php echo $row3['book_title']; ?></h4>
                                        <label id="author" class="author"><?php echo $row3['author']; ?></label><br />
                                        <label id="location" class="location"><?php echo $row3['book_location']; ?></label>
                                    </div>

                                    <div class="fav-action" onclick="openFav('<?php echo $isbn; ?>', '<?php echo $currentURL; ?>')">
                                        <?php
                                        $query4 = "SELECT * FROM [bookmark] WHERE [user_id] = ? AND [ISBN] = ?";
                                        $array4 = [$_SESSION['userid'], $isbn];
                                        $statement4 = sqlsrv_query($conn, $query4, $array4);

                                        if (sqlsrv_has_rows($statement4)) {
                                        ?>
                                            <i class="fa fa-star" style="color: #c6c6c6d1;"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-star"></i>
                                        <?php } ?>
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
                        $containsQuestionMark = (strpos($currentURL, '?') !== false);
                        $containsAmpersand = (strrpos($currentURL, '&') !== false);

                        if ($containsQuestionMark && $containsAmpersand) {
                            $separator = '&'; // Both ? and & are present
                        } else {
                            $separator = ($containsQuestionMark) ? '&' : '?';
                        }

                        $query5 = "SELECT
                                bc.*,
                                b.*
                            FROM [bookcatalog] bc
                            LEFT JOIN [book] b ON bc.ISBN = b.ISBN
                            ORDER BY [book_title] ASC OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                        $statement5 = sqlsrv_query($conn, $query5);

                        $norecord = true;
                        while ($row5 = sqlsrv_fetch_array($statement5)) {
                            $isbn = $row5['ISBN'];
                            $bktitle = $row5['book_title'];
                        ?>
                            <div class="book">
                                <div class="book-detail">
                                    <div class="coverimage" onclick="openDetail('<?php echo $isbn; ?>')">
                                        <?php
                                        //check if there is image data in the row
                                        if ($row5['cover_img']) {
                                            //get the image data from the row
                                            $imageBinary = $row5['cover_img'];

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

                                    <div class="detail" onclick="openDetail('<?php echo $isbn; ?>')">
                                        <h4><?php echo $row5['book_title']; ?></h4>
                                        <label id="author" class="author"><?php echo $row5['author']; ?></label><br />
                                        <label id="location" class="location"><?php echo $row5['book_location']; ?></label>
                                    </div>

                                    <div class="fav-action" onclick="openFav('<?php echo $isbn; ?>', '<?php echo $currentURL; ?>')">
                                        <?php
                                        $query6 = "SELECT * FROM [bookmark] WHERE [user_id] = ? AND [ISBN] = ?";
                                        $array6 = [$_SESSION['userid'], $isbn];
                                        $statement6 = sqlsrv_query($conn, $query6, $array6);

                                        if (sqlsrv_has_rows($statement6)) {
                                        ?>
                                            <i class="fa fa-star" style="color: #c6c6c6d1;"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-star"></i>
                                        <?php } ?>
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
        //search function
        function searchApproved(event) {
            if (event.key === 'Enter') {
                //prevent form submission (if within a form)
                event.preventDefault();

                const searchInput = document.getElementById('searchInput').value;

                document.getElementById('search-btn').click();
            }
        }

        $(document).ready(function() {
            fetchRecommendations();
        });

        function fetchRecommendations() {
            $.getJSON('../recommendations.json', function(data) {
                displayRecommendations(data);
            }).fail(function(jqxhr, textStatus, error) {
                console.error("Error fetching recommendations: " + error);
            });
        }

        async function displayRecommendations(recommendations) {
            var recommendationContainer = document.getElementById("recommendation-container");

            for (var userId in recommendations) {
                var books = recommendations[userId];

                var sortedBooks = Object.entries(books).sort((a, b) => b[1] - a[1]);
                var topFiveBooks = sortedBooks.slice(0, 5);

                for (var [isbn] of topFiveBooks) {
                    (function(userId, bookIsbn) {
                        getBookData(userId, bookIsbn)
                            .then(function(bookData) {
                                if (bookData && !bookData.error) {
                                    var bookTitle = bookData.book_title;
                                    var coverImage = bookData.cover_img;

                                    var bookDiv = document.createElement("div");
                                    bookDiv.classList.add("book");

                                    var bookDetailDiv = document.createElement("div");
                                    bookDetailDiv.classList.add("book-detail");
                                    bookDetailDiv.onclick = function() {
                                        openDetail(bookIsbn);
                                    };

                                    var coverImageElement = document.createElement("img");
                                    coverImageElement.classList.add("cover-img");
                                    if (coverImage) {
                                        coverImageElement.src = "data:image/jpeg;base64," + coverImage;
                                    } else {
                                        coverImageElement.src = "../clients/assets/cover_unavailable.png";
                                    }

                                    var detailElement = document.createElement("div");
                                    detailElement.classList.add("detail");
                                    detailElement.innerHTML = "<p>" + bookTitle + "</p>";

                                    bookDetailDiv.appendChild(coverImageElement);
                                    bookDetailDiv.appendChild(detailElement);
                                    bookDiv.appendChild(bookDetailDiv);
                                    recommendationContainer.appendChild(bookDiv);
                                } else {
                                    console.error("Error fetching book data for ISBN: " + bookIsbn);
                                }
                            })
                            .catch(function(error) {
                                console.error("Error fetching book data: " + error);
                            });
                    })(userId, isbn);
                }
            }
        }

        function getBookData(userId, bookIsbn) {
            return new Promise(function(resolve, reject) {
                $.ajax({
                    type: "GET",
                    url: "../clients/backend/getbookdata.php",
                    data: {
                        userid: userId,
                        isbn: bookIsbn
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log("Book data retrieved successfully:", data);

                        if (data.error) {
                            console.error("Error fetching book data:", data.error);
                            reject(data.error);
                        } else {
                            resolve(data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error fetching book data.", errorThrown);
                        reject(errorThrown);
                    }
                });
            });
        }
    </script>
</body>

</html>