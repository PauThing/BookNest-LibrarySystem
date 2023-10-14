<?php
session_start();
include('../clients/connect.php');

if (isset($_GET['uid'])) {
    $userid = $_GET['uid'];
    $_SESSION['uid'] = $userid;

    $query = "SELECT * FROM [user] WHERE [user_id] = '$userid'";
    $statement = sqlsrv_query($conn, $query);

    //check if the query was successful
    if ($statement === false) {
        die("Query failed: " . print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($statement);
    $usertype = $row['usertype'];
    if ($usertype == "Student") {
?>
        <!DOCTYPE html>
        <html>
        <form class="user-detail-form" id="user-detail-form" method="post" action="../admin/backend/appuserdb.php" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>STUDENT DETAILS</h3>
            </div>

            <div class="wrap">

                <?php
                $query2 = "SELECT * FROM [user] WHERE [user_id] = '$userid'";
                $statement2 = sqlsrv_query($conn, $query2);

                //check if the query was successful
                if ($statement2 === false) {
                    die("Query failed: " . print_r(sqlsrv_errors(), true));
                }

                //fetch and display data from databse
                while ($row2 = sqlsrv_fetch_array($statement2, SQLSRV_FETCH_ASSOC)) {
                    //check if there is image data in the row
                    if ($row2['stu_img']) {
                        //get the image data from the row
                        $imageBinary = $row2['stu_img'];

                        //detect the image format
                        $image = getimagesizefromstring($imageBinary);
                        if ($image !== false) {
                            //determine the MIME type based on the detected image format
                            $mimeType = $image['mime'];
                ?>

                            <div class="show-stu-id">
                                <label for="show-stuid">Student ID Image</label> <br />
                                <div class="center">
                                    <img src="data:<?php echo $mimeType; ?>;base64,<?php echo base64_encode($imageBinary); ?>" name="show-stuid" id="show-stuid" class="show-stuid" />
                                </div>
                            </div>

                    <?php
                        } else {
                            echo "Unable to detect image format.";
                        }
                    } else {
                        echo "No image data found.";
                    }
                    ?>

                    <br />

                    <div class="InputText">
                        <label for="uID">Student ID</label>
                        <input type="text" name="uID" id="uID" value="<?php echo $row2['user_id']; ?>" disabled>
                    </div>

                    <div class="InputText">
                        <label for="fname">Full Name</label>
                        <input type="text" name="fname" id="fname" value="<?php echo $row2['fullname']; ?>" disabled>
                    </div>

                    <div class="InputText">
                        <label for="uEmail">Email</label>
                        <input type="email" name="uEmail" id="uEmail" value="<?php echo $row2['user_email']; ?>" disabled>
                    </div>

                    <?php if ($row2['acc_status'] == "Pending") { ?>
                        <div class="user-detail-btn">
                            <input type="submit" name="user-approve" id="user-approve" class="user-approve" value="Approve">
                            <input type="submit" name="user-reject" id="user-reject" class="user-reject" value="Reject">
                        </div>
                <?php }} ?>
            </div>
        </form>

    <?php } else if ($usertype == "SuperAdmin" || $usertype == "Admin") { ?>

        <form class="admin-detail-form" id="admin-detail-form" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeDetail()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>ADMIN DETAILS</h3>
            </div>

            <div class="wrap">

                <?php
                $query2 = "SELECT * FROM [user] WHERE [user_id] = '$userid'";
                $statement2 = sqlsrv_query($conn, $query2);

                //check if the query was successful
                if ($statement2 === false) {
                    die("Query failed: " . print_r(sqlsrv_errors(), true));
                }

                //fetch and display data from databse
                while ($row2 = sqlsrv_fetch_array($statement2, SQLSRV_FETCH_ASSOC)) {
                ?>

                    <div class="InputText">
                        <label for="uID">Admin ID</label>
                        <input type="text" name="uID" id="uID" value="<?php echo $row2['user_id']; ?>" disabled>
                    </div>

                    <div class="InputText">
                        <label for="fname">Full Name</label>
                        <input type="text" name="fname" id="fname" value="<?php echo $row2['fullname']; ?>" disabled>
                    </div>

                    <div class="InputText">
                        <label for="uEmail">Email</label>
                        <input type="email" name="uEmail" id="uEmail" value="<?php echo $row2['user_email']; ?>" disabled>
                    </div>
                <?php } ?>
            </div>
        </form>

        </html>
<?php }
} ?>