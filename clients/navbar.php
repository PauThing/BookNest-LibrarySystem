<?php
include('../clients/connect.php');
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>

    <link rel="icon" href="../clients/assets/BookNest.ico" type="image/x-icon">
    <link rel="icon" href="../clients/assets/BookNest_Logo.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../clients/styles/navbar.css">
</head>

<body>
    <div class="logo">
        <div class="container">
            <ul>
                <li><a href="index.php"><img src="../clients/assets/BookNest_Logo.png" width="95em" height="80em"></a></li>
                <?php
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                    // User is logged in, display "Sign Out"
                    echo '<li style="float:right"><a href="../clients/backend/signoutdb.php">SIGN OUT</a></li>';
                } else {
                    // User is not logged in, display "Sign In" and "Sign Up"
                    echo '<li style="float:right"><a href="../clients/signin.php">SIGN IN</a> | <a href="../clients/signup.php">SIGN UP</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <?php
    if (!isset($_SESSION['userid']) || trim($_SESSION['userid'] == '')) {
    ?>
        <nav>
            <div class="navbar">
                <a href="index.php">HOME</a>
                <a href="catalog.php">BOOK CATALOG</a>
                <div class="dropdown">
                    <button class="dropbtn">USING THE LIBRARY</button>
                    <div class="dropdown-content">
                        <a href="../clients/aboutlibrary.php">About Library</a>
                        <a href="../clients/aboutrules.php">Rules & Regulation</a>
                        <a href="../clients/aboutfines.php">Fines & Penalty</a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="dropbtn">ONLINE RESOURCES</button>
                    <div class="dropdown-content">
                        <a href="../clients/onlinedblist.php">Online Database</a>
                        <a href="../clients/programmelist.php">Past Year Exam Papers</a>
                        <a href="../clients/stuprojectlist.php">Student's Project</a>
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
                        <a href="../clients/myprofile.php">My Profile</a>
                        <a href="../clients/myfavorites.php">My Favorites</a>
                        <a href="../clients/onloanbook.php">On Loan Books</a>
                        <a href="../clients/reservation.php">Reservations</a>
                    </div>
                </div>
            </div>
        </nav>

        <?php
    } else {
        $userid = $_SESSION['userid'];
        $query = sqlsrv_query($conn, "SELECT * FROM [user] WHERE [user_id] = '$userid'");

        // Fetch the result
        $row = sqlsrv_fetch_array($query);

        if ($row) {
            $usertype = $row['usertype'];

            switch ($usertype) {
                case 'Student':
        ?>
                    <nav>
                        <div class="navbar">
                            <a href="index.php">HOME</a>
                            <a href="catalog.php">BOOK CATALOG</a>
                            <div class="dropdown">
                                <button class="dropbtn">USING THE LIBRARY</button>
                                <div class="dropdown-content">
                                    <a href="../clients/aboutlibrary.php">About Library</a>
                                    <a href="../clients/aboutrules.php">Rules & Regulation</a>
                                    <a href="../clients/aboutfines.php">Fines & Penalty</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="dropbtn">ONLINE RESOURCES</button>
                                <div class="dropdown-content">
                                    <a href="../clients/onlinedblist.php">Online Database</a>
                                    <a href="../clients/programmelist.php">Past Year Exam Papers</a>
                                    <a href="../clients/stuprojectlist.php">Student's Project</a>
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
                                    <a href="../clients/myprofile.php">My Profile</a>
                                    <a href="../clients/myfavorites.php">My Favorites</a>
                                    <a href="../clients/onloanbook.php">On Loan Books</a>
                                    <a href="../clients/reservation.php">Reservations</a>
                                </div>
                            </div>
                        </div>
                    </nav>

                <?php
                    break;

                case 'Admin':
                ?>

                    <nav>
                        <div class="navbar">
                            <a href="../admin/index.php">DASHBOARD</a>
                            <a href="../admin/catalog.php">BOOK CATALOG</a>
                            <a href="../admin/bookreturn.php">BOOK RETURN</a>
                            <a href="../admin/discussionroom.php">DISCUSSION ROOM</a>
                            <div class="dropdown">
                                <button class="dropbtn">USING THE LIBRARY</button>
                                <div class="dropdown-content">
                                    <a href="../admin/editlibrary.php">About Library</a>
                                    <a href="../admin/editrules.php">Rules & Regulation</a>
                                    <a href="../admin/editfines.php">Fines & Penalty</a>
                                    <a href="../admin/editannouncement.php">Announcement</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="dropbtn">ONLINE RESOURCES</button>
                                <div class="dropdown-content">
                                    <a href="../admin/eonlinedblist.php">Online Database</a>
                                    <a href="../admin/eprogrammelist.php">Past Year Exam Papers</a>
                                    <a href="../admin/estuprojectlist.php">Student's Project</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="dropbtn">ACCOUNT</button>
                                <div class="dropdown-content">
                                    <a href="../clients/myprofile.php">My Profile</a>
                                    <a href="../admin/adminlist.php">Administrator List</a>
                                    <a href="../admin/userlist.php">User List</a>
                                </div>
                            </div>
                        </div>
                    </nav>

                <?php
                    break;

                case 'SuperAdmin':
                ?>

                    <nav>
                        <div class="navbar">
                            <a href="../admin/index.php">DASHBOARD</a>
                            <a href="../admin/catalog.php">BOOK CATALOG</a>
                            <a href="../admin/bookreturn.php">BORROW &  RETURN</a>
                            <a href="../admin/discussionroom.php">DISCUSSION ROOM</a>
                            <div class="dropdown">
                                <button class="dropbtn">USING THE LIBRARY</button>
                                <div class="dropdown-content">
                                    <a href="../admin/editlibrary.php">About Library</a>
                                    <a href="../admin/editrules.php">Rules & Regulation</a>
                                    <a href="../admin/editfines.php">Fines & Penalty</a>
                                    <a href="../admin/editannouncement.php">Announcement</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="dropbtn">ONLINE RESOURCES</button>
                                <div class="dropdown-content">
                                    <a href="../admin/eonlinedblist.php">Online Database</a>
                                    <a href="../admin/eprogrammelist.php">Past Year Exam Papers</a>
                                    <a href="../admin/estuprojectlist.php">Student's Project</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="dropbtn">ACCOUNT</button>
                                <div class="dropdown-content">
                                    <a href="../clients/myprofile.php">My Profile</a>
                                    <a href="../admin/adminlist.php">Administrator List</a>
                                    <a href="../admin/userlist.php">User List</a>
                                </div>
                            </div>
                        </div>
                    </nav>

    <?php
                    break;
            }
        }
    }
    ?>
</body>

</html>