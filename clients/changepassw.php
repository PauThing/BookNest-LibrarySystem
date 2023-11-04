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
    <link rel="stylesheet" href="../clients/styles/changepassw.css">

    <title>Change Password</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showPassword() {
            var x = document.getElementById("cpass");
            var y = document.getElementById("npass");
            var z = document.getElementById("cfpass");

            if (x.type === "password" && y.type === "password" && z.type === "password") {
                x.type = "text";
                y.type = "text";
                z.type = "text";
            } else {
                x.type = "password";
                y.type = "password";
                z.type = "password";
            }
        }
    </script>
</head>

<body>
    <div class="big-container">
        <div class="chgpass-container">
            <form class="chgpass-form" id="chgpass-form" method="post" action="../clients/backend/changepasswdb.php">
                <div class="header">
                    <h3>CHANGE PASSWORD</h3>
                </div>

                <br />

                <div class="wrap">
                    <div class="InputText">
                        <input type="password" name="cpass" id="cpass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{10,}$" title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 10 or more characters" required>
                        <label for="cpass">Current Password</label>
                    </div>

                    <br /><br />

                    <div class="InputText">
                        <input type="password" name="npass" id="npass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{10,}$" title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 10 or more characters" required>
                        <label for="npass">New Password</label>
                    </div>

                    <br /><br />

                    <div class="InputText">
                        <input type="password" name="cfpass" id="cfpass" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{10,}$" title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 10 or more characters" required>
                        <label for="cfpass">Confirm Password</label>
                    </div>
                    
                    <br />

                    <div class="ShowPass">
                        <input type="checkbox" name="showpass" id="showpass" class="showpass" onclick="showPassword()"><span> Show Password</span>
                    </div>

                    <br />

                    <div class="chgpass-btn">
                        <input type="submit" name="chgpass" id="chgpass" class="chgpass" value="Confirm">
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