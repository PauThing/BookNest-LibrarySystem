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
    <link rel="stylesheet" href="../clients/styles/resources.css">

    <title>Student's Project</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>Student's Project</h3>
        </div>

        <?php
        $query = "SELECT * FROM [studentproject]";
        $statement = sqlsrv_query($conn, $query);

        $programmes = array(); //create an array to store the programmes
        $monthYears = array(); //create an array to store the years

        while ($row = sqlsrv_fetch_array($statement)) {
            $date = $row['created_at'];
            list($month, $year) = explode('-', $date);

            //construct a DateTime object based on the year and month
            $dateTime = new DateTime();
            $dateTime->setDate($year, $month, 1);
            $formattedDate = $dateTime->format('F Y');

            $programme = $row['programme'];
            $title = $row['title'];
            $docData = $row['filedata'];

            //if the programme is not in the programmes array, add it
            if (!array_key_exists($programme, $programmes)) {
                $programmes[$programme] = array();
            }

            //if the year is not in the years array, add it
            if (!array_key_exists($formattedDate, $monthYears)) {
                $monthYears[$formattedDate] = array();
            }

            //add the exam paper title to the corresponding year
            $programmes[$programme][$formattedDate][] = $title;
        }
        ?>
        <div class="stuproject-container">
            <?php $count = 0;
            foreach ($programmes as $programme => $monthYears) {
                if ($count % 2 === 0) { ?>
                    <div class="stuproject-row">
                    <?php } ?>
                    <div class="programme">
                        <h4><?php echo $programme; ?></h4>
                        <div class="year">
                            <?php foreach ($monthYears as $monthYear => $titles) { ?>
                                <h5><?php echo $monthYear; ?></h5>
                                <div class="title">
                                    <?php foreach ($titles as $title) { ?>
                                        <div class="link">
                                            <a href="../admin/backend/viewdocdb.php?sptitle=<?php echo $title; ?>" target="_blank" class="sptitle"><?php echo $title; ?></a>
                                            <br />
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($count % 2 === 1 || $count === count($programmes) - 1) { ?>
                    </div>
            <?php }
                    $count++;
                } ?>
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