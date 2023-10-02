<?php
// Set the session timeout to 4 hours (4 hours * 60 minutes * 60 seconds)
ini_set('session.gc_maxlifetime', 4 * 60 * 60);
session_start();

include('../clients/navbar.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./styles/sign.css">

    <title>Sign In</title>
</head>

<body>
    <div class="big-container">
        <div class="signin-container">
            <form class="signin-form" id="signin-form" method="post" action="./backend/signindb.php" enctype="multipart/form-data">
                <div class="header">
                    <h2>SIGN IN</h2>
                </div>

                <div class="wrap">
                    <div class="InputText">
                        <input type="text" name="uID" id="uID" required>
                        <label>User ID</label>
                    </div>

                    <div class="InputText">
                        <input type="password" name="password" id="password" required>
                        <label>Password</label>
                    </div>

                    <div class="signin-btn">
                        <input type="submit" name="signin" id="signin" class="signin" value="Sign In">
                    </div>

                    <div class="not-register">
                        Not Yet Register? <a href="./signup.php">Register Here!</a>
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