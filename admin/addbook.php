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
    <link rel="stylesheet" href="../clients/styles/bookcatalog.css">

    <title>New Book</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateDataText(input) {
            //get the file name from the input field
            var fileName = input.value.replace(/.*(\/|\\)/, '');

            //update the data-text attribute of the parent element
            $(input).closest(".file-upload-wrapper").attr("data-text", fileName);
        }
    </script>
</head>

<body>
    <div class="big-container">
        <div id="new-book-container" class="new-book-container">
            <form id="new-book-form" class="new-book-form" method="post" action="../admin/backend/newbookdb.php" enctype="multipart/form-data">
                <div class="header">
                    <h3>New Book</h3>
                </div>

                <br />

                <div class="wrap">
                    <div class="InputText">
                        <input type="number" name="bisbn" id="bisbn" class="bisbn" autocomplete="off" required>
                        <label for="bisbn">ISBN</label>
                    </div>

                    <div class="InputText">
                        <input type="text" name="btitle" id="btitle" autocomplete="off" required>
                        <label for="btitle">Book Title</label>
                    </div>

                    <div class="InputText">
                        <input type="text" name="author" id="author" autocomplete="off">
                        <label for="author">Author</label>
                    </div>

                    <div class="InputText">
                        <input type="text" name="publication" id="publication" autocomplete="off" required>
                        <label for="publication">Publication</label>
                    </div>

                    <div class="InputText">
                        <input type="number" name="pyear" id="pyear" min="1900" max="2900" autocomplete="off" required>
                        <label for="pyear">Publication Year</label>
                    </div>

                    <div class="SelectInput" data-mate-select="active">
                        <label for="bcat">Book Category</label> <br />
                        <select name="bcat" id="bcat" class="bcat" required>
                            <option value="">Select a category </option>
                            <option value="Computer Science and Information">Computer Science and Information</option>
                            <option value="Philosophy and Psychology">Philosophy and Psychology</option>
                            <option value="Science and Technology">Science and Technology</option>
                            <option value="Literature">Literature</option>
                            <option value="History and Geography">History and Geography</option>
                            <option value="Business and Economics">Business and Economics</option>
                        </select>
                    </div>

                    <br /><br />

                    <div class="InputText">
                        <input type="number" name="blocation" id="blocation" min="1" max="18" autocomplete="off" required>
                        <label for="blocation">Location - Rak ?</label>
                    </div>

                    <div class="InputText">
                        <input type="number" name="bamount" id="bamount" min="1" autocomplete="off" required>
                        <label for="bamount">Amount of Book</label>
                    </div>

                    <div class="InputFile">
                        <label for="file-upload-field">Cover Image</label>
                        <br />
                        <div class="file-upload-wrapper" data-text="Choose File (PNG, JPG, JPEG)">
                            <input type="file" name="file-upload-field" id="file-upload-field" class="file-upload-field" accept="image/" onchange="updateDataText(this)">
                        </div>
                    </div>

                    <br /><br />

                    <div class="new-book-btn">
                        <input type="submit" name="new-book" id="new-book" class="new-book" value="Save">
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