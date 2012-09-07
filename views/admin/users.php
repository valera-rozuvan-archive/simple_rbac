<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Aug 25 17:08:41 EEST 2012
 * 
 * File:      users.php
 * Full path: protected/modules/simple_rbac/views/admin/users.php
 *
 * Description: The main view of the Simple RBAC module. Will provide links to the various actions available (user
 * creation, role management, ...).
 */
?>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
        'dataProvider' => $dataProvider,
        'columns' => array(
            'id', 'username', 'status', 'last_access', 'registered',
        ),
    )
);
?>
