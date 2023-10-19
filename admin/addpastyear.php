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

    <title>Past Year Exam Paper</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateDataText(input) {
            //get the file name from the input field
            var fileName = input.files && input.files.length > 0 ? input.files[0].name : input.value;
            
            //update the data-text attribute of the parent element
            $(input).closest(".file-upload-wrapper").attr("data-text", fileName);
        }
    </script>
</head>

<body>
    <div class="big-container">
        <?php $pg = $_GET['programme']; ?>

        <div id="new-exampaper-container" class="new-exampaper-container">
            <form id="new-exampaper-form" class="new-exampaper-form" method="post" action="../admin/backend/newexamppdb.php?programme=<?php echo $pg; ?>" enctype="multipart/form-data">
                <div class="header">
                    <h3><?php echo $pg; ?> • New Exam Paper</h3>
                </div>

                <br />

                <div class="wrap">
                    <div class="InputText">
                        <input type="month" name="date" id="date" class="date" required>
                        <label for="date">Semester</label>
                    </div>

                    <div class="InputFile">
                        <label for="file-upload-field">Folder Upload</label>
                        <br />
                        <div class="file-upload-wrapper" data-text="Select multiple PDF files">
                            <input type="file" name="file-upload-field[]" id="file-upload-field" class="file-upload-field" accept=".pdf" multiple required onchange="updateDataText(this)">
                        </div>
                    </div>

                    <br /><br />

                    <div class="new-exampaper-btn">
                        <input type="submit" name="new-exampaper" id="new-exampaper" class="new-exampaper" value="Upload">
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