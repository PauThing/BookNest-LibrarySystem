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
        function closeForm() {
            document.getElementById("user-detail-container").style.display = "none";
        }

        document.addEventListener("DOMContentLoaded", function() {
            showTab("user-accepted"); //show "User List" by default

            const tabLinks = document.querySelectorAll(".tablinks");
            tabLinks.forEach(function(link) {
                link.addEventListener("click", function() {
                    const target = this.getAttribute("data-target");
                    showTab(target);

                    //remove "active" class from all tab links
                    tabLinks.forEach(function(tablink) {
                        tablink.classList.remove("active");
                    });

                    //add "active" class to the clicked tab link
                    this.classList.add("active");
                });
            });
        });

        function showTab(tabId) {
            const tabContents = document.querySelectorAll(".tabcontent");
            tabContents.forEach(function(content) {
                content.classList.remove("active");
            });

            const tab = document.getElementById(tabId);
            tab.classList.add("active");

            //smoothly scroll to the tab content
            window.scrollTo({
                top: tab.offsetTop,
                behavior: "smooth"
            });
        }
    </script>
</head>

<body>
    <div class="big-container">
        <div class="search-box">
            <input type="text" name="searchInput" id="searchInput" class="searchInput" placeholder="Search by student name or student ID">
        </div>

        <div class="tab-container">
            <div class="tabs">
                <button class="tablinks active" data-target="user-accepted">User List</button>
                <button class="tablinks" data-target="user-pending">User Pending</button>
            </div>

            <div class="tab-content">
                <div id="user-accepted" class="tabcontent">
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
                            $itemsPerPage = 1;
                            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
                            $offset = ($currentPage - 1) * $itemsPerPage;

                            //check if offset is negative and adjust if necessary
                            if ($offset < 0) {
                                $offset = 0; //reset offset to 0 if it's negative
                            }

                            $query = "SELECT * FROM [user] where [usertype] = 'Student' AND [acc_status] = 'Approved' ORDER BY user_id OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
                            $statement = sqlsrv_query($conn, $query);

                            $i = 1;
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
                                        <td class="action"><a href="../admin/backend/deluserdb.php?userid=<?php echo $row['user_id']; ?>" class="del" onclick="return confirm('Are you sure you want to delete this admin?');"><i class="fa fa-trash"></i></a></td>
                                    </tr>
                                </tbody>
                            <?php } ?>
                        </table>
                    </div>

                    <div class="pagination-container">
                        <ul class="pagination">
                            <?php if ($currentPage > 1) : ?>
                                <li><a href="?page=<?php echo $currentPage - 1; ?>">Previous</a></li>
                            <?php else : ?>
                                <li><span>Previous</span></li>
                            <?php endif;
                            if ($currentPage <= 1) : ?>
                                <li><a href="?page=<?php echo $currentPage + 1; ?>">Next</a></li>
                            <?php else : ?>
                                <li><span>Next</span></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <div id="user-pending" class="tabcontent">
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
                            $query2 = "SELECT * FROM [user] where [usertype] = 'Student' AND [acc_status] = 'Pending'";
                            $statement2 = sqlsrv_query($conn, $query2);

                            $i = 1;
                            while ($row = sqlsrv_fetch_array($statement2)) {
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
                                            <a href="javascript:void(0);" class="view" onclick="openForm('<?php echo $row['user_id']; ?>')"><i class="fa fa-eye"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            <?php } ?>
                        </table>
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
        //search Function
        function filterTable() {
            const input = document.getElementById("searchInput");
            const filter = input.value.toUpperCase();
            const table = document.getElementById("userlist");
            const rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                const idColumn = rows[i].getElementsByTagName("td")[1];
                const nameColumn = rows[i].getElementsByTagName("td")[2];

                if (idColumn || nameColumn) {
                    const idText = idColumn.textContent || idColumn.innerText;
                    const nameText = nameColumn.textContent || nameColumn.innerText;

                    if (idText.toUpperCase().indexOf(filter) > -1 || nameText.toUpperCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
        //attach an event listener to the search input field
        document.getElementById("searchInput").addEventListener("keyup", filterTable);

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
    </script>
</body>

</html>