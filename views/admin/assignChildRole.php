<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 22:57:44 EEST 2012
 * 
 * File:      assignChildRole.php
 * Full path: protected/modules/simple_rbac/views/assignChildRole.php
 *
 * Description: Show the form to assign a child role to a parent role.
 */
?>

<h3 class="h3NoMargin">Assign a child role to the parent role <span style="font-style: italic; text-decoration: underline;"><?=$parentRole?></span></h3>

<?=CHtml::beginForm()?>
<?=CHtml::errorSummary($model)?>
<?=CHtml::activeTextField($model, 'parentRole', array('value' => $parentRole, 'hidden' => 'hidden',))?>
<?=CHtml::activeLabel($model, 'childRole')?><br />
<?=CHtml::activeTextField($model, 'childRole')?><br />
<br />
<?=CHtml::submitButton('Assign child role')?>
<?=CHtml::endForm()?>
