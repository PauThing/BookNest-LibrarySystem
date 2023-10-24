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
    <link rel="stylesheet" href="../clients/styles/reservation.css">

    <title>Discussion Room Schedule</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openSetting() {
            document.getElementById("setting-container").style.display = "block";
            document.getElementById("overlay-bg").style.display = "block";
        }

        function closeSetting() {
            document.getElementById("setting-container").style.display = "none";
            document.getElementById("overlay-bg").style.display = "none";
        }

        function openHistory() {
            window.location.href = '../clients/reservationlist.php';
        }
    </script>
</head>

<body>
    <div class="big-container">
        <div class="header">
            <h3>Discussion Room Daily Schedule</h3>
        </div>

        <div class="reserve-room" onclick="openSetting()">
            <i class="fas fa-door-open"></i> Reserve a Room
        </div>

        <div class="view-reservation" onclick="openHistory()">
            <i class="fa fa-eye"></i> Reservation History
        </div>

        <div class="reservation-schedule"></div>
    </div>

    <div class="overlay-bg" id="overlay-bg"></div>

    <div id="setting-container" class="setting-container">
        <form id="setting-form" class="setting-form" method="post" action="../admin/backend/eroomsettingdb.php" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeSetting()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>Configuration Settings</h3>
            </div>

            <table id="room-setting">
                <thead>
                    <tr>
                        <th>Discussion Room ID</th>
                        <th>Discussion Room</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <?php
                $query = "SELECT * FROM [discussionroom]";
                $statement = sqlsrv_query($conn, $query);

                if ($statement === false) {
                    die(print_r(sqlsrv_errors(), true)); //print and handle the error
                }

                while ($row = sqlsrv_fetch_array($statement)) {
                ?>
                    <tbody>
                        <tr>
                            <td><?php echo $row['droom_id']; ?></td>
                            <td><?php echo $row['droom_num']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    </tbody>
                <?php } ?>
            </table>

            <div class="wrap">
                <div class="SelectInput" data-mate-select="active">
                    <label for="droom">Discussion Room</label> <br />
                    <select name="droom" id="droom" class="droom" required>
                        <option value="">Select a room </option>
                        <option value="Room 1">Room 1</option>
                        <option value="Room 2">Room 2</option>
                        <option value="Room 3">Room 3</option>
                        <option value="Room 4">Room 4</option>
                    </select>
                </div>

                <br />

                <div class="SelectInput" data-mate-select="active">
                    <label for="status">Status</label> <br />
                    <select name="status" id="status" class="status" required>
                        <option value="">Select an option </option>
                        <option value="Available">Available</option>
                        <option value="Unavailable">Unavailable</option>
                    </select>
                </div>

                <br />

                <div class="setting-btn">
                    <input type="submit" name="setting" id="setting" class="setting" value="Save">
                </div>
            </div>
        </form>
    </div>

    <br /><br />

    <span>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<script>alert('" . $_SESSION['message'] . "');</script>";
        }

        unset($_SESSION['message']);
        ?>
    </span>

    <script>

    </script>
</body>

</html>