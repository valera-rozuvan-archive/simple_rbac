<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep  8 10:15:41 EEST 2012
 * 
 * File:      test.php
 * Full path: protected/modules/simple_rbac/views/admin/test.php
 *
 * Description: A view to use for testing.
 */
?>

Test.<br />

<?

// SRUser::deleteRole('newRole');

/*
$auth = Yii::app()->authManager;
$output = $auth->removeAuthItem('newRole');

echo 'removed: ';
if ($output === true)
    echo 'true';
else
    echo 'false';
echo '<br />';

// $auth->save();
*/
/*

$command = Yii::app()->db->createCommand()
                      ->delete('{{AuthItemChild}}',
                               'child=:name',
                               array(':name'=>'newRole'));
$command->execute();
$command->getPdoStatement()->closeCursor();
*/
/*
$command = Yii::app()->db->createCommand()
    ->delete('{{AuthItemChild}}',
             'parent=:name',
             array(':name'=>'newRole'));
$command->execute();
$command->getPdoStatement()->closeCursor();

$command = Yii::app()->db->createCommand()
    ->delete('{{AuthAssignment}}',
             'itemname=:name',
             array(':name'=>'newRole'));
$command->execute();
$command->getPdoStatement()->closeCursor();
*/
/*
$command = Yii::app()->db->createCommand()
    ->delete('{{AuthItem}}',
             'name=:name',
             array(':name'=>'newRole'));
$command->execute();
$command->getPdoStatement()->closeCursor();
*/

?>


<?
/*
$username = 'admin';

$user = SRUser::getUser($username);

if ($user === null) {
    echo 'User "'.$username.'" does not exist.<br />';
} else {
    $auth = Yii::app()->authManager;

    $userRoles = array_keys($auth->getAuthItems(2, $user->id));

    foreach ($userRoles as $userRole)
        echo $userRole.', ';
}
*/
?>

<?
/*
echo CHtml::button('Delete', array('submit' => array('admin/delete', 'type' => 'user', 'name' => 'testUserName',), 'csrf' => true, 'class' => 'headerButton logOut'))
*/
?>

<br />
asa
<br />

<?
/*
SRUser::createUser('admin', 'zzz');
*/
?>

<?
/*
SRUser::createPermission('anotherPermission4', 'Permission n 4.');
SRUser::assignPermission('admin', 'anotherPermission4');

SRUser::assignChildRole('admin', 'authenticated');
SRUser::createRole('newRole', 'this is a new role');

SRUser::assignChildRole('admin', 'newRole');
SRUser::createRole('subRole', 'Sub role of new role');
SRUser::assignChildRole('newRole', 'subRole');

SRUser::createUser('grand', 'lll', array('newRole'));

SRUser::createPermission('coolPermission', 'you can do cool things with this permission');
SRUser::assignPermission('newRole', 'coolPermission');

$role = Yii::app()->authManager->roles['admin'];

$roleInfo = array(
    'name' => $role->name,
    'description' => $role->description,
    'permissions' => implode(', ', array_keys($role->children)),
);

print_r($roleInfo);
*/
?>

<br /><br />

<?
/*
$permissions = Yii::app()->authManager->getAuthItems(0);
print_r(array_keys($permissions));
*/
?>

<br /><br />

User has 'anotherPermission4' permission: <?=(SRUser::checkAccess('anotherPermission4')) ? 'true' : 'false'?><br />
User has 'coolPermission' permission: <?=(SRUser::checkAccess('coolPermission')) ? 'true' : 'false'?><br />

User also has 'newRole' permissions: <?=(SRUser::checkAccess('newRole')) ? 'true' : 'false'?><br />
User also has 'guest' permissions: <?=(SRUser::checkAccess('guest')) ? 'true' : 'false'?><br />

<br /><br />

<?

$password = '1234';

?>

Hello, world! from simple_rbac module, default controller, index action.<br />
<br />
Password: <?=$password?><br />
Hashed password: <?=crypt($password)?><br />
<br />
Current user ID is <?=Yii::app()->user->getId()?><br />
<br />
Roles:<br />
<?=print_r(array_keys(Yii::app()->authManager->roles), true)?><br />
<br />
Default roles:<br />
<?=print_r(Yii::app()->authManager->defaultRoles, true)?><br />
<br />
Is guest: <?=(Yii::app()->authManager->checkAccess('guest', Yii::app()->user->getId())) ? 'true' : 'false'?><br />
<br />
Is authenticated: <?=(Yii::app()->authManager->checkAccess('authenticated', Yii::app()->user->getId())) ? 'true' : 'false'?><br />
<br />
Is admin: <?=(Yii::app()->authManager->checkAccess('admin', Yii::app()->user->getId())) ? 'true' : 'false'?><br />
<br />
