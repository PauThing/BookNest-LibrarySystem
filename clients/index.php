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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.css" integrity="sha512-Z0kTB03S7BU+JFU0nw9mjSBcRnZm2Bvm0tzOX9/OuOuz01XQfOpa0w/N9u6Jf2f1OAdegdIPWZ9nIZZ+keEvBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../clients/styles/index.css">

    <title>BookNest Library</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="big-container">
        <div class="flex-wrap">
            <div class="slideshow-wrap">
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

            </div>

            <div class="ann-wrap">
                <div class="header">
                    <h3>Announcement</h3>
                </div>

                <div class="announcement-container">
                    <div class="view-all">
                        <a href="../clients/announcement.php" class="all">View All</a>
                    </div>

                    <br />

                    <?php
                    $query = "SELECT TOP 5 * FROM [announcement] ORDER BY [created_at] DESC";
                    $statement = sqlsrv_query($conn, $query);

                    $dates = array(); //create an array to store the unique school names

                    while ($row = sqlsrv_fetch_array($statement)) {
                        $annid = $row['ann_id'];
                        $date = $row['created_at'];

                        //check if it is a string
                        if (is_string($date)) {
                            $date = date_create($date);
                        }

                        $showDate = $date->format('Y-m-d');

                        $anntitle = $row['ann_title'];

                        //if the date is not in the dates array, add it
                        if (!array_key_exists($showDate, $dates)) {
                            $dates[$showDate] = array();
                        }

                        //add the title, date, detail to the corresponding school
                        $dates[$showDate][] = array('title' => $anntitle, 'date' => $showDate, 'id' => $annid);
                    }
                    ?>
                    <div class="ann-container">
                        <?php foreach ($dates as $showDate => $anntitles) { ?>
                            <div class="anntitle-row">
                                <div class="anntitle">
                                    <?php foreach ($anntitles as $entry) { ?>
                                        <div class="link">
                                            <a href="javascript:void(0);" class="title" onclick="openDetail('<?php echo $entry['id']; ?>')"><?php echo $entry['title']; ?> • <span style="color: blue;"><?php echo $entry['date']; ?></span></a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
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

    <div id="ann-detail-container" class="ann-detail-container"></div>

    <span>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        }

        unset($_SESSION['message']);
        ?>
    </span>

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

        function openDetail(id) {
            $.ajax({
                type: 'GET',
                url: '../clients/announcementdetail.php',
                data: {
                    annid: id
                },
                success: function(response) {
                    $('#ann-detail-container').html(response);
                    document.getElementById("ann-detail-container").style.display = "block";
                },
                error: function() {
                    console.log("AJAX Error: " + status + " - " + error);
                    alert('Failed to load announcement details.');
                }
            });
        }

        function closeDetail() {
            document.getElementById("ann-detail-container").style.display = "none";
        }
    </script>
</body>

</html>