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

    <title>Administrator List</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openForm() {
            document.getElementById("new-admin-container").style.display = "block";
            document.getElementById("overlay-bg").style.display = "block";
        }

        function closeForm() {
            document.getElementById("new-admin-container").style.display = "none";
            document.getElementById("overlay-bg").style.display = "none";
        }

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
        <div class="header">
            <h3>Administrator List</h3>
        </div>

        <?php
        if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'SuperAdmin') {
        ?>

            <div class="add-admin" onclick="openForm()">
                <i class="fa fa-plus"></i> New Admin
            </div>

        <?php } ?>

        <br />

        <div class="adminlist-container">
            <table>
                <thead>
                    <tr>
                        <th class="number">No.</th>
                        <th>Admin ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                $query = "SELECT * FROM [user] where [usertype] = 'SuperAdmin' OR [usertype] = 'Admin'";
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
                            <td><?php echo $row['registered_at']->format('Y-m-d H:i:s');; ?></td>
                            <td class="action">
                                <a href="javascript:void(0);" class="view" onclick="openDetail('<?php echo $row['user_id']; ?>')"><i class="fa fa-eye"></i></a>
                                <?php
                                if (isset($_SESSION['usertype']) && $_SESSION['usertype'] == 'SuperAdmin') {
                                ?>
                                    <a href="javascript:void(0);" class="del" onclick="confirmDelete('<?php echo $row['user_id']; ?>');" style="margin-left: 0.8em;"><i class="fa fa-trash"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    </tbody>
                <?php } ?>
            </table>
        </div>
    </div>

    <div class="overlay-bg" id="overlay-bg"></div>

    <div class="admin-detail-container" id="admin-detail-container"></div>

    <div id="new-admin-container" class="new-admin-container">
        <form id="new-admin-form" class="new-admin-form" method="post" action="../admin/backend/newadmindb.php" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>NEW ADMIN</h3>
            </div>

            <br />

            <div class="wrap">
                <div class="InputText">
                    <input type="text" name="fname" id="fname" autocomplete="off" required>
                    <label for="fname">Full Name</label>
                </div>

                <div class="InputText">
                    <input type="email" name="uEmail" id="uEmail" autocomplete="off" required>
                    <label for="uEmail">Email</label>
                </div>

                <div class="InputText">
                    <input type="text" name="uID" id="uID" autocomplete="off" required>
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

    <script>
        function openDetail(userid) {
            $.ajax({
                type: 'GET',
                url: '../admin/userdetail.php',
                data: {
                    uid: userid
                },
                success: function(response) {
                    $('#admin-detail-container').html(response);
                    document.getElementById("admin-detail-container").style.display = "block";
                    document.getElementById("overlay-bg").style.display = "block";
                },
                error: function() {
                    alert('Failed to load user details.');
                }
            });
        }

        function closeDetail() {
            document.getElementById("admin-detail-container").style.display = "none";
            document.getElementById("overlay-bg").style.display = "none";
        }
    </script>

    <span>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        }

        unset($_SESSION['message']);
        ?>
    </span>
</body>

</html>