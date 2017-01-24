<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require 'include.php';

global $wpdb;
global $form_table;
$myrows = $wpdb->get_results( "SELECT * FROM $form_table", OBJECT);
?>

<div class="col-sm-12">
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
        <th>
            نام فرم
        </th>
        <th>
            کد
        </th>
        <th>
            عملیات
        </th>
        </thead>
        <tbody>
        <?php foreach ($myrows as $row): ?>
            <tr>
               <td><?php echo $row->name; ?></td>
               <td><input type="text" style="width:100%; direction: ltr;" onfocus="this.select()" readonly="readonly" value="<?php echo "[dyon_bank_getway_form id=".$row->id."]"; ?>"></td>
               <td>
                   <a href="admin.php?page=newbankgetwaymenu&action=edit&id=<?php echo $row->id;?>">تغییر</a> -
                   <a href="#" onclick="if(confirm('آیا از حذف این فرم مطمئن هستید؟')){window.location ='admin.php?page=fromsgetwaymenu&action=remove&id=<?php echo $row->id;?>'}">حذف</a>
               </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>