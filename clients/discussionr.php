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
        <div class="header">
            <h3>Discussion Room Daily Schedule</h3>
        </div>

        <div class="reserve-room" onclick="openForm()">
            <i class="fas fa-door-open"></i> Reserve a Room
        </div>

        <div class="view-reservation" onclick="openHistory()">
            <i class="fa fa-eye"></i> Reservation History
        </div>

        <div class="reservation-schedule"></div>
    </div>

    <div class="overlay-bg" id="overlay-bg"></div>

    <div id="reserve-room-container" class="reserve-room-container">
        <form id="reserve-room-form" class="reserve-room-form" method="post" action="../clients/backend/addreservationdb.php" enctype="multipart/form-data">
            <button type="button" class="cancel" onclick="closeForm()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3>Reserve Your Discussion Room</h3>
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
                </div>

                <br />

                <div class="SelectInput" data-mate-select="active">
                    <label for="slot">Time Slot</label> <br />
                    <select name="slot" id="slot" class="slot" required>
                        <option value="">Select a slot </option>
                        <option value="9.00 AM - 10.00 AM">9.00 AM - 10.00 AM</option>
                        <option value="10.00 AM - 11.00 AM">10.00 AM - 11.00 AM</option>
                        <option value="10.00 AM - 11.00 AM">10.00 AM - 11.00 AM</option>
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
        var slider = document.getElementById("member");
        var num = document.getElementById("num");
        num.innerHTML = slider.value; //display the default slider value

        //update the current slider value (each time you drag the slider handle)
        slider.oninput = function() {
            num.innerHTML = this.value;
        }
    </script>
</body>

</html>