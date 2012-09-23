<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 22:13:56 EEST 2012
 * 
 * File:      childRoles.php
 * Full path: protected/modules/simple_rbac/views/admin/childRoles.php
 *
 * Description: A view to display all child roles assigned to a parent role.
 */
?>

<h3 class="h3NoMargin">Child roles of <span style="font-style: italic; text-decoration: underline;"><?=$roleName?></span> role</h3>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
         'dataProvider' => $childRolesDP,
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
                         'url'      => 'Yii::app()->createUrl("simple_rbac/admin/removeChildRole", array("parentRole" => "'.$roleName.'", "childRole" => $data["name"],))',
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

<?=CHtml::button('', array('submit' => array('admin/assignChildRole', 'parentRole' => $roleName,), 'csrf' => true, 'class' => 'createNewAction permission'))?>
