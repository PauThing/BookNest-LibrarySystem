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
    <link rel = "stylesheet" href = "https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../clients/styles/userlist.css">

    <title>Administrator List</title>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>Administrator List</h3>
        </div>

        <div class="add-admin">
        <a href="deladmindb.php?id=<?php echo $row['user_id'];?>"><span class = "fa fa-plus"></span> New Admin</a>
        </div>

        <div class="adminlist-container">
            <table>
                <thead>
                    <tr>
                        <th class="number">No.</th>
                        <th>User ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                    //$query = "SELECT * FROM [user] where mtitle like '%$s%' or subID like '%$s%'";
                    $sql = "SELECT * FROM [user] where [usertype] = 'Admin'";
                    $query = sqlsrv_query($conn, $sql);

					$i = 1;
					while($row = sqlsrv_fetch_array($query))
					{
				?>
                <tbody>
                    <tr>
                        <td class="number"><?php echo $i++;?></td>
                        <td><?php echo $row['user_id'];?></td>
                        <td><?php echo $row['fullname'];?></td>
                        <td><?php echo $row['user_email'];?></td>
                        <td><?php echo $row['registered_at']->format('Y-m-d H:i:s');;?></td>
                        <td class="action"><a href="deladmindb.php?id=<?php echo $row['user_id'];?>" onclick="return confirm('Are you sure you want to delete this admin?');"><span class = "fa fa-trash"></span></a></td>
                    </tr>
                </tbody>
                <?php } ?>
            </table>
        </div>
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