<?php
session_start();
include('../clients/connect.php');

if (isset($_GET['anntitle']) && isset($_GET['anndate'])) {
    $title = $_GET['anntitle'];
    $date = $_GET['anndate'];
?>
    <!DOCTYPE html>
    <html>
    <div id="ann-detail-form" class="ann-detail-form">
        <?php
        $query = "SELECT * FROM [announcement] WHERE [ann_title] = ? AND [created_at] = ?";
        $array = [$title, $date];
        $statement = sqlsrv_query($conn, $query, $array);

        while ($row = sqlsrv_fetch_array($statement, SQLSRV_FETCH_ASSOC)) {
        ?>
            <button type="button" class="cancel" onclick="closeDetail()"><i class="fa fa-remove"></i></button>
            <div class="header">
                <h3><?php echo $row['ann_title'] ?></h3>
            </div>

            <div class="show-ann-text">
                <?php echo html_entity_decode($row['ann_detail']); ?>
            </div>
        <?php } ?>
    </div>

    </html>
<?php
} ?>