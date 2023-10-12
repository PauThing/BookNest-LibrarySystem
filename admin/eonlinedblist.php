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
    <link rel="stylesheet" href="../clients/styles/resources.css">

    <title>Online Database</title>

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
        <div class="header">
            <h3>Online Database</h3>
        </div>

        <div class="onlinedb-container">
            <table id="onlinedb">
                <?php
                $query = "SELECT * FROM [onlinedb]";
                $statement = sqlsrv_query($conn, $query);

                while ($row = sqlsrv_fetch_array($statement)) {
                ?>
                    <thead>
                        <tr>
                            <th><?php echo $row['category']; ?></th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td><a href="<?php echo $row['db_url']; ?>" target="_blank"><?php echo $row['title']; ?></a></td>
                            <td class="action">
                                <a href="javascript:void(0);" class="del" onclick="confirmDelete('<?php echo $row['title']; ?>');"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                <?php } ?>
            </table>
        </div>
    </div>

    <div id="new-admin-container" class="new-admin-container">
        <form id="new-admin-form" class="new-admin-form" method="post" action="../admin/backend/newadmindb.php" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>NEW ADMIN</h3>
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