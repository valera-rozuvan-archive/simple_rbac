<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep 15 19:22:27 EEST 2012
 * 
 * File:      userInfo.php
 * Full path: protected/modules/simple_rbac/views/admin/userInfo.php
 *
 * Description: Will show a table of all user related information.
 */
?>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
         'dataProvider' => $userInfoDP,
         'columns' => array(
             array(
                 'name' => 'Attribute',
                 'value' => '$data["attribute"]',
             ),
             array(
                 'name' => 'Value',
                 'value' => '$data["value"]',
             ),
         ),
    )
);
?>
