<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Aug 25 17:23:09 EEST 2012
 * 
 * File:      main.php
 * Full path: protected/modules/simple_rbac/views/layouts/main.php
 *
 * Description: The main layout used by views in the Simple RBAC module.
 */
?>

<?
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->getClientScript();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>Admin</title>
        <? $cs->registerCssFile($baseUrl.$this->modulePath.'/css/adminIndex.css') ?>
    </head>
    <body>
        <div id="page">

            <? if ($this->useHeader): ?>
                <div class="pageBlock header">
                    <?=CHtml::button('', array('submit' => array('admin/logout',), 'csrf' => true, 'class' => 'headerButton logOut'))?>
                    <?=CHtml::button('', array('submit' => array('/site/index',), 'csrf' => true, 'class' => 'headerButton home'))?>
                </div>
            <? endif ?>

            <? if ($this->useBody): ?>
                <div class="pageBlock body">
                    <? if ($this->useMenu): ?>
                        <div class="bodyBlock menu">
                            <?=CHtml::button('Users', array('submit' => array('admin/users',), 'csrf' => true, 'class' => 'menuButton'))?>
                            <?=CHtml::button('Roles', array('submit' => array('admin/roles',), 'csrf' => true, 'class' => 'menuButton'))?>
                            <?=CHtml::button('Permissions', array('submit' => array('admin/permissions',), 'csrf' => true, 'class' => 'menuButton'))?>
                        </div>
                    <? endif ?>
                    <? if ($this->useChart): ?>
                        <div class="bodyBlock chart">
                            <?=$content?>
                        </div>
                    <? endif ?>
                </div>
            <? endif ?>

            <? if ($this->useFooter): ?>
                <div class="pageBlock footer">
                    <div class="moduleInfo"><a href="https://github.com/valera-rozuvan/simple_rbac">Simple RBAC module</a> v<?=$this->module->simple_rbacVersion?></div>
                </div>
            <? endif ?>

        </div>
    </body>
</html>
