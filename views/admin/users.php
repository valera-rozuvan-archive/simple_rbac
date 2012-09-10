<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Aug 25 17:08:41 EEST 2012
 * 
 * File:      users.php
 * Full path: protected/modules/simple_rbac/views/admin/users.php
 *
 * Description: The main view of the Simple RBAC module. Will provide links to the various actions available (user
 * creation, role management, ...).
 */
?>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
        'dataProvider' => $dataProvider,
        'columns' => array(
            'id',
            array(
                'name'  => 'username',
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
                        'url'      => 'Yii::app()->createUrl("simple_rbac/admin/delete", array("type" => "user", "name" => $data->username,))',
                        'visible'  => '!in_array($data->username, array("admin",))',
                    ),
                ),
                'headerHtmlOptions' => array(
                    'style' => 'display: none;',
                ),
            ),
            array(
                'name' => 'status',
                'type' => 'html',
                'value' => '(intval($data->status) === 1) ? "<span class=\"status active\">active</span>" : "<span class=\"status inactive\">inactive</span>"',
            ),
            array(
                'name' => 'last_access',
                'value' => '(preg_match("/^1970-01-01/", $data->last_access)) ? "never" : $data->last_access',
            ),
            'registered',
        ),
    )
);
?>

<br />

<?=CHtml::button('', array('submit' => array('admin/newUser',), 'csrf' => true, 'class' => 'createNewAction user'))?>
