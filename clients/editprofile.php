<?php
include('navbar.php');

if (!isset($_SESSION['userid']) || trim($_SESSION['userid'] == '')) {
    header('location: signin.php');
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./styles/profile.css">

    <title>My Profile</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="big-container">
        <div class="profile-container">
            <form class="profile-form" id="profile-form" action="">
                <div class="header">
                    <h2>EDIT MY PROFILE</h2>
                </div>

                <?php
                // SQL Query to retrieve data from a table
                $sql = "SELECT * FROM [user] WHERE [user_id] = '" . $_SESSION['userid'] . "'";

                // Execute the SQL query
                $query = sqlsrv_query($conn, $sql);

                // Check if the query was successful
                if ($query === false) {
                    die("Query failed: " . print_r(sqlsrv_errors(), true));
                }

                // Fetch and display data from the result set
                while ($row = sqlsrv_fetch_array($query, SQLSRV_FETCH_ASSOC)) {
                    // Check if there is image data in the row
                    if ($row['profile_img']) {
                        // Get the image data from the row
                        $imageData = $row['profile_img'];

                        // Detect the image format
                        $imageInfo = getimagesizefromstring($imageData);
                        if ($imageInfo !== false) {
                            // Determine the MIME type based on the detected image format
                            $mimeType = $imageInfo['mime'];
                ?>

                    <div class="wrap">
                        <div class="show-profile-pic">
                            <img src="data:<?php echo $mimeType; ?>;base64,<?php echo base64_encode($imageData); ?>" name="show-profile" id="show-profile" class="show-profile" />
                        </div>

                    <?php
                        } else {
                            // the image format could not be detected
                            echo "Unable to detect image format.";
                        }
                    } else {
                        // there is no image data in the row
                        echo "No image data found.";
                    }
                    ?>

                        <br /><br/>

                        <div class="InputText">
                            <label for="fname">Full Name</label>
                            <input type="text" name="fname" id="fname" value="<?php echo $row['fullname']; ?>">
                        </div>

                        <div class="InputText">
                            <label for="uEmail">Email</label>
                            <input type="email" name="uEmail" id="uEmail" value="<?php echo $row['email']; ?>">
                        </div>

                        <div class="InputText">
                            <label for="uID">User ID</label>
                            <input type="text" name="uID" id="uID" value="<?php echo $row['user_id']; ?>">
                        </div>

                        <br /><br />

                        <div class="editprofile-btn">
                            <input type="submit" name="editprofile" id="editprofile" class="editprofile" value="Edit Profile">
                        </div>
                    </div>
                <?php } ?>
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