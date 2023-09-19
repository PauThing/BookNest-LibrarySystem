<?php
include('navbar.php');
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
            $(input).closest(".file-upload-wrapper").attr("data-text", fileName);
        }
    </script>
</head>

<body>
    <div class="big-container">
        <div class="signup-container">
            <form class="signup-form" id="signup-form" action="" enctype="multipart/form-data">
                <div class="header">
                    <h2>SIGN UP</h2>
                </div>

                <div class="wrap">
                    <div class="InputText">
                        <input type="text" name="fname" id="fname" required>
                        <label>Full Name</label>
                    </div>

                    <div class="InputText">
                        <input type="email" name="uEmail" id="uEmail" required>
                        <label>Student Email</label>
                    </div>

                    <div class="InputText">
                        <input type="text" name="uID" id="uID" required>
                        <label>Student ID</label>
                    </div>

                    <div class="InputFile">
                        <label>Student ID Image</label>
                        <br />
                        <div class="file-upload-wrapper" data-text="Choose File (PNG, JPG, JPEG)">
                            <input type="file" name="file-upload-field" class="file-upload-field" required onchange="updateDataText(this)">
                        </div>
                    </div>
                    <br /><br />

                    <div class="InputText">
                        <input type="password" name="password" id="password" required>
                        <label>Password</label>
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
</body>

</html>