<?php
session_start();
include('connect.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel="icon" href="./assets/BookNest_Logo.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./styles/navbar.css">
</head>

<body>
    <!-- For Logo -->
    <div class="logo">
        <div class="container">
            <ul>
                <li><a href="index.php"><img src="./assets/BookNest_Logo.png" width="95em" height="80em"></a></li>
                <?php
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                    // User is logged in, display "Sign Out"
                    echo '<li style="float:right"><a href="./backend/signoutdb.php">SIGN OUT</a></li>';
                } else {
                    // User is not logged in, display "Sign In" and "Sign Up"
                    echo '<li style="float:right"><a href="signin.php">SIGN IN</a> / <a href="signup.php">SIGN UP</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <nav>
        <!-- For Navigation Bar -->
        <div class="navbar">
            <a href="index.php">HOME</a>
            <a href="catalog.php">BOOK CATALOG</a>
            <div class="dropdown">
                <button class="dropbtn">USING THE LIBRARY</button>
                <div class="dropdown-content">
                    <a href="aboutlibrary.php">About Library</a>
                    <a href="aboutrules.php">Rules & Regulation</a>
                    <a href="aboutfines.php">Fines & Penalty</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">ONLINE RESOURCES</button>
                <div class="dropdown-content">
                    <a href="#">Online Database</a>
                    <a href="#">Past Year Exam Papers</a>
                    <a href="#">Student's Project</a>
                    <div class="dropdown-second">
                        <button class="dropbtn2">Online Magazine</button>
                        <div class="dropdown-third">
                            <a href="https://freemagazines.top" target="_blank">Free Magazines</a>
                            <a href="https://www.magzter.com/most-popular" target="_blank">Magzter</a>
                        </div>
                    </div>
                    <div class="dropdown-second">
                        <button class="dropbtn2">Online Newspaper</button>
                        <div class="dropdown-third">
                            <a href="https://www.bharian.com.my/?utm_source=google&utm_source=google&utm_medium=search&utm_medium=search&utm_campaign=bh_always_on_rsa&utm_campaign=bh_always_on_rsa&gclid=CjwKCAjwu5yYBhAjEiwAKXk_eBEfejwvmdxZRhtu0WfGPgM4bQnpcXBxauv9Qyf1-CTY4Z4mOCVxKhoCKXoQAvD_BwE" target="_blank">Berita Harian</a>
                            <a href="https://www.nst.com.my/?utm_source=google&utm_source=google&utm_medium=search&utm_medium=search&utm_campaign=nst_always%20on%20%28RSA%29_22&utm_campaign=nst_always%20on%20%28RSA%29_22&gclid=CjwKCAjwu5yYBhAjEiwAKXk_eFgQbceM6SqSGoIcjmScR9sVhQ7WZSHCO8UzXvCdAw1pUyvWR4dQsRoCZskQAvD_BwE" target="_blank">New Straits Times</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">MY ACCOUNT</button>
                <div class="dropdown-content">
                    <a href="myprofile.php">My Profile</a>
                    <a href="#">My Favorites</a>
                    <a href="#">On Loan Books</a>
                    <a href="#">Reservations</a>
                </div>
            </div>
        </div>
    </nav>
</body>

</html>