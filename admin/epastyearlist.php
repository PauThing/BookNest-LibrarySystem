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

    <title>Past Year Exam Paper</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openForm() {
            window.location.href = '../admin/addpastyear.php?programme=<?php echo $_GET['programme']; ?>';
        }

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
        <?php $pg = $_GET['programme']; ?>

        <div class="header">
            <h3><?php echo $pg; ?></h3>
        </div>

        <div class="add-exampaper" onclick="openForm()">
            <i class="fa fa-plus"></i> Upload
        </div>

        <?php
        $query = "SELECT * FROM [exampaper] WHERE [programme] = '$pg' ORDER BY [created_at] DESC";
        $statement = sqlsrv_query($conn, $query);

        $years = array(); //create an array to store the years

        while ($row = sqlsrv_fetch_array($statement)) {
            $date = $row['created_at'];
            list($month, $year) = explode('-', $date);

            //construct a DateTime object based on the year and month
            $dateTime = new DateTime();
            $dateTime->setDate($year, $month, 1);
            $formattedDate = $dateTime->format('F Y');

            $docName = $row['filename'];
            $docData = $row['filedata'];

            //if the year is not in the years array, add it
            if (!array_key_exists($formattedDate, $years)) {
                $years[$formattedDate] = array();
            }

            //add the exam paper title to the corresponding year
            $years[$formattedDate][] = $docName;
        }
        ?>
        <div class="exampaper-container">
            <?php $count = 0;
            foreach ($years as $formattedDate => $docNames) {
                if ($count % 2 === 0) { ?>
                    <div class="exampaper-row">
                    <?php } ?>
                    <div class="year">
                        <h4><?php echo $formattedDate; ?></h4>
                        <div class="exampaper">
                            <?php foreach ($docNames as $docName) { ?>
                                <div class="link">
                                    <a href="../admin/backend/viewdocdb.php?eptitle=<?php echo $docName; ?>&epprogramme=<?php echo $pg; ?>" target="_blank" class="eptitle"><?php echo $docName; ?></a>
                                    <a href="../admin/backend/delresourcedb.php?eptitle=<?php echo $docName; ?>&epprogramme=<?php echo $pg; ?>" class="del" onclick="return confirm('Are you sure you want to delete this exam paper?');"><i class="fa fa-trash"></i></a>
                                    <br />
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($count % 2 === 1 || $count === count($years) - 1) { ?>
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