<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require 'include.php';
$selectedReturnPageId = -1;
$selectedGuideToGetwayPageId = -1;
if(get_option($shaparak_settings_option) == false){
    echo "Problem loading option $shaparak_settings_option";
} else {
    if(isset($_POST["action"]) && $_POST["action"] == "edit") {
        $shaparak_settings = array("mid" => $_POST["shapark-MID"], "url" => $_POST["shapark-URL"]);
        $selectedReturnPageId = $_POST["return_page_id"];
        $selectedGuideToGetwayPageId = $_POST["guide_to_getway_page_id"];
        update_option($shaparak_settings_option, serialize($shaparak_settings));
        update_option($return_page_id_settings, $selectedReturnPageId);
        update_option($guide_to_getway_page_id_settings, $selectedGuideToGetwayPageId);
    } else {
        $shaparak_settings = unserialize(get_option($shaparak_settings_option));
        $selectedReturnPageId = intval(get_option($return_page_id_settings));
        $selectedGuideToGetwayPageId = intval(get_option($guide_to_getway_page_id_settings));
    }
}
?>
<style>
    .col-sm-1, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9{
        float: right;
    }
</style>

<div class="col-sm-8" style="margin-top: 50px">
    <form method="post">
        <div class="panel panel-default">
            <div class="panel-heading">تنظیمات شاپرک</div>
            <div class="panel-body">
                    <div class="form-group">
                        <label for="name">کد پذیرندگی (MID):</label>
                        <input style="direction: ltr;" class="form-control" name="shapark-MID" id="shaparak-MID" value="<?php echo $shaparak_settings["mid"]; ?>" type="number">
                    </div>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="name">آدرس URL درگاه:</label>
                    <input style="direction: ltr;" class="form-control" name="shapark-URL" id="shaparak-URL" type="text" value="<?php echo $shaparak_settings["url"]; ?>">
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">سایر تنظیمات</div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="success_return_page_id">صفحه بازگشت:</label>
                    <?php wp_dropdown_pages( array('name' => 'return_page_id', 'id'=>'return_page_id', 'selected' => $selectedReturnPageId) ); ?>
                </div>
                <div class="form-group">
                    <label for="success_return_page_id">صفحه هدایت به درگاه:</label>
                    <?php wp_dropdown_pages( array('name' => 'guide_to_getway_page_id', 'id'=>'guide_to_getway_page_id', 'selected' => $selectedGuideToGetwayPageId) ); ?>
                </div>
            </div>
        </div>
        <input type="hidden" value="edit" name="action">
        <button type="submit" class="btn btn-primary">اعمال</button>
    </form>
</div>
<div class="col-sm-4" style="margin-top: 50px">
    <div class="panel panel-primary">
        <div class="panel-heading">
            در مورد این افزونه
        </div>
        <div class="panel-body">
        این افزونه توسط روزبه حاجی زاده نوشته شده است.<br>
        در صورت بروز مشکل با من تماس بگیرید.<br>
        <a href="mailto:hajizadeh.roozbeh@gmail.com">hajizadeh.roozbeh@gmail.com</a>
            </div>
    </div>
</div>