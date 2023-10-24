<?php
//set the session timeout to 4 hours (4 hours * 60 minutes * 60 seconds)
ini_set('session.gc_maxlifetime', 4 * 60 * 60);
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

    <title>Programme List</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>Programme List</h3>
        </div>

        <?php
        $query = "SELECT * FROM [programme]";
        $statement = sqlsrv_query($conn, $query);

        $schools = array(); //create an array to store the unique school names

        while ($row = sqlsrv_fetch_array($statement)) {
            $school = $row['department'];
            $programme = $row['programme'];

            //if the school is not in the schools array, add it
            if (!array_key_exists($school, $schools)) {
                $schools[$school] = array();
            }

            //add the programme to the corresponding school
            $schools[$school][] = $programme;
        }
        ?>
        <div class="programme-container">
            <?php $count = 0;
            foreach ($schools as $school => $programmes) {
                if ($count % 2 === 0) { ?>
                    <div class="programme-row">
                    <?php } ?>
                    <div class="school">
                        <h4><?php echo $school; ?></h4>
                        <div class="programme">
                            <?php foreach ($programmes as $programme) { ?>
                                <div class="link">
                                    <a href="../clients/pastyearlist.php?programme=<?php echo $programme; ?>" class="pgtitle"><?php echo $programme; ?></a></td>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($count % 2 === 1 || $count === count($schools) - 1) { ?>
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