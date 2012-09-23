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
        'columns'      => array(
            'id',
            array(
                'name'  => 'username',
                'type'  => 'html',
                'value' => 'CHtml::link($data->username, array("admin/userInfo", "username" => $data->username,))',
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
                        'url'      => 'Yii::app()->createUrl("simple_rbac/admin/delete", array("type" => "user", "name" => $data->username,))',
                        'visible'  => 'SRUser::getUserId($data->username) !== 1',
                    ),
                ),
                'headerHtmlOptions' => array(
                    'style' => 'display: none;',
                ),
            ),
            array(
                'class'    => 'CButtonColumn',
                'template' => '{roles}',
                'header'   => 'Roles',
                'buttons'  => array(
                    'roles' => array(
                        'imageUrl' => $modulePath.'/images/editIcon24.png',
                        'url'      => 'Yii::app()->createUrl("simple_rbac/admin/userRoles", array("username" => $data->username,))',
                    ),
                ),
            ),
            array(
                'class'              => 'CLinkColumn',
                'labelExpression'    => '(SRUser::getUserId($data->username) === 1) ? "" : ((intval($data->status) === 1) ? "active" : "inactive")',
                'urlExpression'      => '(intval($data->status) === 1) ?
                                             Yii::app()->createUrl("simple_rbac/admin/switchUserStatus", array("username" => $data->username, "status" => 0,)) :
                                             Yii::app()->createUrl("simple_rbac/admin/switchUserStatus", array("username" => $data->username, "status" => 1,))',
                'header'             => 'Status',
                'cssClassExpression' => '(intval($data->status) === 1) ? "status active" : "status inactive"',
            ),
            array(
                'name'  => 'last_access',
                'value' => '(preg_match("/^1970-01-01/", $data->last_access)) ? "never" : $data->last_access',
            ),
            'registered',
        ),
    )
);
?>

<br />

<?=CHtml::button('', array('submit' => array('admin/newUser',), 'csrf' => true, 'class' => 'createNewAction user'))?>
