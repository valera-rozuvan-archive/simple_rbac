<?php
/*
 * Author    Valera Rozuvan
 * Created:  Fri Sep  7 06:30:43 EEST 2012
 * 
 * File:      permissions.php
 * Full path: protected/modules/simple_rbac/views/admin/permissions.php
 *
 * Description: This view will display a list of available privileges.
 */
?>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
         'dataProvider' => $permissionsDP,
    )
);
?>
