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
    <link rel="stylesheet" href="../clients/styles/reservation.css">

    <title>Discussion Room Reservation</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

    </script>
</head>

<body>
    <div class="big-container">
        <div id="reservation-list" class="reservation-list">
            <div class="personal-reservation-container">
                <table id="reservation">
                    <thead>
                        <tr>
                            <th>Discussion Room</th>
                            <th>Time Slot</th>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>No. of Member</th>
                            <th>Reserved Date</th>
                        </tr>
                    </thead>

                    <?php
                    $userid = $_SESSION['userid'];

                    $itemsPerPage = 20;
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($currentPage - 1) * $itemsPerPage;

                    $cquery = "SELECT COUNT(*) AS ttlrecord FROM [reservation]";
                    $cstatement = sqlsrv_query($conn, $cquery);
                    $ttlrecord = 0;

                    if ($cstatement) {
                        $crow = sqlsrv_fetch_array($cstatement);
                        $ttlrecord = $crow['ttlrecord'];
                    }
                    //calculate the total number of pages
                    $ttlpages = ceil($ttlrecord / $itemsPerPage);

                    $query = "SELECT
                            r.*,
                            u.fullname,
                            dr.droom_num
                        FROM [reservation] r
                        LEFT JOIN [user] u ON r.user_id = u.user_id
                        LEFT JOIN [discussionroom] dr ON r.droom_id = dr.droom_id
                        WHERE r.user_id = '$userid'
                        ORDER BY [created_at] OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";

                    $statement = sqlsrv_query($conn, $query);

                    if ($statement === false) {
                        die(print_r(sqlsrv_errors(), true)); //print and handle the error
                    }

                    $norecord = true;
                    while ($row = sqlsrv_fetch_array($statement)) {
                    ?>
                        <tbody>
                            <tr>
                                <td><?php echo $row['droom_num']; ?></td>
                                <td><?php echo $row['time_slot']; ?></td>
                                <td><?php echo $row['user_id']; ?></td>
                                <td><?php echo $row['fullname']; ?></td>
                                <td><?php echo $row['member']; ?></td>
                                <td><?php echo $row['created_at']->format('Y-m-d');; ?></td>
                            </tr>
                        </tbody>
                    <?php
                        $norecord = false;
                    }

                    if ($norecord) {
                        echo "<tbody><tr><td colspan='7'>No records found.</td></tr></tbody>";
                    } ?>
                </table>
            </div>

            <!-- Pagination - page by page -->
            <div class="pagination-container">
                <ul class="pagination">
                    <?php
                    if ($currentPage > 1) {
                        echo "<li><a href='?page=" . ($currentPage - 1) . "'>Previous</a></li>";
                    } else {
                        echo "<li><span>Previous</span></li>";
                    }

                    if ($currentPage < $ttlpages) {
                        echo "<li><a href='?page=" . ($currentPage + 1) . "'>Next</a></li>";
                    } else {
                        echo "<li><span>Next</span></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>

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
        //search function for approved list
        function searchHistory(event) {
            if (event.key === 'Enter') {
                //prevent form submission (if within a form)
                event.preventDefault();

                const searchInput = document.getElementById('searchInput').value;
                const currentPage = document.getElementById('currentPage').value;

                //create the query string
                const urlstring = `?page=${currentPage}&searchInput=${searchInput}`;

                //update the URL without reloading the page
                history.pushState(null, '', urlstring);

                document.getElementById('search-btn').click();
            }
        }
    </script>
</body>

</html>