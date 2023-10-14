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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            //set the active tab
            function setActive(tabId) {
                const tabLinks = document.querySelectorAll(".tablinks");
                tabLinks.forEach(function(link) {
                    link.classList.remove("active");
                    if (link.getAttribute("data-target") === tabId) {
                        link.classList.add("active");
                    }
                });

                const tabContents = document.querySelectorAll(".tabcontent");
                tabContents.forEach(function(content) {
                    content.classList.remove("active");
                });

                const tab = document.getElementById(tabId);
                tab.classList.add("active");

                window.scrollTo({
                    top: tab.offsetTop,
                    behavior: "smooth"
                });
            }

            //check if the active tab is stored in local storage
            const storedTab = localStorage.getItem("activeTab");

            if (storedTab) {
                setActive(storedTab);
            } else {
                setActive("user-accepted"); //set as default tab if none is stored
            }

            const tabLinks = document.querySelectorAll(".tablinks");
            tabLinks.forEach(function(link) {
                link.addEventListener("click", function() {
                    const target = this.getAttribute("data-target");
                    setActive(target);

                    //store the active tab in local storage
                    localStorage.setItem("activeTab", target);
                });
            });
        });

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
                <button id="accepted" class="tablinks active" data-target="user-accepted">User List</button>
                <button id="pending" class="tablinks" data-target="user-pending">User Pending</button>
            </div>

            <div class="tab-content">
                <div id="user-accepted" class="tabcontent">
                    <form method="post">
                        <div class="search-box">
                            <input type="text" name="searchInput" id="searchInput" class="searchInput" placeholder="Search by student name or student ID" onkeyup="searchApproved(event)">
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

                            <tbody>
                                <?php
                                if (isset($_POST['searchInput'])) {
                                    $search = $_POST['searchInput'];

                                    $itemsPerPage = 1;
                                    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                                    $offset = ($currentPage - 1) * $itemsPerPage;

                                    //check if offset is negative and adjust if necessary
                                    if ($offset < 0) {
                                        $offset = 0; //reset offset to 0 if it's negative
                                    }

                                    $cquery = "SELECT COUNT(*) AS ttlrecord FROM [user] WHERE [usertype] = 'Student' AND [acc_status] = 'Approved' AND ([user_id] LIKE ? OR [fullname] LIKE ?)";
                                    $carray = ["%$search%", "%$search%"];
                                    $cstatement = sqlsrv_query($conn, $cquery, $carray);
                                    $ttlrecord = 0;

                                    if ($cstatement) {
                                        $crow = sqlsrv_fetch_array($cstatement);
                                        $ttlrecord = $crow['ttlrecord'];
                                    }

                                    //calculate the total number of pages
                                    $ttlpages = ceil($ttlrecord / $itemsPerPage);

                                    if ($currentPage < 1) {
                                        $currentPage = 1;
                                    } elseif ($currentPage > $ttlpages) {
                                        $currentPage = $ttlpages;
                                    }

                                    $query = "SELECT * FROM [user] where [usertype] = 'Student' AND [acc_status] = 'Approved' AND ([user_id] LIKE ? OR [fullname] LIKE ?) ORDER BY user_id OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                                    $array = ["%$search%", "%$search%"];
                                    $statement = sqlsrv_query($conn, $query, $array);

                                    $i = 1;
                                    $norecord = true;
                                    while ($row = sqlsrv_fetch_array($statement)) {
                                ?>

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
                                    <?php
                                        $norecord = false;
                                    }

                                    if ($norecord) {
                                        echo "<td colspan='7'>No records found.</td>";
                                    }
                                } else {
                                    $itemsPerPage = 1;
                                    $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                                    $offset = ($currentPage - 1) * $itemsPerPage;

                                    //check if offset is negative and adjust if necessary
                                    if ($offset < 0) {
                                        $offset = 0; //reset offset to 0 if it's negative
                                    }

                                    $cquery = "SELECT COUNT(*) AS ttlrecord FROM [user] WHERE [usertype] = 'Student' AND [acc_status] = 'Approved'";
                                    $cstatement = sqlsrv_query($conn, $cquery);
                                    $ttlrecord = 0;

                                    if ($cstatement) {
                                        $crow = sqlsrv_fetch_array($cstatement);
                                        $ttlrecord = $crow['ttlrecord'];
                                    }

                                    //calculate the total number of pages
                                    $ttlpages = ceil($ttlrecord / $itemsPerPage);

                                    $query = "SELECT * FROM [user] where [usertype] = 'Student' AND [acc_status] = 'Approved' ORDER BY user_id OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                                    $statement = sqlsrv_query($conn, $query);

                                    $i = 1;
                                    while ($row = sqlsrv_fetch_array($statement)) {
                                    ?>
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
                                <?php }
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination - page by page -->
                    <div class="pagination-container">
                        <ul class="pagination">
                            <?php if ($currentPage > 1) : ?>
                                <li><a href="?page=<?php echo $currentPage - 1 . (isset($_POST['searchInput']) ? '&searchInput=' . $_POST['searchInput'] : ''); ?>">Previous</a></li>
                            <?php else : ?>
                                <li><span>Previous</span></li>
                            <?php endif;
                            if ($currentPage < $ttlpages) : ?>
                                <li><a href="?page=<?php echo $currentPage + 1 . (isset($_POST['searchInput']) ? '&searchInput=' . $_POST['searchInput'] : ''); ?>">Next</a></li>
                            <?php else : ?>
                                <li><span>Next</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Pending List -->
                <div id="user-pending" class="tabcontent">
                    <form method="post">
                        <div class="search-box">
                            <input type="text" name="searchInput2" id="searchInput2" class="searchInput2" placeholder="Search by student name or student ID" onkeyup="searchPending(event)">
                            <button class="search2-btn" name="search2-btn" style="display: none;"></button>
                        </div>
                    </form>

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
                                if (isset($_POST['searchInput2'])) {
                                    $search2 = $_POST['searchInput2'];

                                    $itemsPerPage2 = 1;
                                    $currentPage2 = isset($_GET['page']) ? $_GET['page'] : 1;
                                    $offset2 = ($currentPage2 - 1) * $itemsPerPage2;

                                    //check if offset is negative and adjust if necessary
                                    if ($offset2 < 0) {
                                        $offset2 = 0; //reset offset to 0 if it's negative
                                    }

                                    $cquery2 = "SELECT COUNT(*) AS ttlrecord2 FROM [user] WHERE [usertype] = 'Student' AND [acc_status] = 'Pending' AND ([user_id] LIKE ? OR [fullname] LIKE ?)";
                                    $carray2 = ["%$search2%", "%$search2%"];
                                    $cstatement2 = sqlsrv_query($conn, $cquery2, $carray2);
                                    $ttlrecord2 = 0;

                                    if ($cstatement2) {
                                        $crow2 = sqlsrv_fetch_array($cstatement2);
                                        $ttlrecord2 = $crow2['ttlrecord2'];
                                    }

                                    //calculate the total number of pages
                                    $ttlpages2 = ceil($ttlrecord2 / $itemsPerPage2);

                                    $query2 = "SELECT * FROM [user] where [usertype] = 'Student' AND [acc_status] = 'Pending' AND ([user_id] LIKE ? OR [fullname] LIKE ?) ORDER BY user_id OFFSET $offset2 ROWS FETCH NEXT $itemsPerPage2 ROWS ONLY";
                                    $array2 = ["%$search2%", "%$search2%"];
                                    $statement2 = sqlsrv_query($conn, $query2, $array2);

                                    $i = 1;
                                    $norecord = true;
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
                                                <a href="javascript:void(0);" class="view" onclick="openForm('<?php echo $row2['user_id']; ?>')" style="margin-right: 0.8em;"><i class="fa fa-eye"></i></a>
                                                <a href="javascript:void(0);" class="del" onclick="confirmDelete('<?php echo $row2['user_id']; ?>');"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        $norecord = false;
                                    }

                                    if ($norecord) {
                                        echo "<td colspan='7'>No records found.</td>";
                                    }
                                } else {
                                    $itemsPerPage2 = 1;
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
                                <?php }
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination - page by page -->
                    <div class="pagination-container">
                        <ul class="pagination">
                            <?php if ($currentPage2 > 1) : ?>
                                <li><a href="?page=<?php echo $currentPage2 - 1 . (isset($_POST['searchInput2']) ? '&searchInput2=' . $_POST['searchInput2'] : ''); ?>">Previous</a></li>
                            <?php else : ?>
                                <li><span>Previous</span></li>
                            <?php endif;
                            if ($currentPage2 < $ttlpages2) : ?>
                                <li><a href="?page=<?php echo $currentPage2 + 1 . (isset($_POST['searchInput2']) ? '&searchInput2=' . $_POST['searchInput2'] : ''); ?>">Next</a></li>
                            <?php else : ?>
                                <li><span>Next</span></li>
                            <?php endif; ?>
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
        //search function for approved list
        function searchApproved(event) {
            if (event.key === 'Enter') {
                //prevent form submission (if within a form)
                event.preventDefault();
                document.getElementById('search-btn').click();
            }
        }

        //search function for pending list
        function searchPending(event) {
            if (event.key === 'Enter') {
                //prevent form submission (if within a form)
                event.preventDefault();
                document.getElementById('search2-btn').click();
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