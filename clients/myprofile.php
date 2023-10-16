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
    <link rel="stylesheet" href="../clients/styles/profile.css">

    <title>My Profile</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="big-container">
        <div class="profile-container">
            <form class="profile-form" id="profile-form" method="post" action="../clients/editprofile.php">
                <div class="header">
                    <h2>MY PROFILE</h2>
                </div>

                <div class="wrap">

                    <?php
                    $query = "SELECT * FROM [user] WHERE [user_id] = '" . $_SESSION['userid'] . "'";
                    $statement = sqlsrv_query($conn, $query);

                    //check if the query was successful
                    if ($statement === false) {
                        die("Query failed: " . print_r(sqlsrv_errors(), true));
                    }

                    //fetch and display data from databse
                    while ($row = sqlsrv_fetch_array($statement, SQLSRV_FETCH_ASSOC)) {
                        //check if there is image data in the row
                        if ($row['profile_img']) {
                            //get the image data from the row
                            $imageBinary = $row['profile_img'];

                            //detect the image format
                            $image = getimagesizefromstring($imageBinary);
                            if ($image !== false) {
                                //determine the MIME type based on the detected image format
                                $mimeType = $image['mime'];
                    ?>
                                <div class="show-profile-pic">
                                    <img src="data:<?php echo $mimeType; ?>;base64,<?php echo base64_encode($imageBinary); ?>" name="show-profile" id="show-profile" class="show-profile" />
                                </div>

                        <?php
                            } else {
                                echo "Unable to detect image format.";
                            }
                        } else {
                            echo "No image data found.";
                        }
                        ?>

                        <br /><br />

                        <div class="InputText">
                            <label for="fname">Full Name</label>
                            <input type="text" name="fname" id="fname" value="<?php echo $row['fullname']; ?>" disabled>
                        </div>

                        <div class="InputText">
                            <label for="uEmail">Email</label>
                            <input type="email" name="uEmail" id="uEmail" value="<?php echo $row['user_email']; ?>" disabled>
                        </div>

                        <div class="InputText">
                            <label for="uID">User ID</label>
                            <input type="text" name="uID" id="uID" value="<?php echo $row['user_id']; ?>" disabled>
                        </div>

                        <br /><br />

                        <div class="editprofile-btn">
                            <button type="submit" name="editprofile" id="editprofile" class="editprofile" value="Edit Profile"><i class="fa fa-edit"></i> Edit Profile</button>
                        </div>
                    <?php } ?>

                    <div class="chgpass-btn">
                        <a href="../clients/changepassw.php" name="chgpass" id="chgpass" class="chgpass"><i class="fas fa-unlock-alt"></i> Change Password</a>
                    </div>
                </div>
            </form>
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