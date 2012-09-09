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
            array(
                'type'  => 'raw',
                'value' => '($data->username !== "admin") ? "<div class=\"gridActionIcon deleteUser\" value=\"" . $data->id . "\"></div>" : ""',
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
