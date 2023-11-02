<?php
session_start();
include('../clients/connect.php');

if (isset($_GET['isbn']) && isset($_GET['currURL'])) {
    $bisbn = $_GET['isbn'];
    $url = $_GET['currURL'];
    $userid = $_SESSION['userid'];
?>
    <!DOCTYPE html>
    <html>
    <form class="book-borrow-form" id="book-borrow-form" method="post" action="../clients/backend/bookborrowdb.php?cURL=<?php echo $url; ?>" enctype="multipart/form-data">
        <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>

        <div class="book">
            <div class="book-detail">
                <?php
                $query = "SELECT
                        bc.*,
                        b.*
                FROM [bookcatalog] bc
                LEFT JOIN [book] b ON bc.ISBN = b.ISBN
                WHERE bc.ISBN = ? AND b.ISBN = ?";
                $array = [$bisbn, $bisbn];
                $statement = sqlsrv_query($conn, $query, $array);

                $currDate = date('Y-m-d');
                $dueDate = date('Y-m-d', strtotime('+7 day', strtotime($currDate)));

                while ($row = sqlsrv_fetch_array($statement)) {
                    $isbn = $row['ISBN'];
                    $bktitle = $row['book_title'];
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
                        <h3><?php echo $row['book_title']; ?></h3>

                        <label for="isbn">ISBN </label>
                        <input type="text" name="bisbn" id="bisbn" value="<?php echo $bisbn; ?>" readonly>
                        <br /><br />

                        <label for="brw-date">Borrow Date: </label>
                        <input type="text" name="brwdate" id="brwdate" value="<?php echo $currDate; ?>" readonly>
                        <br />

                        <label for="due-date">Due Date: </label>
                        <input type="text" name="duedate" id="duedate" value="<?php echo $dueDate; ?>" readonly>
                        <br />

                        <label for="uid">Student ID: </label>
                        <input type="text" name="uid" id="uid" value="<?php echo $userid; ?>" readonly>
                        <br />

                        <?php
                        $query2 = "SELECT * FROM [user] WHERE [user_id] = ?";
                        $array2 = [$userid];
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

            <div class="borrow-action">
                <input type="submit" name="brwconfirm" id="brwconfirm" class="brwconfirm" value="Confirm">
            </div>
        </div>
    </form>

    </html>
<?php } ?>