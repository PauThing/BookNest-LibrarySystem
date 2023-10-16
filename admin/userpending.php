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
    <link rel="stylesheet" href="../clients/styles/userlist.css">

    <title>User List</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="big-container">
        <div class="tab-container">
            <div class="tabs">
                <a href="../admin/userlist.php" <?php if (basename($_SERVER['PHP_SELF']) == 'userlist.php') echo 'class="tablink active"'; ?>>User List</a>
                <a href="../admin/userpending.php" <?php if (basename($_SERVER['PHP_SELF']) == 'userpending.php') echo 'class="tablink active"'; ?>>User Pending</a>
            </div>

            <div class="tab-content">
                <div id="user-pending" class="tabcontent">
                    <!-- Pending List -->
                    <div class="userlist-container">
                        <table id="userpending">
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

                            <tbody>
                                <?php
                                $itemsPerPage2 = 20;
                                $currentPage2 = isset($_GET['page']) ? $_GET['page'] : 1;
                                $offset2 = ($currentPage2 - 1) * $itemsPerPage2;

                                //check if offset is negative and adjust if necessary
                                if ($offset2 < 0) {
                                    $offset2 = 0; //reset offset to 0 if it's negative
                                }

                                $cquery2 = "SELECT COUNT(*) AS ttlrecord FROM [user] WHERE [usertype] = 'Student' AND [acc_status] = 'Pending'";
                                $cstatement2 = sqlsrv_query($conn, $cquery2);
                                $ttlrecord2 = 0;

                                if ($cstatement2) {
                                    $crow2 = sqlsrv_fetch_array($cstatement2);
                                    $ttlrecord2 = $crow2['ttlrecord'];
                                }

                                //calculate the total number of pages
                                $ttlpages2 = ceil($ttlrecord2 / $itemsPerPage2);

                                $query2 = "SELECT * FROM [user] where [usertype] = 'Student' AND [acc_status] = 'Pending' ORDER BY user_id OFFSET $offset2 ROWS FETCH NEXT $itemsPerPage2 ROWS ONLY";
                                $statement2 = sqlsrv_query($conn, $query2);

                                $i = 1;
                                while ($row2 = sqlsrv_fetch_array($statement2)) {
                                ?>
                                    <tr>
                                        <td class="number"><?php echo $i++; ?></td>
                                        <td><?php echo $row2['user_id']; ?></td>
                                        <td><?php echo $row2['fullname']; ?></td>
                                        <td><?php echo $row2['user_email']; ?></td>
                                        <td><?php echo $row2['acc_status']; ?></td>
                                        <td><?php echo $row2['registered_at']->format('Y-m-d H:i:s');; ?></td>
                                        <td class="action">
                                            <a href="javascript:void(0);" class="view" onclick="openForm('<?php echo $row2['user_id']; ?>')"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination - page by page -->
                    <div class="pagination-container">
                        <ul class="pagination">
                            <?php
                            if ($currentPage2 > 1) {
                                echo "<li><a href='?page=" . ($currentPage2 - 1) . "'>Previous</a></li>";
                            } else {
                                echo "<li><span>Previous</span></li>";
                            }

                            if ($currentPage2 < $ttlpages2) {
                                echo "<li><a href='?page=" . ($currentPage2 + 1) . "'>Next</a></li>";
                            } else {
                                echo "<li><span>Next</span></li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                },
                error: function() {
                    alert('Failed to load user details.');
                }
            });
        }

        function closeForm() {
            document.getElementById("user-detail-container").style.display = "none";
        }
    </script>
</body>

</html>