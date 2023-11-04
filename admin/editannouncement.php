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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openForm() {
            window.location.href = '../admin/addannouncement.php';
        }
    </script>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>Announcement</h3>
        </div>

        <div class="add-announcement" onclick="openForm()">
            <i class="fa fa-plus"></i> New Announcement
        </div>

        <?php
        $itemsPerPage = 20;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($currentPage - 1) * $itemsPerPage;

        $cquery = "SELECT COUNT(*) AS ttlrecord FROM [announcement]";
        $cstatement = sqlsrv_query($conn, $cquery);
        $ttlrecord = 0;

        if ($cstatement) {
            $crow = sqlsrv_fetch_array($cstatement);
            $ttlrecord = $crow['ttlrecord'];
        }
        //calculate the total number of pages
        $ttlpages = ceil($ttlrecord / $itemsPerPage);

        $query = "SELECT * FROM [announcement] ORDER BY [created_at] DESC OFFSET $offset ROWS FETCH NEXT $itemsPerPage ROWS ONLY";
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

            //format the DateTime object as "Month Year"
            $formattedDate = $date->format('F Y');
            
            $anntitle = $row['ann_title'];
            
            //if the school is not in the schools array, add it
            if (!array_key_exists($formattedDate, $dates)) {
                $dates[$formattedDate] = array();
            }

            //add the programme to the corresponding school
            $dates[$formattedDate][] = array('title' => $anntitle, 'date' => $showDate, 'id' => $annid);
        }
        ?>
        <div class="announcement-container">
            <?php $count = 0;
            foreach ($dates as $formattedDate => $anntitles) {
                if ($count % 2 === 0) { ?>
                    <div class="anntitle-row">
                    <?php } ?>
                    <div class="date">
                        <h4><?php echo $formattedDate; ?></h4>
                        <div class="anntitle">
                            <?php foreach ($anntitles as $entry) { ?>
                                <div class="link">
                                    <a href="javascript:void(0);" class="title" onclick="openDetail('<?php echo $entry['id']; ?>')"><?php echo $entry['title']; ?> • <span style="color: blue;"><?php echo $entry['date']; ?></span></a>
                                    <a href="../admin/backend/delannouncementdb.php?annID=<?php echo $entry['id']; ?>" class="del" onclick="return confirm('Are you sure you want to delete this announcement?');"><i class="fa fa-trash"></i></a>
                                    <br />
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($count % 2 === 1 || $count === count($dates) - 1) { ?>
                    </div>
            <?php }
                    $count++;
                } ?>
        </div>

        <!-- Pagination - page by page -->
        <div class="pagination-container">
            <ul class="pagination">
                <?php
                if ($currentPage > 1) {
                    echo "<li><a href='?page=" . ($currentPage - 1) . "'>Previous</a></li>";
                } else {
                    echo "<li><span>Previous</span></li>";
                }

                if ($currentPage < $ttlpages) {
                    echo "<li><a href='?page=" . ($currentPage + 1) . "'>Next</a></li>";
                } else {
                    echo "<li><span>Next</span></li>";
                }
                ?>
            </ul>
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
        function openDetail(id) {
            $.ajax({
                type: 'GET',
                url: '../admin/announcementdetail.php',
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