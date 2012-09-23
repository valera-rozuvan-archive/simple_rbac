<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep 15 19:22:27 EEST 2012
 * 
 * File:      userInfo.php
 * Full path: protected/modules/simple_rbac/views/admin/userInfo.php
 *
 * Description: Will show a table of all user related information.
 */
?>

<h3 class="h3NoMargin">User information for <span style="font-style: italic; text-decoration: underline;"><?=$username?></span></h3>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
         'dataProvider' => $userInfoDP,
         'columns' => array(
             array(
                 'name' => 'Attribute',
                 'value' => '$data["attribute"]',
             ),
             array(
                 'name' => 'Value',
                 'value' => '$data["value"]',
                 'headerHtmlOptions' => array(
                     'colspan' => '2',
                 ),
             ),
             array
             (
                 'class'    => 'CButtonColumn',
                 'template' => '{edit}',
                 'buttons' => array(
                     'edit' => array
                     (
                         'imageUrl' => $modulePath.'/images/editIcon24.png',
                         'url'      => 'Yii::app()->createUrl("simple_rbac/admin/changeUserInfoAttributeValue", array("username" => "'.$username.'", "attribute" => $data["attribute"],))',
                         'visible'  => '$data["attribute"] !== "user_id"',
                     ),
                 ),
                 'headerHtmlOptions' => array(
                     'style' => 'display: none;',
                 ),
             ),
         ),
    )
);
?>

<?=CHTML::link('Change password', array('admin/changePassword', 'username' => $username,))?>
