<?php
get_header();

global $wpdb;
require_once(plugin_dir_path( __FILE__ ).'lib/nusoap.php');

$payment_shaparak_detail_table = $wpdb->prefix . '_dy_form_payment_sdt';

$state = trim($_POST["State"]);
$stateCode = intval($_POST["StateCode"]);
$refNum = trim($_POST["RefNum"]);
$amount = 0;
if(isset($_POST["Amount"]))
    $amount = trim($_POST["Amount"]);
$entryId = trim($_POST["ResNum"]);
$stateTr = Translate_state($state);
$CID = trim($_POST["CID"]);
$TRACENO = trim($_POST["TRACENO"]);
$RRN = trim($_POST["RRN"]);
$SecurePan = trim($_POST["SecurePan"]);

$form_table = $wpdb->prefix . '_dy_form_table';
$form_entry_table = $wpdb->prefix . '_dy_form_entry_table';
$query = "SELECT $form_entry_table.id,
$form_entry_table.form_id,
$form_entry_table.detail,
$form_table.after_payment_content,
$form_table.email_from,
$form_table.email_title,
$form_table.email_content FROM $form_entry_table INNER JOIN $form_table on $form_table.id = $form_entry_table.form_id WHERE $form_entry_table.id=$entryId";

$row = $wpdb->get_row($query);

$success = "";
$error = "";

if($refNum != ""){
    $query = "SELECT COUNT(*) FROM $payment_shaparak_detail_table WHERE `ref_num` = '$refNum'";
    $res = $wpdb->get_var($query);
    if($res > 0){
        $error = "این شماره پیگیری قبلا موجود است.";
    } else {
        $amount = verifyShaparakTransaction($refNum, unserialize(get_option("dyon_bank_getway_shaparak_settings"))["mid"]);
       $email = "";
        $entery_details = unserialize($row->detail);
        if(isset($form_entry_table["email"])){
            sendEmail($form_entry_table["email"], $row->email_from, $row->email_title, $row->email_content);
        }

    }
}

//if($state != "" && $error == "" && $amount >= 0) {
    $nowFormat = date("Y-m-d H:i:s");
    $query = "SELECT COUNT(*) FROM $payment_shaparak_detail_table WHERE entery_id=$entryId";
    $count =  $wpdb->get_var($query);
    if($count==0) {
        $wpdb->insert($payment_shaparak_detail_table,
            array('entery_id' => $entryId,
                'state' => $state,
                'state_code' => $stateCode,
                'ref_num' => $refNum,
                'amount' => $amount,
                'cid' => $CID,
                'traceno' => $TRACENO,
                'rrn' => $RRN,
                'secure_pan' => $SecurePan,
                'time' => $nowFormat)
        );
    } else {
        $error = "فرم ارسالی تکراری است.";
    }
//}
if($amount <= 0)
    $error = $stateTr;
else{
    $success = "پرداخت شما موفقیت آمیز بود.";
}

?>

<br>
<div class="container">
    <?php if($success != ""): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
        <div class="container-fluid">
            <?php echo $row->after_payment_content; ?>
        </div>
    <?php endif; ?>
    <?php if($error != ""): ?>
    <div class="alert alert-danger">
        <strong>
تراکنش موفقیت آمیز نبود بدلیل:
        </strong>
        <br><?php echo $error; ?>
        </div>
    <?php endif; ?>

</div>
<?php get_footer(); ?>
