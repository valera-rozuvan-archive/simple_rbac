<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep  9 10:20:14 EEST 2012
 * 
 * File:      newPermission.php
 * Full path: protected/modules/simple_rbac/views/admin/newPermission.php
 *
 * Description: Show the form to create a new permission.
 */
?>

<h3 class="h3NoMargin">Create a new permission</h3>

<?=CHtml::beginForm()?>
<?=CHtml::errorSummary($model)?>
<?=CHtml::activeLabel($model, 'permissionName')?><br />
<?=CHtml::activeTextField($model, 'permissionName')?><br />
<br />
<?=CHtml::activeLabel($model, 'description')?><br />
<?=CHtml::activeTextField($model, 'description')?><br />
<br />
<?=CHtml::submitButton('Create new permission')?>
<?=CHtml::endForm()?>
