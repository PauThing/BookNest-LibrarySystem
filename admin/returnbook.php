<?php
session_start();
include('../clients/connect.php');

if (isset($_GET['bisbn']) && isset($_GET['uid'])) {
    $bisbn = $_GET['bisbn'];
    $uid = $_GET['uid'];
?>
    <!DOCTYPE html>
    <html>
    <form class="returnb-form" id="returnb-form" method="post" action="../admin/backend/returnbookdb.php" enctype="multipart/form-data">
        <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>

        <div class="book">
            <div class="book-detail">
                <?php
                //set time zone
                date_default_timezone_set('Asia/Kuala_Lumpur');
                $currD = date('Y-m-d');

                $status = "Returned";

                $query = "SELECT
                        bh.*,
                        bc.*,
                        b.*
                FROM [borrowinghistory] bh
                LEFT JOIN [book] b ON bh.ISBN = b.ISBN
                LEFT JOIN [bookcatalog] bc ON bh.ISBN = bc.ISBN
                WHERE bh.ISBN = ? AND bh.user_id = ? AND [status] = 'On Loan'";
                $array = [$bisbn, $uid];
                $statement = sqlsrv_query($conn, $query, $array);

                while ($row = sqlsrv_fetch_array($statement)) {
                    $isbn = $row['ISBN'];
                    $bktitle = $row['book_title'];
                    $latefee = $row['late_fees'];

                    $dueDate = $row['due_at'];
                    $dueD = $dueDate->format('Y-m-d');
                ?>
                    <div class="coverimage">
                        <?php
                        //check if there is image data in the row
                        if ($row['cover_img']) {
                            //get the image data from the row
                            $imageBinary = $row['cover_img'];

                            //detect the image format
                            $image = getimagesizefromstring($imageBinary);
                            if ($image !== false) {
                                //determine the MIME type based on the detected image format
                                $mimeType = $image['mime'];
                        ?>
                                <img src="data:<?php echo $mimeType; ?>;base64,<?php echo base64_encode($imageBinary); ?>" class="cover-img">

                        <?php
                            } else {
                                echo "Unable to detect image format.";
                            }
                        } else {
                            echo "<img src='../clients/assets/cover_unavailable.png' class='cover-img'>";
                        }
                        ?>
                    </div>

                    <div class="detail">
                        <h3><?php echo $bktitle; ?></h3>

                        <label for="isbn">ISBN </label>
                        <input type="text" name="bisbn" id="bisbn" value="<?php echo $bisbn; ?>" readonly>
                        <br /><br />

                        <label for="duedate">Due Date: </label>
                        <input type="text" name="duedate" id="duedate" value="<?php echo $dueD; ?>" readonly>
                        <br />

                        <label for="returndate">Return Date: </label>
                        <input type="text" name="returndate" id="returndate" value="<?php echo $currD; ?>" readonly>
                        <br />

                        <label for="latefee">Fee Pending: RM </label>
                        <input type="text" name="latefee" id="latefee" value="<?php echo number_format($latefee, 2); ?>" readonly>
                        <br />

                        <label for="uid">Student ID: </label>
                        <input type="text" name="uid" id="uid" value="<?php echo $uid; ?>" readonly>
                        <br />

                        <?php
                        $query2 = "SELECT * FROM [user] WHERE [user_id] = ?";
                        $array2 = [$uid];
                        $statement2 = sqlsrv_query($conn, $query2, $array2);
                        $row2 = sqlsrv_fetch_array($statement2);
                        $name = $row2['fullname'];
                        ?>
                        <label for="uname">Student Name: </label>
                        <input type="text" name="uname" id="uname" class="uname" value="<?php echo $name; ?>" readonly>
                    </div>
                <?php
                } ?>
            </div>

            <br />

            <div class="returnb-action">
                <input type="submit" name="returnb" id="returnb" class="returnb" value="Return">
            </div>
        </div>
    </form>

    </html>
<?php } ?>