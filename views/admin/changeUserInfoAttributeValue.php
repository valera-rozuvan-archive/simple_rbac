<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 19:55:43 EEST 2012
 * 
 * File:      changeUserInfoAttributeValue.php
 * Full path: protected/modules/simple_rbac/views/admin/changeUserInfoAttributeValue.php
 *
 * Description: Show the form to change the value of an attribute from userInfo DB table.
 */
?>

<h3 class="h3NoMargin">
    User info: change value of
    <span style="font-style: italic; text-decoration: underline;"><?=$attribute?></span>
    for user
    <span style="font-style: italic; text-decoration: underline;"><?=$username?></span>
</h3>

<?=CHtml::beginForm()?>
<?=CHtml::errorSummary($model)?>
<?=CHtml::activeTextField($model, 'username', array('value' => $username, 'hidden' => 'hidden',))?>
<?=CHtml::activeTextField($model, 'attribute', array('value' => $attribute, 'hidden' => 'hidden',))?>
<?=CHtml::activeLabel($model, 'value')?><br />
<?=CHtml::activeTextField($model, 'value', array('value' => $oldValue,))?><br />
<br />
<?=CHtml::submitButton('Assign value')?>
<?=CHtml::endForm()?>
