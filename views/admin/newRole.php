<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep  9 10:19:19 EEST 2012
 * 
 * File:      newRole.php
 * Full path: protected/modules/simple_rbac/views/admin/newRole.php
 *
 * Description: Show the form to create a new role.
 */
?>

<h3 class="h3NoMargin">Create a new role</h3>

<?=CHtml::beginForm()?>
<?=CHtml::errorSummary($model)?>
<?=CHtml::activeLabel($model, 'roleName')?><br />
<?=CHtml::activeTextField($model, 'roleName')?><br />
<br />
<?=CHtml::activeLabel($model, 'description')?><br />
<?=CHtml::activeTextField($model, 'description')?><br />
<br />
<?=CHtml::submitButton('Create new role')?>
<?=CHtml::endForm()?>
