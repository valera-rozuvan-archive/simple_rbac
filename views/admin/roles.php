<?php
/*
 * Author    Valera Rozuvan
 * Created:  Fri Sep  7 06:23:32 EEST 2012
 * 
 * File:      roles.php
 * Full path: protected/modules/simple_rbac/views/admin/roles.php
 *
 * Description: A view to display the currently available rows.
 */
?>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
        'dataProvider' => $rolesDP,
        'columns'      => array(
            array(
                'name'  => 'Role',
                'value' => '$data["name"]',
                'headerHtmlOptions' => array(
                    'colspan' => '2',
                ),
            ),
            array(
                'class'    => 'CButtonColumn',
                'template' => '{delete}',
                'buttons'  => array(
                    'delete' => array(
                        'imageUrl' => $modulePath.'/images/deleteIcon24.png',
                        'url'      => 'Yii::app()->createUrl("simple_rbac/admin/delete", array("type" => "role", "name" => $data["name"],))',
                        'visible'  => '!in_array($data["name"], array("admin", "authenticated", "guest",))',
                    ),
                ),
                'headerHtmlOptions' => array(
                    'style' => 'display: none;',
                ),
            ),
            array(
                'name'  => 'Description',
                'value' => '$data["description"]',
            ),
            array(
                'class'    => 'CButtonColumn',
                'template' => '{childRoles}',
                'header'   => 'Child roles',
                'buttons'  => array(
                    'childRoles' => array(
                        'imageUrl' => $modulePath.'/images/editIcon24.png',
                        'url'      => 'Yii::app()->createUrl("simple_rbac/admin/childRoles", array("roleName" => $data["name"],))',
                    ),
                ),
            ),
            array(
                'class'    => 'CButtonColumn',
                'template' => '{childPermissions}',
                'header'   => 'Child permissions',
                'buttons'  => array(
                    'childPermissions' => array(
                        'imageUrl' => $modulePath.'/images/editIcon24.png',
                        'url'      => 'Yii::app()->createUrl("simple_rbac/admin/childPermissions", array("roleName" => $data["name"],))',
                    ),
                ),
            ),
        ),
    )
);
?>

<br />

<?=CHtml::button('', array('submit' => array('admin/newRole',), 'csrf' => true, 'class' => 'createNewAction role'))?>
