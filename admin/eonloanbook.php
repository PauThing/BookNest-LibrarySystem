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
    <link rel="stylesheet" href="../clients/styles/onloanbook.css">

    <title>Borrow & Return</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

    </script>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>Books Pending Shelf Retrieval</h3>
        </div>
        <div id="onloan-list" class="onloan-list">
            <div class="onloan-container">
                <table id="onloan">
                    <thead>
                        <tr>
                            <th class="uid">User ID</th>
                            <th class="isbn">ISBN</th>
                            <th class="title">Book Title</th>
                            <th class="bdate">Borrow Date</th>
                            <th class="location">Location</th>
                        </tr>
                    </thead>

                    <?php
                    //set time zone
                    date_default_timezone_set('Asia/Kuala_Lumpur');
                    $currD = date('Y-m-d');

                    $beforeCurrD = date('Y-m-d', strtotime('-1 day', strtotime($currD)));

                    $query = "SELECT
                            bh.*,
                            bc.*,
                            b.*
                        FROM [borrowinghistory] bh
                        LEFT JOIN [bookcatalog] bc ON bh.[catalog_id] = bc.[catalog_id]
                        LEFT JOIN [book] b ON bh.[ISBN] = b.[ISBN]
                        WHERE bh.[status] = 'On Loan' AND (bh.[borrow_at] = '$beforeCurrD' OR bh.[borrow_at] = '$currD')
                        ORDER BY [due_at] ASC";

                    $statement = sqlsrv_query($conn, $query);

                    if ($statement === false) {
                        die(print_r(sqlsrv_errors(), true)); //print and handle the error
                    }

                    $norecord = true;
                    while ($row = sqlsrv_fetch_array($statement)) {
                        $borrow = $row['borrow_at'];
                        $brwDate = $borrow->format('Y-m-d');

                        $due = $row['due_at'];
                        $dueDate = $due->format('Y-m-d');
                    ?>
                        <tbody>
                            <tr>
                                <td><?php echo $row['user_id']; ?></td>
                                <td><?php echo $row['ISBN']; ?></td>
                                <td><?php echo $row['book_title']; ?></td>
                                <td><?php echo $brwDate; ?></td>
                                <td><?php echo $row['book_location']; ?></td>
                            </tr>
                        </tbody>
                    <?php
                        $norecord = false;
                    }

                    if ($norecord) {
                        echo "<tbody><tr><td colspan='5'>No records found.</td></tr></tbody>";
                    } ?>
                </table>
            </div>
        </div>

        <br /><br />

        <!-- Book List for return process -->
        <div class="header">
            <h3>Books to Return</h3>
        </div>
        <div id="onloan-list" class="onloan-list">
            <form method="post" action="../admin/eonloanbook.php">
                <div class="search-box">
                    <input type="text" name="searchInput" id="searchInput" class="searchInput" placeholder="Search by book ISBN or book title" onkeyup="searchApproved(event)">
                    <input type="hidden" name="currentPage" id="currentPage" value="1">
                    <button class="search-btn" name="search-btn" style="display: none;"></button>
                </div>
            </form>

            <div class="return-container">
                <table id="return">
                    <thead>
                        <tr>
                            <th class="isbn">ISBN</th>
                            <th class="title">Book Title</th>
                            <th class="bdate">Borrow Date</th>
                            <th class="ddate">Due Date</th>
                            <th class="action">Action</th>
                        </tr>
                    </thead>

                    <?php
                    $itemsPerPage = 10;
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($currentPage - 1) * $itemsPerPage;

                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['searchInput']) && !empty($_POST['searchInput'])) {
                        $search = '%' . $_POST['searchInput'] . '%';
                        $array = [$search, $search];

                        $query = "SELECT
                            bh.*,
                            bc.*,
                            b.*,
                            u.*
                        FROM [borrowinghistory] bh
                        LEFT JOIN [bookcatalog] bc ON bh.[catalog_id] = bc.[catalog_id]
                        LEFT JOIN [book] b ON bh.[ISBN] = b.[ISBN]
                        LEFT JOIN [user] u ON bh.[user_id] = u.[user_id]
                        WHERE bh.[status] = 'On Loan' AND (bh.[ISBN] LIKE ? OR b.[book_title] LIKE ?)
                        ORDER BY [due_at] ASC";
                        $statement = sqlsrv_query($conn, $query, $array);

                        if ($statement === false) {
                            die(print_r(sqlsrv_errors(), true)); //print and handle the error
                        }

                        $norecord = true;
                        while ($row = sqlsrv_fetch_array($statement)) {
                            $borrow = $row['borrow_at'];
                            $brwDate = $borrow->format('Y-m-d');

                            $due = $row['due_at'];
                            $dueDate = $due->format('Y-m-d');
                    ?>
                            <tbody>
                                <tr>
                                    <td><?php echo $row['ISBN']; ?></td>
                                    <td><?php echo $row['book_title']; ?></td>
                                    <td><?php echo $brwDate; ?></td>
                                    <td><?php echo $dueDate; ?></td>
                                    <td class="action"><a href="javascript:void(0);" class="return" onclick="openForm('<?php echo $row['ISBN']; ?>', '<?php echo $row['user_id']; ?>')"><i class="fa fa-angle-double-left"></i> Return</a></td>
                                </tr>
                            </tbody>
                        <?php
                            $norecord = false;
                        }

                        if ($norecord) {
                            echo "<tbody><tr><td colspan='5'>No records found.</td></tr></tbody>";
                        }
                    } else {
                        $cquery = "SELECT COUNT(*) AS ttlrecord FROM [borrowinghistory] WHERE [status] = 'On Loan'";
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

                        $query = "SELECT
                            bh.*,
                            bc.*,
                            b.*,
                            u.*
                        FROM [borrowinghistory] bh
                        LEFT JOIN [bookcatalog] bc ON bh.[catalog_id] = bc.[catalog_id]
                        LEFT JOIN [book] b ON bh.[ISBN] = b.[ISBN]
                        LEFT JOIN [user] u ON bh.[user_id] = u.[user_id]
                        WHERE [status] = 'On Loan'
                        ORDER BY [due_at] ASC OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";

                        $statement = sqlsrv_query($conn, $query);

                        if ($statement === false) {
                            die(print_r(sqlsrv_errors(), true)); //print and handle the error
                        }

                        $norecord = true;
                        while ($row = sqlsrv_fetch_array($statement)) {
                            $borrow = $row['borrow_at'];
                            $brwDate = $borrow->format('Y-m-d');

                            $due = $row['due_at'];
                            $dueDate = $due->format('Y-m-d');
                        ?>
                            <tbody>
                                <tr>
                                    <td><?php echo $row['ISBN']; ?></td>
                                    <td><?php echo $row['book_title']; ?></td>
                                    <td><?php echo $brwDate; ?></td>
                                    <td><?php echo $dueDate; ?></td>
                                    <td class="action"><a href="javascript:void(0);" class="return" onclick="openForm('<?php echo $row['ISBN']; ?>', '<?php echo $row['user_id']; ?>')"><i class="fa fa-angle-double-left"></i> Return</a></td>
                                </tr>
                            </tbody>
                    <?php
                            $norecord = false;
                        }

                        if ($norecord) {
                            echo "<tbody><tr><td colspan='5'>No records found.</td></tr></tbody>";
                        }
                    } ?>
                </table>
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

    <div class="overlay-bg" id="overlay-bg"></div>

    <div class="returnb-container" id="returnb-container"></div>

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

        function openForm(isbn, userid) {
            $.ajax({
                type: 'GET',
                url: '../admin/returnbook.php',
                data: {
                    bisbn: isbn,
                    uid: userid
                },
                success: function(response) {
                    $('#returnb-container').html(response);
                    document.getElementById("returnb-container").style.display = "block";
                    document.getElementById("overlay-bg").style.display = "block";
                },
                error: function() {
                    alert('Failed to load return details.');
                }
            });
        }

        function closeForm() {
            document.getElementById("returnb-container").style.display = "none";
            document.getElementById("overlay-bg").style.display = "none";
        }
    </script>
</body>

</html>