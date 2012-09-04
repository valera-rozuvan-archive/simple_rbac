<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Aug 25 17:08:41 EEST 2012
 * 
 * File:      index.php
 * Full path: protected/modules/simple_rbac/views/admin/index.php
 *
 * Description: The main view of the Simple RBAC module. Will provide links to the various actions available (user
 * creation, role management, ...).
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
