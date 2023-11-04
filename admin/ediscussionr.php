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
            window.location.href = '../admin/ereservationlist.php';
        }
    </script>
</head>

<body>
    <div class="big-container">
        <?php
        $query = "SELECT
                    r.*,
                    u.fullname,
                    dr.*
                FROM [reservation] r
                LEFT JOIN [user] u ON r.user_id = u.user_id
                LEFT JOIN [discussionroom] dr ON r.droom_id = dr.droom_id
                WHERE [created_at] = CONVERT(DATE, GETDATE())";
        $statement = sqlsrv_query($conn, $query);

        $reservationData = array();

        if ($statement) {
            while ($row = sqlsrv_fetch_array($statement, SQLSRV_FETCH_ASSOC)) {
                $reservationData[] = $row;
            }
        }
        ?>

        <div class="header">
            <h3>Discussion Room Daily Schedule</h3>
            <h4><?php $currentDate = date('Y-m-d');
                echo $currentDate; ?></h4>
        </div>

        <br />

        <div class="open-setting" onclick="openSetting()">
            <i class="fa fa-gear"></i> Settings
        </div>

        <div class="view-reservation" onclick="openHistory()">
            <i class="fa fa-eye"></i> Reservation History
        </div>

        <div class="reservation-schedule">
            <table id="schedule">
                <thead>
                    <tr>
                        <th>Time Slot</th>
                        <th>Room 1</th>
                        <th>Room 2</th>
                        <th>Room 3</th>
                        <th>Room 4</th>
                    </tr>
                </thead>

                <tbody>
                    <tr id="9.00 AM - 10.00 AM">
                        <td><b>9.00 AM - 10.00 AM</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr id="10.00 AM - 11.00 AM">
                        <td><b>10.00 AM - 11.00 AM</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr id="11.00 AM - 12.00 PM">
                        <td><b>11.00 AM - 12.00 PM</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr id="12.00 PM - 1.00 PM">
                        <td><b>12.00 PM - 1.00 PM</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr id="1.00 PM - 2.00 PM">
                        <td><b>1.00 PM - 2.00 PM</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr id="2.00 PM - 3.00 PM">
                        <td><b>2.00 PM - 3.00 PM</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr id="3.00 PM - 4.00 PM">
                        <td><b>3.00 PM - 4.00 PM</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr id="4.00 PM - 5.00 PM">
                        <td><b>4.00 PM - 5.00 PM</b></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
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
        //for displaying data in timetable form
        document.addEventListener("DOMContentLoaded", function() {
            var scheduleTable = document.getElementById("schedule");
            var tbody = scheduleTable.getElementsByTagName('tbody')[0];

            <?php foreach ($reservationData as $reservation) { ?>
                var roomNum = "<?php echo $reservation['droom_num']; ?>";
                var timeSlot = "<?php echo $reservation['time_slot']; ?>";
                var name = "<?php echo $reservation['fullname']; ?>";
                var member = "<?php echo $reservation['member']; ?>";

                var timeSlot = findSlotWithText("<?php echo $reservation['time_slot']; ?>");
                var room = findRoomWithText("<?php echo $reservation['droom_num']; ?>");

                if (timeSlot && room) {
                    var columnIndex = room.cellIndex; //get the column index of the room
                    var row = timeSlot.parentElement;
                    var cell = row.cells[columnIndex];

                    //populate the cell with the user's name
                    cell.innerHTML = "<b>" + name + "</b><br>Members: " + member;
                    cell.style.backgroundColor = "#fee7e7";
                }
            <?php } ?>
        });

        function findSlotWithText(text) {
            var cells = document.getElementsByTagName("td");
            for (var i = 0; i < cells.length; i++) {
                if (cells[i].textContent === text) {
                    return cells[i];
                }
            }
            return null;
        }

        function findRoomWithText(text) {
            var cells = document.getElementsByTagName("th");
            for (var i = 0; i < cells.length; i++) {
                if (cells[i].textContent === text) {
                    return cells[i];
                }
            }
            return null;
        }
    </script>
</body>

</html>