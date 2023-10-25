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
        function openForm() {
            document.getElementById("reserve-room-container").style.display = "block";
            document.getElementById("overlay-bg").style.display = "block";
        }

        function closeForm() {
            document.getElementById("reserve-room-container").style.display = "none";
            document.getElementById("overlay-bg").style.display = "none";
        }

        function openHistory() {
            window.location.href = '../clients/reservationlist.php';
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

        <div class="reserve-room" onclick="openForm()">
            <i class="fas fa-door-open"></i> Reserve a Room
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

    <div id="reserve-room-container" class="reserve-room-container">
        <form id="reserve-room-form" class="reserve-room-form" method="post" action="../clients/backend/addreservationdb.php" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>Reserve Your Discussion Room</h3>
                <label class="instruction">You are only allow to reserve the discussion room on this day.</label>
            </div>

            <div class="wrap">
                <div class="InputSlide">
                    <label for="member">Number of Members: <b><span id="num" class="num"></span></b></label><br />
                    <input type="range" name="member" id="member" class="member" value="" min="2" max="6" required>
                </div>

                <br />

                <div class="SelectInput" data-mate-select="active">
                    <label for="droom">Discussion Room</label> <br />
                    <select name="droom" id="droom" class="droom" required>
                        <option value="">Select a room </option>
                        <?php
                        $query = "SELECT * FROM [discussionroom] WHERE [status] = 'Available'";
                        $statement = sqlsrv_query($conn, $query);

                        while ($row = sqlsrv_fetch_array($statement)) {
                        ?>
                            <option value="<?php echo $row['droom_id']; ?>"><?php echo $row['droom_num']; ?></option>
                        <?php } ?>
                    </select>

                    <label class="explanation">
                        Note: Unavailable Room Today - 
                        <?php
                        $query = "SELECT * FROM [discussionroom] WHERE [status] = 'Unavailable'";
                        $statement = sqlsrv_query($conn, $query);
                        while($row = sqlsrv_fetch_array($statement)) {
                         echo $row['droom_num'] . " . "; } ?>
                    </label>
                </div>

                <br />

                <div class="SelectInput" data-mate-select="active">
                    <label for="slot">Time Slot</label> <br />
                    <select name="slot" id="slot" class="slot" required>
                        <option value="">Select a slot </option>
                        <option value="9.00 AM - 10.00 AM">9.00 AM - 10.00 AM</option>
                        <option value="10.00 AM - 11.00 AM">10.00 AM - 11.00 AM</option>
                        <option value="11.00 AM - 12.00 PM">11.00 AM - 12.00 PM</option>
                        <option value="12.00 PM - 1.00 PM">12.00 PM - 1.00 PM</option>
                        <option value="1.00 PM - 2.00 PM">1.00 PM - 2.00 PM</option>
                        <option value="2.00 PM - 3.00 PM">2.00 PM - 3.00 PM</option>
                        <option value="3.00 PM - 4.00 PM">3.00 PM - 4.00 PM</option>
                        <option value="4.00 PM - 5.00 PM">4.00 PM - 5.00 PM</option>
                    </select>
                </div>

                <br />

                <div class="reserve-btn">
                    <input type="submit" name="reserve" id="reserve" class="reserve" value="Reserve">
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
        //for slider in reservation form
        var slider = document.getElementById("member");
        var num = document.getElementById("num");
        num.innerHTML = slider.value; //display the default slider value

        //update the current slider value (each time you drag the slider handle)
        slider.oninput = function() {
            num.innerHTML = this.value;
        }

        //disable the past time slot
        document.addEventListener("DOMContentLoaded", function() {
            var slotSelect = document.getElementById("slot");
            var now = new Date();
            var currentHour = now.getHours();
            var currentMinute = now.getMinutes();

            for (var i = 1; i < slotSelect.options.length; i++) {
                var slotValue = slotSelect.options[i].value;
                var startHour = parseInt(slotValue.split(" - ")[0]);

                for (var i = 1; i < slotSelect.options.length; i++) {
                    var slotValue = slotSelect.options[i].value;
                    var startHour = parseInt(slotValue.split(" - ")[0].split(".")[0]);
                    var isAM = slotValue.indexOf("AM") !== -1;

                    //convert AM/PM to 24-hour format
                    if (!isAM && startHour === 12) {
                        startHour = 12;
                    } else if (!isAM) {
                        startHour += 12;
                    }

                    //compare the current time with the start time of the slot
                    if (currentHour > startHour || (currentHour === startHour && currentMinute >= 0)) {
                        slotSelect.options[i].disabled = true;
                    }
                }
            }
        });

        //for displaying data in timetable form
        document.addEventListener("DOMContentLoaded", function() {
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