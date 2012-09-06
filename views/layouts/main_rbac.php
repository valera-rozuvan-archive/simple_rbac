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

<html>
    <head>
        <title>This is the main layout of simple_rbac module</title>
        <link type="text/css" rel="stylesheet" href="<?=$this->modulePath.'css/adminIndex.css'?>" />
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
                            <?=CHtml::button('Users', array('submit' => array('admin/index',), 'csrf' => true, 'class' => 'menuButton'))?>
                            <?=CHtml::button('Roles', array('submit' => array('admin/index',), 'csrf' => true, 'class' => 'menuButton'))?>
                            <?=CHtml::button('Privileges', array('submit' => array('admin/index',), 'csrf' => true, 'class' => 'menuButton'))?>
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

                </div>
            <? endif ?>

        </div>
    </body>
</html>
