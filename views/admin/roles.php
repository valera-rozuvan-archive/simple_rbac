<?php
/*
 * Author    Valera Rozuvan
 * Created:  Fri Sep  7 06:23:32 EEST 2012
 * 
 * File:      roles.php
 * Full path: protected/modules/simple_rbac/views/admin/roles.php
 *
 * Description: A view to display the currently available rows.
 */
?>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
         'dataProvider' => $rolesDP,
    )
);
?>
