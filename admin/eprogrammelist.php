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
    <script>
        function openForm() {
            document.getElementById("new-programme-container").style.display = "block";
        }

        function closeForm() {
            document.getElementById("new-programme-container").style.display = "none";
        }
    </script>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>Programme List</h3>
        </div>

        <div class="add-programme" onclick="openForm()">
            <i class="fa fa-plus"></i> New Programme
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
                                    <a href="../admin/epastyearlist.php?programme=<?php echo $programme; ?>" class="pgtitle"><?php echo $programme; ?></a>
                                    <a href="../admin/backend/delresourcedb.php?programme=<?php echo $programme; ?>" class="del" onclick="return confirm('Are you sure you want to delete this programme?');"><i class="fa fa-trash"></i></a>
                                    <br />
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

    <div id="new-programme-container" class="new-programme-container">
        <form id="new-programme-form" class="new-programme-form" method="post" action="../admin/backend/newresourcedb.php" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>NEW PROGRAMME</h3>
            </div>

            <br />

            <div class="wrap">
                <div class="InputText">
                    <input type="text" name="programme" id="programme" placeholder="DBM" autocomplete="off" required>
                    <label for="programme">Programme</label>
                </div>

                <div class="SelectInput" data-mate-select="active">
                    <label for="school">Department</label> <br />
                    <select name="school" id="school" class="school" required>
                        <option value="">Select a department </option>
                        <option value="School of Arts and Sciences">School of Arts and Sciences</option>
                        <option value="School of Communication">School of Communication</option>
                        <option value="School of Computing">School of Computing</option>
                        <option value="School of Education">School of Education</option>
                        <option value="School of Engineering">School of Engineering</option>
                        <option value="School of Hospitality">School of Hospitality</option>
                        <option value="School of Management">School of Management</option>
                    </select>
                </div>

                <br /><br />

                <div class="new-programme-btn">
                    <input type="submit" name="new-programme" id="new-programme" class="new-programme" value="Create">
                </div>
            </div>
        </form>
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