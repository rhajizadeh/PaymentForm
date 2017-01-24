<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require_once 'include.php';
if(!function_exists('jdate')){
    require_once( plugin_dir_path(__FILE__) .'lib/jdf.php');
}

$sql = "SELECT id, name, columns FROM $form_table";
$forms = $wpdb->get_results($sql);

$form_id = -1;
if(isset($_GET["id"])){
    $form_id = $_GET["id"];
}
$sql = "SELECT
$form_entry_table.id,
$payment_shaparak_detail_table.time,
$form_entry_table.detail,
$payment_shaparak_detail_table.ref_num,
$payment_shaparak_detail_table.secure_pan,
$payment_shaparak_detail_table.amount,
$payment_shaparak_detail_table.state
FROM $form_entry_table 
INNER JOIN $form_table ON $form_table.id = $form_entry_table.form_id
LEFT OUTER JOIN $payment_shaparak_detail_table ON $form_entry_table.id = $payment_shaparak_detail_table.entery_id";

$condition = array();
$todate = "";
$fromdate = "";
$selectedformId = $forms[0]->id;
$type = "success";

if(isset($_POST["action"]) && $_POST["action"] == "filter"){
    if($_POST["tr-form"] != -1){
        $selectedformId = $_POST["tr-form"];
    }
    if($_POST["tr-type"] == "success" ) {
        $type = "success";
    } else if($_POST["tr-type"] == "failure" ) {
        $type = "failure";
    }
    if($_POST["fromdate"] != ""){
        $fromdate = $_POST["fromdate"];
        $date_elements = explode("/", $fromdate);
        if(count($date_elements) == 3) {
            $gregorian_els = jalali_to_gregorian($date_elements[0], $date_elements[1], $date_elements[2]);
            if(strlen($gregorian_els[1]) == 1) $gregorian_els[1] = "0".$gregorian_els[1];
            if(strlen($gregorian_els[2]) == 1) $gregorian_els[2] = "0".$gregorian_els[2];
            $str = "$gregorian_els[0]-$gregorian_els[1]-$gregorian_els[2] 00:00:00";
            array_push($condition, "$payment_shaparak_detail_table.time >= '$str'");
        }
    }
    if($_POST["todate"] != ""){
        $todate = $_POST["todate"];
        $date_elements = explode("/", $todate);
        if(count($date_elements) == 3) {
            $gregorian_els = jalali_to_gregorian($date_elements[0], $date_elements[1], $date_elements[2]);
            if(strlen($gregorian_els[1]) == 1) $gregorian_els[1] = "0".$gregorian_els[1];
            if(strlen($gregorian_els[2]) == 1) $gregorian_els[2] = "0".$gregorian_els[2];
            $str = "$gregorian_els[0]-$gregorian_els[1]-$gregorian_els[2] 00:00:00";
            array_push($condition, "$payment_shaparak_detail_table.time <= '$str'");
        }
    }
} else{
}

array_push($condition, "$form_table.id = $selectedformId");
if($type == "success" ) {
    array_push($condition, "$payment_shaparak_detail_table.amount > 0");
} else if($type == "failure" ) {
    array_push($condition, "$payment_shaparak_detail_table.amount <= 0");
}
if(count($condition) > 0){
    $condition_str = join(" AND ", $condition);
    $sql .= " WHERE $condition_str";
}
$sql .= " ORDER BY $payment_shaparak_detail_table.time DESC";

$rows = $wpdb->get_results($sql);
$count = 0;

foreach ($forms as $form){
    if($form->id == $selectedformId) {
        $details = explode("\n", $form->columns);
        $cols_name = array();
        $cols_key = array();

        foreach ($details as $detail) {
            $columns_elements = explode(":", $detail);
            array_push($cols_key, trim($columns_elements[1]));
            array_push($cols_name, trim($columns_elements[0]));
        }
        break;
    }
}

$totalAmount = 0;
foreach ($rows as $row){
    $am = intval($row->amount);
    if($am > 0)
        $totalAmount += $am;
}

?>
<div class="col-sm-12">
    <br/>
    <form action="" method="post">
        <div class="col-sm-6">
            <div class="form-group">
                <label for="fromdate">از تاریخ:</label>
                <input type="date" name="fromdate" id="fromdate"  placeholder="1390/01/01" value="<?php echo $fromdate;?>">
            </div>
            <div class="form-group">
                <label for="todate"> تا تاریخ:</label>
                <input type="date" name="todate" id="todate" placeholder="1395/01/01"  value="<?php echo $todate;?>">
            </div>
            <input type="hidden" name="action" value="filter">
            <button class="btn btn-default" type="submit">اعمال فیلتر</button>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <select name="tr-form">
                    <?php foreach ($forms as $form): ?>
                        <option <?php echo $selectedformId == $form->id ? "selected" : ""; ?> value="<?php echo $form->id;?>"><?php echo $form->name;?> </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <select name="tr-type">
                    <option <?php echo $type == "selected" ? "selected" : ""; ?> value="success">تراکنش های موفق</option>
                    <option <?php echo $type == "failure" ? "selected" : ""; ?> value="failure">تراکنش های ناموفق</option>
                </select>
            </div>
        </div>
    </form>
    <br/>
    <div class="label label-primary">
        <?php echo " جمع مقدار واریزی ها: $totalAmount ریال";  ?>
    </div>
    <div class="col-sm-12">
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <th>ردیف</th>
            <?php foreach ($cols_name as $name): ?>
                <th><?php echo $name; ?></th>
            <?php endforeach; ?>
            <th>وضعیت</th>
            <th>مقدار</th>
            <th>شماره پیگیری</th>
            <th>کارت</th>
            <th>تاریخ</th>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <?php $details = unserialize($row->detail); ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <?php foreach ($cols_key as $key): ?>
                    <td><?php echo $details[trim($key)];?></td>
                <?php endforeach; ?>
                <td><?php echo Translate_state($row->state); ?></td>
                <td><?php echo $row->amount; ?></td>
                <td><?php echo $row->ref_num; ?></td>
                <td><?php echo $row->secure_pan; ?></td>
                <td><?php echo mysql2date('l, F j, Y', $row->time); ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

</div>