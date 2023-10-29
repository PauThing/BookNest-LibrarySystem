<?php
session_start();
include('../clients/connect.php');

if (isset($_GET['isbn'])) {
    $bisbn = $_GET['isbn'];
    $currentURL = $_GET['cURL'];
?>
    <!DOCTYPE html>
    <html>
    <form class="edit-book-form" id="edit-book-form" method="post" action="../admin/backend/ebookdetaildb.php?bisbn=<?php echo $bisbn; ?>&currentURL=<?php echo $currentURL; ?>" enctype="multipart/form-data">
        <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>
        <div class="header">
            <h3>EDIT BOOK DETAILS</h3>
        </div>

        <div class="wrap">

            <?php
            $query = "SELECT 
                        bc.*,
                        b.* 
                    FROM [bookcatalog] bc
                    LEFT JOIN [book] b ON bc.ISBN = b.ISBN 
                    WHERE bc.[ISBN] = ?";
            $array = [$bisbn];
            $statement = sqlsrv_query($conn, $query, $array);

            //check if the query was successful
            if ($statement === false) {
                die("Query failed: " . print_r(sqlsrv_errors(), true));
            }

            //fetch and display data from databse
            while ($row = sqlsrv_fetch_array($statement)) {
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

                        <div class="show-book">
                            <div class="center">
                                <img src="data:<?php echo $mimeType; ?>;base64,<?php echo base64_encode($imageBinary); ?>" name="show-bookd" id="show-bookd" class="show-bookd" />
                            </div>
                        </div>

                <?php
                    } else {
                        echo "Unable to detect image format.";
                    }
                } else {
                    echo "<div class='show-book'><div class='center'>
                        <img src='../clients/assets/cover_unavailable.png' class='cover-img'>
                        </div></div>";
                }
                ?>

                <br />

                <div class="InputText">
                    <label for="bisbn">ISBN</label>
                    <input type="number" name="bisbn" id="bisbn" class="bisbn" value="<?php echo $row['ISBN']; ?>" disabled>
                </div>

                <div class="InputText">
                    <label for="btitle">Book Title</label>
                    <input type="text" name="btitle" id="btitle" value="<?php echo $row['book_title']; ?>" disabled>
                </div>

                <div class="InputText">
                    <label for="author">Author</label>
                    <input type="text" name="author" id="author" value="<?php echo $row['author']; ?>" autocomplete="off" required>
                </div>

                <div class="InputText">
                    <label for="publication">Publication</label>
                    <input type="text" name="publication" id="publication" value="<?php echo $row['publication']; ?>" autocomplete="off" required>
                </div>

                <div class="InputText">
                    <label for="pyear">Publication Year</label>
                    <input type="number" name="pyear" id="pyear" min="1900" max="2900" value="<?php echo $row['publication_year']; ?>" autocomplete="off" required>
                </div>

                <div class="SelectInput" data-mate-select="active">
                    <label for="bcat">Book Category</label> <br />
                    <select name="bcat" id="bcat" class="bcat">
                        <option value="<?php echo $row['category']; ?>"><?php echo $row['category']; ?></option>
                        <option value="Computer Science and Information">Computer Science and Information</option>
                        <option value="Philosophy and Psychology">Philosophy and Psychology</option>
                        <option value="Science and Technology">Science and Technology</option>
                        <option value="Literature">Literature</option>
                        <option value="History and Geography">History and Geography</option>
                        <option value="Business and Economics">Business and Economics</option>
                    </select>
                </div>

                <br />

                <div class="InputText">
                    <label for="bamount">Amount of Book</label>
                    <input type="number" name="bamount" id="bamount" min="1" value="<?php echo $row['total_qty']; ?>" autocomplete="off" required>
                </div>

                <div class="edit-book-btn">
                    <input type="submit" name="edit-book" id="edit-book" class="edit-book" value="Update">
                </div>
            <?php
            } ?>
        </div>
    </form>

    </html>
<?php } ?>