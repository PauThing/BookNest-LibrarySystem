<?php
session_start();
include('../clients/connect.php');

if (isset($_GET['bisbn']) && isset($_GET['uid']) && isset($_GET['cURL'])) {
    $bisbn = $_GET['bisbn'];
    $uid = $_GET['uid'];
    $url = $_GET['cURL'];
?>
    <!DOCTYPE html>
    <html>
    <form class="renew-form" id="renew-form" method="post" action="../clients/backend/renewbookdb.php?cURL=<?php echo $url; ?>" enctype="multipart/form-data">
        <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>

        <div class="book">
            <div class="book-detail">
                <?php
                //set time zone
                date_default_timezone_set('Asia/Kuala_Lumpur');

                $query = "SELECT
                        bh.*,
                        bc.*,
                        b.*
                FROM [borrowinghistory] bh
                LEFT JOIN [book] b ON bh.ISBN = b.ISBN
                LEFT JOIN [bookcatalog] bc ON bh.ISBN = bc.ISBN
                WHERE bh.ISBN = ? AND bh.user_id = ? AND bh.status = 'On Loan'";
                $array = [$bisbn, $uid];
                $statement = sqlsrv_query($conn, $query, $array);

                while ($row = sqlsrv_fetch_array($statement)) {
                    $bktitle = $row['book_title'];

                    $brwDate = $row['borrow_at'];
                    $brwD = $brwDate->format('Y-m-d');

                    $dueDate = $row['due_at'];
                    $dueD = $dueDate->format('Y-m-d');

                    $renewD = date('Y-m-d', strtotime('+7 day', strtotime($dueD)));
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

                        <label for="bisbn">ISBN </label>
                        <input type="text" name="bisbn" id="bisbn" value="<?php echo $bisbn; ?>" readonly>
                        <br /><br />

                        <label for="newdate">Renew Date: </label>
                        <input type="text" name="newdate" id="newdate" value="<?php echo $dueD; ?>" readonly>
                        <br />

                        <label for="brwdate">Borrow Date: </label>
                        <input type="text" name="brwdate" id="brwdate" value="<?php echo $brwD; ?>" readonly>
                        <br />

                        <label for="renewdate">Renewed Due Date: </label>
                        <input type="text" name="renewdate" id="renewdate" value="<?php echo $renewD; ?>" readonly>
                        <br />

                        <label for="userid">Student ID: </label>
                        <input type="text" name="userid" id="userid" value="<?php echo $uid; ?>" readonly>
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

            <div class="renew-action">
                <input type="submit" name="renew" id="renew" class="renew" value="Renew">
            </div>
        </div>
    </form>

    </html>
<?php } ?>