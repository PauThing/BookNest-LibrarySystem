<?php
session_start();
include('../clients/connect.php');

if (isset($_GET['annid'])) {
    $annid = $_GET['annid'];
?>
    <!DOCTYPE html>
    <html>
    <div id="ann-detail-form" class="ann-detail-form">
        <?php
        $query = "SELECT * FROM [announcement] WHERE [ann_id] = ?";
        $array = [$annid];
        $statement = sqlsrv_query($conn, $query, $array);

        while ($row = sqlsrv_fetch_array($statement, SQLSRV_FETCH_ASSOC)) {
        ?>
            <button type="button" class="cancel" onclick="closeDetail()"><i class="fa fa-remove"></i></button>
            
            <br />
            
            <div class="header">
                <h3><?php echo $row['ann_title'] ?></h3>
            </div>

            <br />
            
            <div class="show-ann-text">
                <?php echo html_entity_decode($row['ann_detail']); ?>
            </div>
        <?php } ?>
    </div>

    </html>
<?php
} ?>