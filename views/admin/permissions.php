<?php
/*
 * Author    Valera Rozuvan
 * Created:  Fri Sep  7 06:30:43 EEST 2012
 * 
 * File:      permissions.php
 * Full path: protected/modules/simple_rbac/views/admin/permissions.php
 *
 * Description: This view will display a list of available privileges.
 */
?>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
         'dataProvider' => $permissionsDP,
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
                         'url'      => 'Yii::app()->createUrl("simple_rbac/admin/delete", array("type" => "permission", "name" => $data["name"],))',
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

<?=CHtml::button('', array('submit' => array('admin/newPermission',), 'csrf' => true, 'class' => 'createNewAction permission'))?>
