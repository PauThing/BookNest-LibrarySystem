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
    <link rel="stylesheet" href="../clients/styles/userlist.css">

    <title>User List</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function confirmDelete(userid) {
            const password = prompt("Please enter your password:");
            if (password !== null) {
                window.location.href = `../admin/backend/deluserdb.php?userid=${userid}&password=${password}`;
            }
        }
    </script>
</head>

<body>
    <div class="big-container">
        <div class="tab-container">
            <div class="tabs">
                <a href="../admin/userlist.php" id="accepted" <?php if (basename($_SERVER['PHP_SELF']) == 'userlist.php') echo 'class="tablink active"'; ?>>User List</a>
                <a href="../admin/userpending.php" id="pending" <?php if (basename($_SERVER['PHP_SELF']) == 'userpending.php') echo 'class=" tablink active"'; ?>>User Pending</a>
            </div>

            <div class="tab-content">
                <div id="user-accepted" class="tabcontent">
                    <form method="post" action="../admin/userlist.php">
                        <div class="search-box">
                            <input type="text" name="searchInput" id="searchInput" class="searchInput" placeholder="Search by student name or student ID" onkeyup="searchApproved(event)">
                            <input type="hidden" name="currentPage" id="currentPage" value="1">
                            <button class="search-btn" name="search-btn" style="display: none;"></button>
                        </div>
                    </form>

                    <!-- Approved List -->
                    <div class="userlist-container">
                        <table id="userlist">
                            <thead>
                                <tr>
                                    <th class="number">No.</th>
                                    <th>Student ID</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Registered Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <?php
                            $itemsPerPage = 20;
                            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $offset = ($currentPage - 1) * $itemsPerPage;

                            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['searchInput']) && !empty($_POST['searchInput'])) {
                                $search = '%' . $_POST['searchInput'] . '%';
                                $array = [$search, $search];

                                $query = "SELECT * FROM [user] WHERE [usertype] = 'Student' AND [acc_status] = 'Approved' AND ([user_id] LIKE ? OR [fullname] LIKE ?)";
                                $statement = sqlsrv_query($conn, $query, $array);

                                $i = 1;
                                $norecord = true;
                                while ($row = sqlsrv_fetch_array($statement)) {
                            ?>
                                    <tbody>
                                        <tr>
                                            <td class="number"><?php echo $i++; ?></td>
                                            <td><?php echo $row['user_id']; ?></td>
                                            <td><?php echo $row['fullname']; ?></td>
                                            <td><?php echo $row['user_email']; ?></td>
                                            <td><?php echo $row['acc_status']; ?></td>
                                            <td><?php echo $row['registered_at']->format('Y-m-d H:i:s');; ?></td>
                                            <td class="action">
                                                <a href="javascript:void(0);" class="view" onclick="openForm('<?php echo $row['user_id']; ?>')" style="margin-right: 0.8em;"><i class="fa fa-eye"></i></a>
                                                <a href="javascript:void(0);" class="del" onclick="confirmDelete('<?php echo $row['user_id']; ?>');"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                <?php
                                    $norecord = false;
                                }
                            } else {
                                $cquery = "SELECT COUNT(*) AS ttlrecord FROM [user] WHERE [usertype] = 'Student' AND [acc_status] = 'Approved'";
                                $cstatement = sqlsrv_query($conn, $cquery);
                                $ttlrecord = 0;

                                if ($cstatement) {
                                    $crow = sqlsrv_fetch_array($cstatement);
                                    $ttlrecord = $crow['ttlrecord'];
                                }
                                //calculate the total number of pages
                                $ttlpages = ceil($ttlrecord / $itemsPerPage);

                                $query = "SELECT * FROM [user] WHERE [usertype] = 'Student' AND [acc_status] = 'Approved' ORDER BY [user_id] OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                                $statement = sqlsrv_query($conn, $query);

                                $i = 1;
                                $norecord = true;
                                while ($row = sqlsrv_fetch_array($statement)) {
                                ?>
                                    <tbody>
                                        <tr>
                                            <td class="number"><?php echo $i++; ?></td>
                                            <td><?php echo $row['user_id']; ?></td>
                                            <td><?php echo $row['fullname']; ?></td>
                                            <td><?php echo $row['user_email']; ?></td>
                                            <td><?php echo $row['acc_status']; ?></td>
                                            <td><?php echo $row['registered_at']->format('Y-m-d H:i:s');; ?></td>
                                            <td class="action">
                                                <a href="javascript:void(0);" class="view" onclick="openForm('<?php echo $row['user_id']; ?>')" style="margin-right: 0.8em;"><i class="fa fa-eye"></i></a>
                                                <a href="javascript:void(0);" class="del" onclick="confirmDelete('<?php echo $row['user_id']; ?>');"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                            <?php
                                    $norecord = false;
                                }
                            }

                            if ($norecord) {
                                echo "<tbody><tr><td colspan='7'>No records found.</td></tr></tbody>";
                            } ?>
                        </table>
                    </div>

                    <!-- Pagination - page by page -->
                    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['searchInput']) && !empty($_POST['searchInput'])) { ?>
                        <div class="pagination-container" style="display: none;"></div>
                    <?php } else { ?>
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
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <div class="overlay-bg" id="overlay-bg"></div>

    <div class="user-detail-container" id="user-detail-container"></div>

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
        function searchApproved(event) {
            if (event.key === 'Enter') {
                //prevent form submission (if within a form)
                event.preventDefault();

                const searchInput = document.getElementById('searchInput').value;

                document.getElementById('search-btn').click();
            }
        }

        function openForm(userid) {
            $.ajax({
                type: 'GET',
                url: '../admin/userdetail.php',
                data: {
                    uid: userid
                },
                success: function(response) {
                    $('#user-detail-container').html(response);
                    document.getElementById("user-detail-container").style.display = "block";
                    document.getElementById("overlay-bg").style.display = "block";
                },
                error: function() {
                    alert('Failed to load user details.');
                }
            });
        }

        function closeForm() {
            document.getElementById("user-detail-container").style.display = "none";
            document.getElementById("overlay-bg").style.display = "none";
        }
    </script>
</body>

</html>