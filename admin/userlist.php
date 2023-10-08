<?php
// Set the session timeout to 4 hours (4 hours * 60 minutes * 60 seconds)
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
        function openForm() {
            document.getElementById("new-admin-container").style.display = "block";
        }

        function closeForm() {
            document.getElementById("new-admin-container").style.display = "none";
        }

        document.addEventListener("DOMContentLoaded", function() {
            showTab("user-accepted"); //show "User List" by default

            const tabLinks = document.querySelectorAll(".tablinks");
            tabLinks.forEach(function(link) {
                link.addEventListener("click", function() {
                    const target = this.getAttribute("data-target");
                    showTab(target);
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
            <input type="text" name="searchInput" id="searchInput" class="searchInput" placeholder="Search by name or student ID">
        </div>

        <div class="tabs">
            <button class="tablinks" data-target="user-accepted">User List</button>
            <button class="tablinks" data-target="user-pending">User Pending</button>
        </div>

        <div class="tab-content">
            <div id="user-accepted" class="tabcontent">
                <div class="header">
                    <h3>User List</h3>
                </div>

                <div class="add-admin" onclick="openForm()">
                    <i class="fa fa-plus"></i> Pending
                </div>

                <br />

                <div class="userlist-container">
                    <table id="userlist">
                        <thead>
                            <tr>
                                <th class="number">No.</th>
                                <th><i onclick="openForm()">Student ID</i></th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Registered Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <?php
                        $query = "SELECT * FROM [user] where [usertype] = 'Student' AND [acc_status] = 'Approved'";
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
                                    <td class="action"><a href="./backend/deluserdb.php?userid=<?php echo $row['user_id']; ?>" class="del" onclick="return confirm('Are you sure you want to delete this admin?');"><i class="fa fa-trash"></i></a></td>
                                </tr>
                            </tbody>
                        <?php } ?>
                    </table>
                </div>
            </div>

            <div id="user-pending" class="tabcontent">
                <div class="header">
                    <h3>User Pending</h3>
                </div>

                <div class="add-admin" onclick="openForm()">
                    <i class="fa fa-plus"></i> Pending
                </div>

                <br />

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
                                    <td class="action"><a href="./backend/deluserdb.php?userid=<?php echo $row['user_id']; ?>" class="del" onclick="return confirm('Are you sure you want to delete this admin?');"><i class="fa fa-trash"></i></a></td>
                                </tr>
                            </tbody>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="new-admin-container" id="new-admin-container">
        <form class="new-admin-form" id="new-admin-form" method="post" action="./backend/nadmindb.php" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>STUDENT DETAILS</h3>
            </div>

            <div class="wrap">
                <div class="InputText">
                    <input type="text" name="fname" id="fname" required>
                    <label for="fname">Full Name</label>
                </div>

                <div class="InputText">
                    <input type="email" name="uEmail" id="uEmail" required>
                    <label for="uEmail">Email</label>
                </div>

                <div class="InputText">
                    <input type="text" name="uID" id="uID" required>
                    <label for="uID">Admin ID</label>
                </div>

                <div class="InputText">
                    <input type="password" name="password" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 8 or more characters" required>
                    <label for="password">Password</label>
                </div>

                <div class="new-admin-btn">
                    <input type="submit" name="new-admin" id="new-admin" class="new-admin" value="Register">
                </div>
            </div>
        </form>
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
        // Search Function
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
        // Attach an event listener to the search input field
        document.getElementById("searchInput").addEventListener("keyup", filterTable);
    </script>
</body>

</html>