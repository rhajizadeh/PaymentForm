<?php
get_header();

$form_table = $wpdb->prefix . '_dy_form_table';
$form_entry_table = $wpdb->prefix . '_dy_form_entry_table';
$payment_shaparak_detail_table = $wpdb->prefix . '_dy_form_payment_shaparak_detail_table';

global $wpdb;
$error = "فراخوانی اشتباه";
if(isset($_POST["form_id"])){
    $form_id = $_POST["form_id"];
    $row = $wpdb->get_row("SELECT * FROM $form_table WHERE id=$form_id");
    $cols = explode("\n",$row->columns);
    $data = array();
    foreach ($cols as $col){
        $col_name = trim(explode(":", $col)[1]);
        $data[$col_name] = $_POST[$col_name];
    }
    $amount = intval($_POST["amount"]);
    if($amount < 1000)
        $error = "مبلغ اشتباه است. حداقل مقدار قابل قبول 10,000 ریال می باشد.";
    else {
        $wpdb->insert($form_entry_table,
            array(
                "form_id" => $form_id,
                "detail" => serialize($data),
                "time" => date("Y-m-d H:i:s")
            ));
        $error = "";
    }
}


$shaparak_settings_option = "dyon_bank_getway_shaparak_settings";
$shaparak_settings = unserialize(get_option($shaparak_settings_option));

$return_url_page_id = intval(get_option("dyon_bank_getway_return_page_id"));
?>

<div class="container" style="height: 300px;">
    <br><br><br>
    <?php if($error == ""):?>
    <p style="font-size: 18px; text-align: center;">
        <i class="fa fa-spinner fa-spin" style="font-size: 90px;"></i><br><br>
        در حال هدایت به درگاه پرداخت
    </p>
        <form method="post" id="goto_getway_form" action="<?php echo $shaparak_settings["url"]; ?>">
            <input type="hidden" name="MID" value="<?php echo $shaparak_settings["mid"]; ?>">
            <input type="hidden" name="ResNum" value="<?php echo $wpdb->insert_id; ?>">
            <input class="donation_amount" id="Amount" name="Amount" value="<?php echo $_POST["amount"];?>" type="hidden">
            <input id="RedirectURL" name="RedirectURL" value="<?php echo get_permalink("$return_url_page_id");?>" type="hidden">
        </form>
        <script>
            setTimeout(function(){
                document.getElementById('goto_getway_form').submit();
            }, 500);

        </script>
    <?php else: ?>
    <div class="alert alert-danger"><?php echo "خطا:".$error; ?></div>
    <?php endif; ?>

    <br><br><br>
</div>

<?php get_footer(); ?>
