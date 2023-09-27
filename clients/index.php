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
    <link rel="stylesheet" href="../clients/styles/index.css">

    <title>BookNest Library</title>
</head>

<body>
    <div class="big-container">
        <div class="slideshow-container">
            <div class="slide-fade">
                <img src="../clients/assets/Welcome.png" style="width:100%; height:20em;">
            </div>

            <div class="slide-fade">
                <img src="../clients/assets/Library1.jpg" style="width:100%; height:20em;">
            </div>

            <div class="slide-fade">
                <img src="../clients/assets/Library2.jpg" style="width:100%; height:20em;">
            </div>

            <a class="prev" onclick="pushSlides(-1)">❮</a>
            <a class="next" onclick="pushSlides(1)">❯</a>

        </div>

        <div style="text-align:center">
            <span class="dot" onclick="currSlide(1)"></span>
            <span class="dot" onclick="currSlide(2)"></span>
            <span class="dot" onclick="currSlide(3)"></span>
        </div>

        <br />

        <div class="header">
            <h3>Announcement</h3>
        </div>
        <div class="announcement-container">
            <form class="ann-form" id="ann-form" action="">
                <div class="wrap">

                </div>
            </form>
        </div>

        <br /><br />

        <div class="header">
            <h3 style="visibility:hidden;">Monthly Report</h3>
        </div>
        <div class="monthr-container">
            <form class="monthr-form" id="monthr-form" action="">
                <div class="wrap">

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

<script>
    let slideIndex = 1;
    showSlides(slideIndex);

    function pushSlides(n) {
        showSlides(slideIndex += n);
    }

    function currSlide(n) {
        showSlides(slideIndex = n);
    }

    function showSlides(n) {
        let i;
        let slides = document.getElementsByClassName("slide-fade");
        let dots = document.getElementsByClassName("dot");

        if (n > slides.length) {
            slideIndex = 1
        }

        if (n < 1) {
            slideIndex = slides.length
        }

        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }

        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }

        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " active";
    }
</script>

</html>