<?php
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

    <title>Sign Up</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function updateDataText(input) {
            // Get the file name from the input field
            var fileName = input.value.replace(/.*(\/|\\)/, '');

            // Update the data-text attribute of the parent element
            $(input).closest(".file-upload-wrapper").attr("aria-placeholder", fileName);
        }
    </script>
</head>

<body>
    <div class="big-container">
        <div class="signup-container">
            <form class="signup-form" id="signup-form" method="post" action="./backend/signupdb.php" enctype="multipart/form-data">
                <div class="header">
                    <h2>SIGN UP</h2>
                </div>

                <div class="wrap">
                    <div class="InputText">
                        <input type="text" name="fname" id="fname" required>
                        <label for="fname">Full Name</label>
                    </div>

                    <div class="InputText">
                        <input type="email" name="uEmail" id="uEmail" required>
                        <label for="uEmail">Student Email</label>
                    </div>

                    <div class="InputText">
                        <input type="text" name="uID" id="uID" required>
                        <label for="uID">Student ID</label>
                    </div>

                    <div class="InputFile">
                        <label for="file-upload-field">Student ID Card</label>
                        <br />
                        <div class="file-upload-wrapper" aria-placeholder="Choose File (PNG, JPG, JPEG)">
                            <input type="file" name="file-upload-field" id="file-upload-field" class="file-upload-field" accept="image/*" required onchange="updateDataText(this)">
                        </div>
                    </div>
                    <br /><br />

                    <div class="InputText">
                        <input type="password" name="password" id="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number, one uppercase letter, one lowercase letter, and at least 8 or more characters" required>
                        <label for="password">Password</label>
                    </div>

                    <div class="signup-btn">
                        <input type="submit" name="signup" id="signup" class="signup" value="Sign Up">
                    </div>

                    <div class="registered">
                        Already Registered? <a href="./signin.php">Sign In Here!</a>
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