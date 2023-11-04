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
    <link rel="stylesheet" href="../clients/styles/onloanbook.css">

    <title>On Loan Books</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

    </script>
</head>

<body>
    <div class="big-container">
        <div id="onloan-list" class="onloan-list">
            <div class="onloan-container">
                <table id="onloan">
                    <thead>
                        <tr>
                            <th class="isbn">ISBN</th>
                            <th class="title">Book Title</th>
                            <th class="bdate">Borrow Date</th>
                            <th class="ddate">Due Date</th>
                            <th class="action">Renew</th>
                        </tr>
                    </thead>

                    <?php
                    $userid = $_SESSION['userid'];

                    //get the current URI
                    $currentURL = $_SERVER['REQUEST_URI'];

                    //check if the current URL already contains query parameters
                    $containsQuestionMark = (strpos($currentURL, '?') !== false);
                    $containsAmpersand = (strrpos($currentURL, '&') !== false);

                    $query = "SELECT
                            bh.*,
                            b.*
                        FROM [borrowinghistory] bh
                        LEFT JOIN [book] b ON bh.[ISBN] = b.[ISBN]
                        WHERE bh.user_id = '$userid' AND [status] = 'On Loan'
                        ORDER BY [due_at] ASC";

                    $statement = sqlsrv_query($conn, $query);

                    if ($statement === false) {
                        die(print_r(sqlsrv_errors(), true)); //print and handle the error
                    }

                    $norecord = true;
                    while ($row = sqlsrv_fetch_array($statement)) {
                        $borrow = $row['borrow_at'];
				        $brwDate = $borrow->format('Y-m-d');

                        $due = $row['due_at'];
                        $dueDate = $due->format('Y-m-d');
                    ?>
                        <tbody>
                            <tr>
                                <td><?php echo $row['ISBN']; ?></td>
                                <td><?php echo $row['book_title']; ?></td>
                                <td><?php echo $brwDate; ?></td>
                                <td><?php echo $dueDate; ?></td>
                                <td class="action"><a href="javascript:void(0);" class="renew" onclick="openForm('<?php echo $row['ISBN']; ?>', '<?php echo $_SESSION['userid']; ?>', '<?php echo $currentURL; ?>')"><i class="fa fa-refresh"></i></a></td>
                            </tr>
                        </tbody>
                    <?php
                        $norecord = false;
                    }

                    if ($norecord) {
                        echo "<tbody><tr><td colspan='5'>No records found.</td></tr></tbody>";
                    } ?>
                </table>
            </div>
        </div>
    </div>

    <div class="overlay-bg" id="overlay-bg"></div>

    <div class="renew-container" id="renew-container"></div>

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
        function openForm(isbn, userid, url) {
            $.ajax({
                type: 'GET',
                url: '../clients/renewbook.php',
                data: {
                    bisbn: isbn,
                    uid: userid,
                    cURL: url
                },
                success: function(response) {
                    $('#renew-container').html(response);
                    document.getElementById("renew-container").style.display = "block";
                    document.getElementById("overlay-bg").style.display = "block";
                },
                error: function() {
                    alert('Failed to load renew form.');
                }
            });
        }

        function closeForm() {
            document.getElementById("renew-container").style.display = "none";
            document.getElementById("overlay-bg").style.display = "none";
        }
    </script>
</body>

</html>