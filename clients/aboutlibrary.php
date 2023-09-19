<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./styles/about.css">

    <title>About Library</title>

    <?php
    include "./navBar.php";
    ?>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>About Library</h3>
        </div>
        <div class="librarian-container">
            <form class="librarian-form" id="librarian-form" action="">
                <div class="wrap">
                    <div class="header">
                        <h4>The Librarians</h4>

                        <br />

                        
                    </div>
                </div>
            </form>
        </div>

        <br /><br />

        <div class="membership-container">
            <form class="member-form" id="member-form" action="">
                <div class="wrap">
                    <div class="header">
                        <h4>Membership</h4>
                    </div>
                </div>
            </form>
        </div>

        <br /><br />

        <div class="ophours-container">
            <form class="ophours-form" id="ophours-form" action="">
                <div class="wrap">
                    <div class="header">
                        <h4>Opening Hours</h4>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>

</html>