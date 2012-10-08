<?/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep  8 10:15:41 EEST 2012
 * 
 * File:      test.php
 * Full path: protected/modules/simple_rbac/views/admin/test.php
 *
 * Description: A view to use for testing.
 */?>

<?/*
Test.<br />
*/?>

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

<?/*
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
*/?>

<?/*
echo CHtml::button('Delete', array('submit' => array('admin/delete', 'type' => 'user', 'name' => 'testUserName',), 'csrf' => true, 'class' => 'headerButton logOut'))
*/?>

<?/*
<br />
asa
<br />
*/?>

<?/*
SRUser::createUser('admin', 'zzz');
*/?>

<?/*
SRUser::createPermission('anotherPermission4', 'Permission n 4.');
SRUser::assignChildPermission('admin', 'anotherPermission4');

SRUser::assignChildRole('admin', 'authenticated');
SRUser::createRole('newRole', 'this is a new role');

SRUser::assignChildRole('admin', 'newRole');
SRUser::createRole('subRole', 'Sub role of new role');
SRUser::assignChildRole('newRole', 'subRole');

SRUser::createUser('grand', 'lll', array('newRole'));

SRUser::createPermission('coolPermission', 'you can do cool things with this permission');
SRUser::assignChildPermission('newRole', 'coolPermission');

$role = Yii::app()->authManager->roles['admin'];

$roleInfo = array(
    'name' => $role->name,
    'description' => $role->description,
    'permissions' => implode(', ', array_keys($role->children)),
);

print_r($roleInfo);
*/?>

<?/*
<br /><br />
*/?>

<?/*
$permissions = Yii::app()->authManager->getAuthItems(0);
print_r(array_keys($permissions));
*/?>

<?/*
<br /><br />
*/?>

<?/*
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

*/?>

<?/*
$user = SRUser::getUser('admin');

if (!isset($user->userInfo->user_id)) {
    echo 'userInfo relations is undefined; creating a new one...<br />';

    $userInfo = new SimpleRbacUsersInfoDbTable();
    $userInfo->user_id = $user->id;
    $userInfo->first_name = 'YourFirstName';
    $userInfo->save();

    $user = SRUser::getUser('admin');
} else {
    echo 'userInfo relation is defined; retrieving stored data...<br />';
}

echo 'User id: '.$user->userInfo->user_id.'<br />';
echo 'First name: '.$user->userInfo->first_name.'<br />';

$user->userInfo->last_name = 'YourLastName';
$user->userInfo->save();

$user = SRUser::getUser('admin');

echo 'Last name: '.$user->userInfo->last_name.'<br />';
*/?>

<?/*
<br />
<? print_r(array_keys($user->getAttributes())) ?>
<br />
<? print_r(array_keys(SimpleRbacUsersInfoDbTable::model()->getAttributes())) ?>
*/?>

<?/*<pre>
<?

function test_p10()
{

}

class test_p17
{

}

class test_p18
{
    public function __destruct()
    {

    }
}

// is NOT set, is empty, does NOT evaluate to true, is null
$x_p1;
$x_p2 = null;
$x_p3 = test_p10(); // for functions without a return() statement PHP returns null

// is set, is empty, does NOT evaluate to true, is NOT null
$x_p4 = '';
$x_p5 = array();
$x_p6 = false;
$x_p7 = 0;
$x_p8 = '0';
$x_p9 = 0.0;

// !!!! is set, is NOT empty, evaluates to true, is NOT null
$x_p10 = '0.0';

// is set, is NOT empty, evaluates to true, is NOT null
$x_p11 = 1;
$x_p12 = '1';
$x_p13 = array(1,);
$x_p14 = array(array(),);
$x_p15 = true;
$x_p16 = NAN;
$x_p17 = new test_p17();
$x_p18 = new test_p18();
$x_p18->__destruct();
$x_p19 = (object)null;

for ($i = 0; $i <= 19; $i++) {
    echo 'x_p'.$i.' ';
    if (isset(${'x_p'.$i}))
        echo 'is set, ';
    else
        echo 'is NOT set, ';

    if (empty(${'x_p'.$i}))
        echo 'is empty, ';
    else
        echo 'is NOT empty, ';

    if (${'x_p'.$i})
        echo 'evaluates to true, ';
    else
        echo 'does NOT evaluate to true, ';

    if (is_null(${'x_p'.$i}))
        echo 'is null';
    else
        echo 'is NOT null';
    echo '<br />';
}

for ($i = 0; $i <= 19; $i++) {
    unset(${'x_p'.$i});
}
echo '<br />';

for ($i = 0; $i <= 19; $i++) {
    echo 'x_p'.$i.' ';
    if (isset(${'x_p'.$i}))
        echo 'is set, ';
    else
        echo 'is NOT set, ';

    if (empty(${'x_p'.$i}))
        echo 'is empty, ';
    else
        echo 'is NOT empty, ';

    if (${'x_p'.$i})
        echo 'evaluates to true, ';
    else
        echo 'does NOT evaluate to true, ';

    if (is_null(${'x_p'.$i}))
        echo 'is null';
    else
        echo 'is NOT null';
    echo '<br />';
}

for ($i = 0; $i <= 19; $i++) {
    ${'x_p'.$i} = '1';
}
echo '<br />';

for ($i = 0; $i <= 19; $i++) {
    echo 'x_p'.$i.' ';
    if (isset(${'x_p'.$i}))
        echo 'is set, ';
    else
        echo 'is NOT set, ';

    if (empty(${'x_p'.$i}))
        echo 'is empty, ';
    else
        echo 'is NOT empty, ';

    if (${'x_p'.$i})
        echo 'evaluates to true, ';
    else
        echo 'does NOT evaluate to true, ';

    if (is_null(${'x_p'.$i}))
        echo 'is null';
    else
        echo 'is NOT null';
    echo '<br />';
}

?>
</pre>*/?>

<?
/**
$user = SRUser::getUser('admin');

$auth = $auth = Yii::app()->authManager;

print_r(array_keys($auth->getAuthAssignments($user->id)));

echo '<br />';

echo 'User ID: '.Yii::app()->user->id;
*/
?>


<?

function myPrintR(array $ar)
{
    print_r($ar);
}

myPrintR(array('one' => 1, 'two' => 2, 'three' => 3,));

?>
