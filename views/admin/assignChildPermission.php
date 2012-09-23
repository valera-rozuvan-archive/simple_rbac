<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 23:40:29 EEST 2012
 * 
 * File:      assignChildPermission.php
 * Full path: protected/modules/simple_rbac/views/assignChildPermission.php
 *
 * Description: Show the form to assign a child permission to a parent role.
 */
?>

<h3 class="h3NoMargin">Assign a child permission to the parent role <span style="font-style: italic; text-decoration: underline;"><?=$parentRole?></span></h3>

<?=CHtml::beginForm()?>
<?=CHtml::errorSummary($model)?>
<?=CHtml::activeTextField($model, 'parentRole', array('value' => $parentRole, 'hidden' => 'hidden',))?>
<?=CHtml::activeLabel($model, 'childPermission')?><br />
<?=CHtml::activeTextField($model, 'childPermission')?><br />
<br />
<?=CHtml::submitButton('Assign child permission')?>
<?=CHtml::endForm()?>
