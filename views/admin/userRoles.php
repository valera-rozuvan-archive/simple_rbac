<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep 15 19:59:45 EEST 2012
 * 
 * File:      userRoles.php
 * Full path: protected/modules/simple_rbac/views/admin/userRoles.php
 *
 * Description: A view to display all the roles that are assigned to the specified user.
 */
?>

<h3 class="h3NoMargin">Roles assigned to user <span style="font-style: italic; text-decoration: underline;"><?=$username?></span></h3>

<?
$this->widget(
    'zii.widgets.grid.CGridView',
    array(
         'dataProvider' => $userRolesDP,
         'columns' => array(
             array(
                 'name' => 'Role',
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
                         'url'      => 'Yii::app()->createUrl("simple_rbac/admin/revokeRoleFromUser", array("username" => "'.$username.'", "role" => $data["name"],))',
                         'visible'  => '!(($data["name"] === "admin") && (SRUser::getUserId("'.$username.'") === 1))',
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
         ),
    )
);
?>

<br />

<?=CHtml::button('', array('submit' => array('admin/assignRoleToUser', 'username' => $username), 'csrf' => true, 'class' => 'createNewAction role'))?>
