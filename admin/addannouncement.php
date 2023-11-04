<?php
//set the session timeout to 8 hours (8 hours * 60 minutes * 60 seconds)
ini_set('session.gc_maxlifetime', 8 * 60 * 60);
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
    <link rel="stylesheet" href="../clients/styles/announcement.css">

    <title>Announcement</title>

    <script src="tinymce/js/tinymce/tinymce.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tinymce.init({
            selector: '#announcement-info',
            plugins: 'searchreplace autoresize image table lists',
        });
    </script>
</head>

<body>
    <div class="big-container">
        <div id="new-announcement-container" class="new-announcement-container">
            <form id="new-announcement-form" class="new-announcement-form" method="post" action="../admin/backend/newannouncementdb.php" enctype="multipart/form-data">
                <div class="header">
                    <h3>New Announcement</h3>
                </div>

                <br />

                <div class="wrap">
                    <div class="InputText">
                        <input type="text" name="anntitle" id="anntitle" autocomplete="off" required>
                        <label for="anntitle">Announcement Title</label>
                    </div>

                    <div class="edit-announcement-text">
                        <textarea name="announcement-info" id="announcement-info"></textarea>
                    </div>
                    
                    <div class="new-announcement-btn">
                        <input type="submit" name="new-announcement" id="new-announcement" class="new-announcement" value="Publish">
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