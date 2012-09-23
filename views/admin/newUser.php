<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep  8 20:19:38 EEST 2012
 * 
 * File:      newUser.php
 * Full path: protected/modules/simple_rbac/views/admin/newUser.php
 *
 * Description: Show the form to create a new user.
 */
?>

<h3 class="h3NoMargin">Create a new user</h3>

<?=CHtml::beginForm()?>
<?=CHtml::errorSummary($model)?>
<?=CHtml::activeLabel($model, 'username')?><br />
<?=CHtml::activeTextField($model, 'username')?><br />
<br />
<?=CHtml::activeLabel($model, 'password')?><br />
<?=CHtml::activePasswordField($model, 'password')?><br />
<br />
<?=CHtml::submitButton('Create new user')?>
<?=CHtml::endForm()?>
