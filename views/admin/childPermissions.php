<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 23:28:10 EEST 2012
 * 
 * File:      childPermissions.php
 * Full path: protected/modules/simple_rbac/views/admin/childPermissions.php
 *
 * Description: A view to display all child permissions assigned to a parent role.
 */
?>

<h3 class="h3NoMargin">Child permissions of <span style="font-style: italic; text-decoration: underline;"><?=$roleName?></span> role</h3>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
         'dataProvider' => $childPermissionsDP,
         'columns' => array(
             array(
                 'name' => 'Name',
                 'value' => '$data["name"]',
                 'headerHtmlOptions' => array(
                     'colspan' => '2',
                 ),
             ),
             array
             (
                 'class'    => 'CButtonColumn',
                 'template' => '{delete}',
                 'buttons' => array(
                     'delete' => array
                     (
                         'imageUrl' => $modulePath.'/images/deleteIcon24.png',
                         'url'      => 'Yii::app()->createUrl("simple_rbac/admin/removeChildPermission", array("parentRole" => "'.$roleName.'", "childPermission" => $data["name"],))',
                     ),
                 ),
                 'headerHtmlOptions' => array(
                     'style' => 'display: none;',
                 ),
             ),
             array(
                 'name' => 'Description',
                 'value' => '$data["description"]',
             ),
         ),
    )
);
?>

<br />

<?=CHtml::button('', array('submit' => array('admin/assignChildPermission', 'parentRole' => $roleName,), 'csrf' => true, 'class' => 'createNewAction permission'))?>
