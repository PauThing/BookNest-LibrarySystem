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
        function openForm() {
            document.getElementById("new-db-container").style.display = "block";
        }

        function closeForm() {
            document.getElementById("new-db-container").style.display = "none";
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
            <h3>Online Database</h3>
        </div>

        <div class="add-db" onclick="openForm()">
            <i class="fa fa-plus"></i> New Database
        </div>

        <div class="onlinedb-container">
            <table id="onlinedb">
                <?php
                $query = "SELECT * FROM [onlinedb]";
                $statement = sqlsrv_query($conn, $query);

                $previousCategory = null;

                while ($row = sqlsrv_fetch_array($statement)) {
                    //check if the current category is different from the previous category
                    if ($row['category'] != $previousCategory) {
                        if ($previousCategory !== null) {
                ?>
            </table>
        <?php } ?>
        <table>
            <thead>
                <tr>
                    <th colspan="2"><?php echo $row['category']; ?></th>
                </tr>
            </thead>
        <?php } ?>
        <tbody>
            <tr>
                <td><a href="<?php echo $row['db_url']; ?>" target="_blank"><?php echo $row['title']; ?></a></td>
                <td class="action">
                    <a href="javascript:void(0);" class="del" onclick="confirmDelete('<?php echo $row['title']; ?>');"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
        </tbody>
    <?php $previousCategory = $row['category'];
                } ?>
        </table>
        </div>
    </div>

    <div id="new-db-container" class="new-db-container">
        <form id="new-db-form" class="new-db-form" method="post" action="../admin/backend/newdbdb.php" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>NEW DATABASE</h3>
            </div>

            <br />

            <div class="wrap">
                <div class="InputText">
                    <input type="text" name="dbtitle" id="dbtitle" autocomplete="off" required>
                    <label for="dbtitle">Database Title</label>
                </div>

                <div class="InputText">
                    <input type="url" name="dburl" id="dburl" pattern="https://.*" autocomplete="off" required />
                    <label for="dburl">URLs</label>
                </div>

                <div class="SelectInput" data-mate-select="active">
                    <label for="dbcat">Database Category</label> <br />
                    <select name="dbcat" id="dbcat" class="dbcat">
                        <option value="">Select an option </option>
                        <option value="Arts">Arts</option>
                        <option value="Business and Communication">Business and Communication</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Hospitality and Tourism">Hospitality and Tourism</option>
                        <option value="Information Technology">Information Technology</option>
                        <option value="Others">Others</option>
                    </select>
                </div>

                <br /><br />

                <div class="new-db-btn">
                    <input type="submit" name="new-db" id="new-db" class="new-db" value="Proceed">
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