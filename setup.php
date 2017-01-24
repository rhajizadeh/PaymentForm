<?php
add_action('admin_menu', 'bank_getway_settings_menu');

function bank_getway_settings_menu(){
    add_menu_page("درگاه پرداخت", "درگاه پرداخت (دایون)", 'manage_options', 'dyonbankgetwaymenu', 'call_new_paymeny_form');
    add_submenu_page("dyonbankgetwaymenu", "درگاه پرداخت (دایون)", "جدید", 'manage_options', 'newbankgetwaymenu', 'call_new_paymeny_form');
    add_submenu_page("dyonbankgetwaymenu", "پرداخت ها", "لیست پرداخت ها", 'manage_options', 'paymentlist', 'call_payment_list');
    add_submenu_page("dyonbankgetwaymenu", "تنظیمات", "تنظیمات", 'manage_options', 'getway_settings', 'call_payment_settings');
}
function call_new_paymeny_form() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    require get_template_directory() . '/bank/new_payment_page.php';
}
function call_payment_list() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    require get_template_directory() . '/bank/payments.php';
}
function call_payment_settings() {
    if ( !current_user_can( 'manage_options' ) )  {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    require get_template_directory() . '/bank/settings.php';
}