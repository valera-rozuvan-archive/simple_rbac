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
$password = '1234';
?>

Hello, world! from simple_rbac module, default controller, index action.<br />
<br />
Password: <?=$password?><br />
Hashed password: <?=crypt($password)?><br />
<br />
Current user ID is <?=Yii::app()->user->getId()?><br />
<br />
Roles:<br />
<?=print_r(array_keys(Yii::app()->authManager->roles), true)?><br />
<br />
Default roles:<br />
<?=print_r(Yii::app()->authManager->defaultRoles, true)?><br />
<br />
Is guest: <?=(Yii::app()->authManager->checkAccess('guest', Yii::app()->user->getId())) ? 'true' : 'false'?><br />
<br />
Is authenticated: <?=(Yii::app()->authManager->checkAccess('authenticated', Yii::app()->user->getId())) ? 'true' : 'false'?><br />
<br />
Is admin: <?=(Yii::app()->authManager->checkAccess('admin', Yii::app()->user->getId())) ? 'true' : 'false'?><br />
<br />
