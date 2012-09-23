<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 21:11:02 EEST 2012
 * 
 * File:      changePassword.php
 * Full path: protected/modules/simple_rbac/views/admin/changePassword.php
 *
 * Description: Show the form to change user's password.
 */
?>

<h3 class="h3NoMargin">Change the password for user <span style="font-style: italic; text-decoration: underline;"><?=$username?></span>
</h3>

<?=CHtml::beginForm()?>
<?=CHtml::errorSummary($model)?>
<?=CHtml::activeTextField($model, 'username', array('value' => $username, 'hidden' => 'hidden',))?>
<?=CHtml::activeLabel($model, 'newPassword1')?><br />
<?=CHtml::activePasswordField($model, 'newPassword1')?><br />
<?=CHtml::activeLabel($model, 'newPassword2')?><br />
<?=CHtml::activePasswordField($model, 'newPassword2')?><br />
<br />
<?=CHtml::submitButton('Change password')?>
<?=CHtml::endForm()?>
