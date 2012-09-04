<?php
/*
 * Author    Valera Rozuvan
 * Created:  Mon Aug 27 20:51:20 EEST 2012
 * 
 * File:      moduleTableStatus.php
 * Full path: protected/modules/simple_rbac/views/admin/moduleTableStatus.php
 *
 * Description: A view to notify the user of the status of DB tables used by this module. For each table it will show
 * either 'exists', or 'does not exist'.
 */
?>

<? if (isset($tableStatus)): ?>
    <ol>
        <? foreach ($tableStatus as $tableName => $status): ?>
        <li><?=$tableName?>: <?=($status === true) ? '<span style="color: green;">exists</span>' : '<span style="color: red;">does not exist</span>'?></li>
        <? endforeach ?>
    </ol>
    <br />
<? endif ?>
