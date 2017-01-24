<?php
/*
Plugin Name: Dyon Bank Getway Wp Plugin
Plugin URI: http://dyon.ir
Description: این افزونه درگاه پرداخت به همراه فرم پرداخت برایتان فراهم می آورد.
Version: 1.0
Author: Roozbeh Hajizadeh
Author URI: http://dyon.ir
License: LGPL2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


add_action('admin_menu', 'bank_getway_settings_menu');

function bank_getway_settings_menu(){
    add_menu_page("درگاه پرداخت", "درگاه پرداخت (دایون)", 'manage_options', 'dyonbankgetwaymenu', 'call_new_paymeny_form');
    add_submenu_page("dyonbankgetwaymenu", "درگاه پرداخت (دایون)", "جدید", 'manage_options', 'newbankgetwaymenu', 'call_new_paymeny_form');
    add_submenu_page("dyonbankgetwaymenu", "فرم های پرداخت", "فرم ها", 'manage_options', 'fromsgetwaymenu', 'call_paymeny_forms_list');
    add_submenu_page("dyonbankgetwaymenu", "پرداخت ها", "لیست پرداخت ها", 'manage_options', 'paymentlist', 'call_payment_list');
    add_submenu_page("dyonbankgetwaymenu", "تنظیمات", "تنظیمات", 'manage_options', 'getway_settings', 'call_payment_settings');
}
function call_new_paymeny_form() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    require plugin_dir_path( __FILE__ ) . '/new_payment_page.php';
}
function call_payment_list() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    require plugin_dir_path( __FILE__ ) . '/payments.php';
}
function call_paymeny_forms_list() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    require plugin_dir_path( __FILE__ ) . '/forms.php';
}
function call_payment_settings() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    require plugin_dir_path( __FILE__ ) . '/settings.php';
}

add_filter( 'page_template', 'after_donation_template' );
function after_donation_template( $page_template )
{
    $return_page_id = get_option("dyon_bank_getway_return_page_id");
    $guide_to_getway_page_id_settings = get_option("dyon_bank_guide_to_getway_page_id");
    $return_page_id = intval($return_page_id);
    $guide_to_getway_page_id_settings = intval($guide_to_getway_page_id_settings);
    if ($return_page_id != -1){
        if(is_page($return_page_id)) {
            $page_template = dirname(__FILE__) . '/after_payment.php';
        }
        else if(is_page($guide_to_getway_page_id_settings)) {
            $page_template = dirname(__FILE__) . '/guide_to_getway.php';
        }
    }
    return $page_template;
}



add_shortcode( 'dyon_bank_getway_form', 'dyon_bank_getway_form_maker' );

function dyon_bank_getway_form_maker($atts){
    $a = shortcode_atts( array(
        'id' => ''
    ), $atts );
    $form_id = $a["id"];
    global $wpdb;
    global $form_table;
    global $guide_to_getway_page_id_settings;

    $pgid = intval(get_option($guide_to_getway_page_id_settings));
    $row = $wpdb->get_row("SELECT * FROM $form_table WHERE id=$form_id");
    $output = "<form method='post' action='". get_permalink($pgid).
        "'>".$row->formhtml.
        "<input type='hidden' name='form_id' value='$row->id'><button type='submit' class='$row->cta_btn_class'>$row->cta_btn</button> </form>";
    echo $output;
}


/**
 * Create tables
 */

register_activation_hook( __FILE__, 'dyon_db_install' );

$form_table = $wpdb->prefix . '_dy_form_table';
$form_entry_table = $wpdb->prefix . '_dy_form_entry_table';
$payment_shaparak_detail_table = $wpdb->prefix . '_dy_form_payment_sdt';

$shaparak_settings_option = "dyon_bank_getway_shaparak_settings";
$return_page_id_settings = "dyon_bank_getway_return_page_id";
$guide_to_getway_page_id_settings = "dyon_bank_guide_to_getway_page_id";


addShaparakSettingsOption();

function dyon_db_install()
{
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $form_table = $wpdb->prefix . '_dy_form_table';
    $form_entry_table = $wpdb->prefix . '_dy_form_entry_table';
    $payment_shaparak_detail_table = $wpdb->prefix . '_dy_form_payment_sdt';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $form_table (
id int(11) NOT NULL AUTO_INCREMENT,
time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
name text NOT NULL,
formhtml text NOT NULL,
columns text NOT NULL,
cta_btn text NOT NULL,
cta_btn_class text NOT NULL,
after_payment_content text NOT NULL,
email_from text DEFAULT '' NOT NULL,
email_title text DEFAULT '' NOT NULL,
email_content text DEFAULT '' NOT NULL,
PRIMARY KEY (id)
) $charset_collate;";
    dbDelta($sql);

    $sql = "CREATE TABLE $form_entry_table (
id int(11) NOT NULL AUTO_INCREMENT,
form_id int(11) NOT NULL,
detail text DEFAULT '' NOT NULL,
time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
PRIMARY KEY (id)
) $charset_collate;";
    dbDelta($sql);



    $sql = "CREATE TABLE $payment_shaparak_detail_table (
id int(11) NOT NULL AUTO_INCREMENT,
entery_id int(11) NOT NULL,
state text DEFAULT '' NOT NULL,
state_code int(11) NOT NULL,
traceno text DEFAULT '' NOT NULL,
ref_num text DEFAULT '' NOT NULL,
amount text DEFAULT '' NOT NULL,
res_num text DEFAULT '' NOT NULL,
cid text DEFAULT '' NOT NULL,
rrn text DEFAULT '' NOT NULL,
secure_pan text DEFAULT '' NOT NULL,
time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
PRIMARY KEY (id)
) $charset_collate;";
    dbDelta($sql);

    add_option( "dyon_bank_getway_db_version", "0.7" );
    add_option( "dyon_bank_getway_return_page_id", -1 );
    add_option( "dyon_bank_guide_to_getway_page_id", -1 );

    register_uninstall_hook( __FILE__, 'dyon_db_uninstall' );

}

function dyon_db_uninstall(){
    global $wpdb;

    $shaparak_settings_option = "dyon_bank_getway_shaparak_settings";
    $form_table = $wpdb->prefix . '_dy_form_table';
    $form_entry_table = $wpdb->prefix . '_dy_form_entry_table';
    $payment_shaparak_detail_table = $wpdb->prefix . '_dy_form_payment_sdt';

    delete_option("dyon_bank_getway_db_version");
    delete_option("dyon_bank_getway_return_page_id");
    delete_option("dyon_bank_guide_to_getway_page_id");
    delete_option($shaparak_settings_option);
    $sql = "DROP TABLE $form_table";
    $wpdb->query($sql);
    $sql= "DROP TABLE $form_entry_table";
    $wpdb->query($sql);
    $sql="DROP TABLE $payment_shaparak_detail_table";
    $wpdb->query($sql);
}


function addShaparakSettingsOption(){
    global $shaparak_settings_option;
    if(get_option($shaparak_settings_option) == false){
        $shaparak_default_settings = array("mid"=>"", "url" => "https://sep.shaparak.ir/payment.aspx");
        add_option( $shaparak_settings_option, serialize($shaparak_default_settings));
    }
}

function Translate_state($state)
{
    $tmess="";
    switch ($state) {
        case "Canceled By User":
            $tmess = "تراکنش توسط خریدار کنسل شده است.";
            break;
        case "Invalid Amount":
            $tmess = "مبلغ سند برگشتی، از مبلغ تراکنش اصلی بیشتر است.";
            break;
        case "Invalid Transaction":
            $tmess = "درخواست برگشت یک تراکنش رسیده است، درحالی که تراکنش اصلی پیدا نمی شود.";
            break;
        case "Invalid Card Number":
            $tmess = "شماره کارت اشتباه است.";
            break;
        case "No Such Issuer":
            $tmess = "چنین صادر کننده کارتی وجود ندارد.";
            break;
        case "Expired Card Pick Up":
            $tmess = "از تاریخ انقضای کارت گذشته است و کارت دیگر معتبر نیست.";
            break;
        case "Allowable PIN Tries Exceeded Pick Up":
            $tmess = "رمز کارت (PIN) 3 مرتبه اشتباه وارد شده است در نتیجه کارت غیر فعال خواهد شد.";
            break;
        case "Incorrect PIN":
            $tmess = "خریدار رمز کارت (PIN) را اشتباه وارد کرده است.";
            break;
        case "Exceeds Withdrawal Amount Limit":
            $tmess = "مبلغ برداشت پیش از سقف برداشت است.";
            break;
        case "Transaction Cannot Be Completed":
            $tmess = "تراکنش Authorize شده است ( شماره PIN و PAN درست هستند) ولی امکان سند خوردن وجود ندارد.";
            break;
        case "Response Received Too Late":
            $tmess = "تراکنش در شبکه بانکی Timeout خورده است.";
            break;
        case "Suspected Fraud Pick Up":
            $tmess = "خریدار یا فیلد CVV2 و یا فیلد ExpDate را اشتباه زده است( یا اصلا وارد نکرده است.)";
            break;
        case "No Sufficient Funds":
            $tmess = "موجودی به اندازه کافی در حساب وجود ندارد.";
            break;
        case "Issuer Down Slm":
            $tmess = "سیستم کارت بانک صادر کننده در وضعیت عملیاتی نیست.";
            break;
        case "TME Error":
            $tmess = "خطا در تراکنش";
            break;
        default:
            $tmess = "خطا ناشناخته در تراکنش";
    }
    return $tmess;
}

function verifyShaparakTransaction($refNum, $midNum){
    $wsdl = "https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL";
    $client = new nusoap_client($wsdl, 'wsdl');
    $soapProxy = $client->getProxy();
    $res = $soapProxy->VerifyTransaction($refNum, $midNum);

    return $res;
}
function sendEmail($to, $from, $subject, $content){

    $headers = array('From: '.$from, 'Content-Type: text/html; charset=UTF-8');

    wp_email($to, $subject, $content, $headers);
}
?>
