<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require 'include.php';
$error = "";
$success = "";

$form_name = "";
$form_code = "<div class='form-group'>
    <label for='cname'>نام و نام خانوادگی</label>
    <input id='cname' class='form-control' name='cname' type='text'>
</div>
<div class='form-group'>
    <label for='email'>ایمیل</label>
    <input id='email' class='form-control' name='email' type='text'>
</div>
<div class='form-group'>
    <label for='tel'>شماره تماس</label>
    <input id='tel' class='form-control' name='tel' type='text'>
</div>
  <div class='form-group'>
    <label for='amount'>مبلغ کمک(ریال)</label>
    <input id='tel' class='form-control' name='amount' type='text'>
</div>";

$cols = "نام و نام خانوادگی:cname
ایمیل:email
شماره تماس:tel
مبلغ کمک:amount";


$cta_btn = "ارسال";
$cta_btn_class = "btn btn-default";
$after_payment_content = "";
$email_title = "";
$email_from = "info@".str_replace("http://","",site_url());

$email_content = "";

if(isset($_POST["action"])){
    if($_POST["action"] == "new_form") {
        $form_name = trim(stripslashes($_POST["form_name"]));
        $form_code = trim(stripslashes($_POST["form_code"]));
        $cols = trim(stripslashes($_POST["cols"]));
        $email_title = trim(stripslashes($_POST["email_title"]));
        $email_from = trim(stripslashes($_POST["email_from"]));
        $email_content = trim(stripslashes($_POST["email_content"]));
        $cta_btn = trim(stripslashes($_POST["cta_btn"]));
        $cta_btn_class = trim(stripslashes($_POST["cta_btn_class"]));
        $after_payment_content = trim(stripslashes($_POST["after_payment_content"]));

        if ($form_name == "") {
            $error = "لطفا نام فرم را وارد کنید.";
        } else if ($form_code == "") {
            $error = "لطفا کد فرم را وارد کنید.";
        } else if ($cols == "") {
            $error = "لطفا ستون ها را وارد کنید.";
        } else if ($cols == "") {
            $error = "لطفا ستون ها را وارد کنید.";
        } else if ($email_from == "") {
            $error = "لطفا ایمیل را وارد کنید.";
        } else if (!filter_var($email_from, FILTER_VALIDATE_EMAIL)) {
            $error = "لطفا ایمیل را درست وارد کنید.";
        }
        if ($error == "") {
            $data = array("name" => $form_name,
                "formhtml" => $form_code,
                "columns" => $cols,
                "email_from" => $email_from,
                "email_content" => $email_content,
                "email_title" => $email_title,
                "cta_btn" => $cta_btn,
                "cta_btn_class" => $cta_btn_class,
                "after_payment_content" => $after_payment_content
            );
            $wpdb->insert($form_table, $data);
            $success = "فرم با موفقیت ایجاد گردید." ;
        }
    } else if($_POST["action"] == "edit"){
        $form_name = trim(stripslashes($_POST["form_name"]));
        $form_code = trim(stripslashes($_POST["form_code"]));
        $cols = trim(stripslashes($_POST["cols"]));
        $email_title = trim(stripslashes($_POST["email_title"]));
        $email_from = trim(stripslashes($_POST["email_from"]));
        $email_content = trim(stripslashes($_POST["email_content"]));
        $cta_btn = trim(stripslashes($_POST["cta_btn"]));
        $cta_btn_class = trim(stripslashes($_POST["cta_btn_class"]));
        $after_payment_content = trim(stripslashes($_POST["after_payment_content"]));

        if ($form_name == "") {
            $error = "لطفا نام فرم را وارد کنید.";
        } else if ($form_code == "") {
            $error = "لطفا کد فرم را وارد کنید.";
        } else if ($cols == "") {
            $error = "لطفا ستون ها را وارد کنید.";
        } else if ($cols == "") {
            $error = "لطفا ستون ها را وارد کنید.";
        } else if ($email_from == "") {
            $error = "لطفا ایمیل را وارد کنید.";
        } else if (!filter_var($email_from, FILTER_VALIDATE_EMAIL)) {
            $error = "لطفا ایمیل را درست وارد کنید.";
        }
        if ($error == "") {
            $editing_id = $_POST["editing_id"];
            $data = array("name" => $form_name,
                "formhtml" => $form_code,
                "columns" => $cols,
                "email_from" => $email_from,
                "email_content" => $email_content,
                "email_title" => $email_title,
                "cta_btn" => $cta_btn,
                "cta_btn_class" => $cta_btn_class,
                "after_payment_content" => $after_payment_content
            );
            $wpdb->update($form_table, $data, array('id' => $editing_id));
            $success = "فرم با موفقیت بروز رسانی گردید.";
        }
    }
}
if(isset($_GET["action"])) {
    if ($_GET["action"] == "edit") {
        $editing_id = $_GET["id"];
        $row = $wpdb->get_row("SELECT * FROM $form_table WHERE id=$editing_id");
        $form_name = $row->name;
        $form_code = $row->formhtml;
        $cols = $row->columns;
        $email_title = $row->email_title;
        $email_from = $row->email_from;
        $email_content = $row->email_content;
        $after_payment_content = $row->after_payment_content;
        $cta_btn = $row->cta_btn;
        $cta_btn_class = $row->cta_btn_class;
    }
}

?>
<style>
    .nav-tabs li{float: right;}
</style>
<div class="containter-fluid">
    <div class="col-sm-12">
        <?php if($success != ""): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php else: ?>
        <?php if($error != ""):?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>

        <strong>ایجاد فرم پرداخت جدید</strong>
        <br>
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#overall">مشخصات کلی </a></li>
            <li><a data-toggle="tab" href="#email"> ایمیل پس از پرداخت</a></li>
            <li><a data-toggle="tab" href="#after_payment">صفحه پس از پرداخت</a></li>
        </ul>

        <form method="post">
        <div class="tab-content">
            <div id="overall" class="tab-pane fade in active">
                <div class="form-group">
                    <label for="form_name">نام درگاه</label>
                    <input id="form_name" class="form-control" name="form_name" type="text" value="<?php echo $form_name;?>">
                </div>

                <div class="form-group">
                    <label for="form_code">کد html فرم</label><br>
                    <div style="direction: ltr;">
                    <?php echo htmlspecialchars("<form method='post'>");?><br>
                    <textarea id="form_code" class="form-control" name="form_code" style="min-height: 400px;direction: ltr;"><?php echo $form_code;?></textarea>
                    <?php echo htmlspecialchars("<button type='submit' class='").
                        "<input type='text' name='cta_btn_class' value='$cta_btn_class'>".
                        htmlspecialchars("'>").
                        "<input type='text' name='cta_btn' value='$cta_btn'>".htmlspecialchars("</button></form>");?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="cols">ستون ها(نام:column name) لطفا نام ستون خود را name نگذارید، برای وردپرس مشکل ایجاد می کند</label>
                    <textarea id="cols" class="form-control" name="cols" style="min-height: 150px;direction: ltr;"><?php echo $cols;?></textarea>
                    </div>
            </div>
            <div id="email" class="tab-pane fade in">
                <strong>ایمیل پس از پرداخت</strong>
                <br>
                <div class="form-group">
                    <label for="email_from">از آدرس</label>
                    <input type="email_from" name="email_from" id="email_from" class="form-control" value="<?php echo $email_from;?>">
                 </div>
                <div class="form-group">
                    <label for="email_title">عنوان ایمیل</label>
                    <input type="text" name="email_title" id="email_title" class="form-control" value="<?php echo $email_title;?>">
                </div>
                <div class="form-group">
                    <label for="email_content">محتوی ایمیل (html)</label>
                    <?php wp_editor( $email_content, "email_content",array( 'media_buttons' => false,
                        'quicktags' => true, 'teeny'=>true, 'textarea_name' => "email_content","textarea_rows"=>15)); ?>
                </div>
            </div>
            <div id="after_payment" class="tab-pane fade in">
                <div class="form-group">
                    <label for="email_content">محتوی صفحه پس از پرداخت</label>
                    <?php wp_editor( $after_payment_content, "after_payment_content",array('textarea_name' => "after_payment_content","textarea_rows"=>15)); ?>
                </div>

            </div>
            <?php if(isset($_GET["action"]) && $_GET["action"] == "edit"): ?>
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="editing_id" value="<?php echo $editing_id; ?>">
                <button type="submit" class="btn btn-primary">تغییر فرم</button>
            <?php else: ?>
                <input type="hidden" name="action" value="new_form">
                <button type="submit" class="btn btn-primary">ایجاد فرم</button>
            <?php endif; ?>
        </form>
    </div>
</div>