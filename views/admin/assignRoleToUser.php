<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 17:18:15 EEST 2012
 * 
 * File:      assignRoleToUser.php
 * Full path: protected/modules/simple_rbac/views/assignRoleToUser.php
 *
 * Description: Show the form to assign a role to a user.
 */
?>

<h3 class="h3NoMargin">Assign role to user <span style="font-style: italic; text-decoration: underline;"><?=$username?></span></h3>

<?=CHtml::beginForm()?>
<?=CHtml::errorSummary($model)?>
<?=CHtml::activeTextField($model, 'username', array('value' => $username, 'hidden' => 'hidden',))?>
<?=CHtml::activeLabel($model, 'role')?><br />
<?=CHtml::activeTextField($model, 'role')?><br />
<br />
<?=CHtml::submitButton('Assign role')?>
<?=CHtml::endForm()?>
