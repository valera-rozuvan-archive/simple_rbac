----------------------------------------------
---++++====== Simple RBAC module ======++++---

---++++================= for Yii ======++++---

Version:

    1.3

Date:

    Mon Oct  8 06:46:58 EEST 2012

Authors:

    REMEDY
    Valera Rozuvan



==-----------------==
|| A - Description ||
==-----------------==

This module is provided as an example of how one can use the available Yii CDbAuthManager class together with custom
DB user tables. It does not fully realize Yii's idea of roles, tasks, and operations. Instead it develops a simplified
authorization management model which includes only roles, and permissions. Permissions are the same as operations in
Yii's terminology.

Roles can be assigned to a user. Roles can also be assigned to other roles. This creates a parent-child role
relationship in which all of the child roles are also indirectly assigned to the user who is assigned the parent role.

Permissions can be assigned to roles. In a parent-child relationship, all of the permissions of the parent role, along
with permissions of all the child roles, are carried over to the user who is assigned the parent role.

This hierarchy allows for a very flexible RBAC scheme to be built into any Yii based site. It can be used together with
custom filtering (or using Yii's default 'accessControl' filter) to allow or deny access to specific controller
actions. This module also provides methods for fine grain control of authorization. For example, we can check if a user
has a specific permission, and display a portion of the overall layout based on this permission.

As a final word, you, as a developer, must understand that this module was created with a purpose to better understand
the tools that Yii provides for authorization. It is not an end-all-and-be-all solution. So use it carefully, testing
everything before putting it on a production machine.



==------------------==
|| B - Installation ||
==------------------==

1.) Extract and place the folder 'simple_rbac' (with all it's contents) into 'protected/modules' folder. If the folder
'modules' does not exist, create it. You should have the following directory structure:

    protected/modules/simple_rbac/.
    protected/modules/simple_rbac/..
    protected/modules/simple_rbac/components/
    protected/modules/simple_rbac/controllers/
    protected/modules/simple_rbac/css/
    protected/modules/simple_rbac/extensions/
    protected/modules/simple_rbac/images/
    protected/modules/simple_rbac/models/
    protected/modules/simple_rbac/views/
    protected/modules/simple_rbac/README.md
    protected/modules/simple_rbac/Simple_rbacModule.php

2.) In the file 'protected/config/main.php', a few sub-arrays should be modified.

a.) Instruct Yii where to find Simple RBAC model classes, and tell Yii of the existence of the module:

    ...
    'import' => array(
        ...
        'application.modules.simple_rbac.models.*',
        'application.modules.simple_rbac.components.*',
        'application.modules.simple_rbac.models.forms.*',
        'application.modules.simple_rbac.models.db_tables.*',
        'application.modules.simple_rbac.models.data_providers.*',
        ...
    ),
    ...
    'modules' => array(
       ...
       'simple_rbac' => array(
           'setup' => true,
       ),
       ...
    ),
    ...

b.) Make sure Yii's database connection settings are configured properly. Simple RBAC module requires a database
connection to store the necessary tables and data. For example:

    ...
    'components' => array(
        ...
        'db' => array(
            'class'            => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;port=3306;dbname=test_database',
            'emulatePrepare'   => true,
            'username'         => 'sample_user',
            'password'         => 'user_password',
            'charset'          => 'utf8',
            'tablePrefix'      => 'tbl_',
        ),
        ...
    ),
    ...

This assumes that there is a MySQL server running at 'localhost' (port 3306), and that a database named 'test_database'
exists, and is accessible by user 'sample_user' (with password 'user_password').

NOTE: Simple RBAC module will work with a database table prefix or without it. So, a table prefix is optional, and
is not required. If your database tables do not use a table prefix, set it to an empty string. I.e.:

    'tablePrefix' => '',

This is a must. If table names are specified by wrapping them in {{ and }}, and the 'tablePrefix' is not set, Yii
behaves strangely.

c.) Tell the 'authManager' component to use table prefixes, and declare two default user roles:

    ...
    'components' => array(
        ...
        'authManager' => array(
            'class'           => 'CDbAuthManager',
            'connectionID'    => 'db',
            'itemTable'       => '{{AuthItem}}',
            'itemChildTable'  => '{{AuthItemChild}}',
            'assignmentTable' => '{{AuthAssignment}}',
            'defaultRoles'    =>  array(
                'guest',
                'authenticated',
            ),
        ),
        ...
    ),
    ...

3.) Commence the internal install procedure by going to:

    http://your-site.com/simple_rbac/admin/install

Modify the URL according to the URL schema used in your Yii project. For example, if you don't have friendly URLs
enabled, then you probably will have to access the install action via:

    http://your-site.com/index.php?r=simple_rbac/admin/install

You will get a list of tables, along with their status ('exists', or 'does not exist'). If all tables are marked as
'exists', then the installation went successfully. If something went wrong, you can try uninstalling (see section
'C - Uninstall' below), and repeating the whole process.

If the installation action was successful, comment out (or remove) the following configuration setting:

    'setup' => true,

in the sub-array 'simple_rbac' in the file 'protected/config/main.php'. Until you do that, you will not be able to
access the administration panel of Simple RABC module. It is a safety precussion.

4.) Modify the site's controller default login action (or whatever action used for logging in at your site) to use
this module's 'SimpleRbacLoginForm' model. It should look something similar to the following:

    /*
     * Displays the login page.
     */
    public function actionLogin()
    {
        $model = new SimpleRbacLoginForm();

        // if it is ajax validation request
        if ((isset($_POST['ajax'])) && ($_POST['ajax'] === 'login-form')) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['SimpleRbacLoginForm'])) {
            $model->attributes = $_POST['SimpleRbacLoginForm'];

            // validate user input and redirect to the previous page if valid
            if (($model->validate()) && ($model->login()))
                $this->redirect(Yii::app()->user->returnUrl);
        }

        // display the login form
        $this->render('login', array('model'=> $model,));
    }

5.) Optionally, add a menu entry for easy access to the Simple RBAC module's administration page:

    $this->widget(
        'zii.widgets.CMenu',
        array(
            'items' => array(
                ...
                array(
                    'label'   => Yii::t('main','Admin'),
                    'url'     => array('/simple_rbac/admin/users',),
                    'visible' => SRUser::checkAccess('admin'),
                ),
                ...
            ),
        )
    );



==---------------==
|| C - Uninstall ||
==---------------==

To uninstall (i.e. remove all created tables and their data from the DB), go to the following URL:

    http://your-site.com/simple_rbac/admin/uninstall

You should get a list of tables, along with their statuses. If everything went well, each table should be marked as
'does not exist'.

After the uninstall action has run successfully, you can remove the module's directory

    protected/modules/simple_rbac/

and undo all the changes that you have made to the 'protected/config/main.php' file, and your login action.



==-----------==
|| D - Usage ||
==-----------==

1.) Example of using Yii's default 'accessControl' filter.

    class MainController extends CController
    {
        /*
         * http://www.yiiframework.com/doc/api/1.1/CController#filters-detail
         * http://www.yiiframework.com/doc/api/1.1/CController#filterAccessControl-detail
         *
         * public array filters()
         *
         * {return}
         *     array: a list of filter configurations.
         *
         * Description:
         *     Returns the filter configurations.
         *
         *     By overriding this method, child classes can specify filters to be applied to actions.
         *
         *     This method returns an array of filter specifications. Each array element specifies a single filter.
         *
         *     For a method-based filter (called inline filter), it is specified as
         *         'FilterName[ +|- Action1, Action2, ...]',
         *     where the '+' ('-') operators describe which actions should be (should not be) applied with the filter.
         *
         *     For a class-based filter, it is specified as an array like the following:
         *
         *         array(
         *             'FilterClass[ +|- Action1, Action2, ...]',
         *             'name1'=>'value1',
         *             'name2'=>'value2',
         *             ...
         *         )
         *
         *     where the name-value pairs will be used to initialize the properties of the filter.
         *
         *     Note, in order to inherit filters defined in the parent class, a child class needs to merge the parent
         *     filters with child filters using functions like array_merge().
         */
        public function filters()
        {
            return array(
                'accessControl',
            );
        }

        /*
         * http://www.yiiframework.com/doc/api/1.1/CController#accessRules-detail
         * http://www.yiiframework.com/doc/api/1.1/CAccessControlFilter
         *
         * public array accessRules()
         *
         * {return}
         *     array: list of access rules. See CAccessControlFilter for details about rule specification.
         *
         * Description:
         *     Returns the access rules for this controller. Override this method if you use the accessControl filter.
         */
        public function accessRules()
        {
            return array(
                array(
                    'allow',
                    'actions' => array('users', 'admin',),
                    'roles'   => array('admin',),
                ),
                array(
                    'allow',
                    'actions' => array('forum', 'addForumPost',),
                    'roles'   => array('authenticated',),
                ),
                array(
                    'allow',
                    'actions' => array('forum',),
                    'roles'   => array('guest',),
                ),
                array(
                    'allow',
                    'actions' => array('specialPrivatePage'),
                    'users'   => array('username_a', 'username_b', 'username_c',),
                ),
                array(
                    'deny',
                    'actions' => array('users', 'admin', 'forum', 'addForumPost', 'specialPrivatePage',),
                    'users'   => array('*',),
                ),
            );
        }

        public function actionUsers()
        {
            // do something when the 'users' action is called
        }

        public function actionAdmin()
        {
            // do something when the 'admin' action is called
        }

        public function actionForum()
        {
            // do something when the 'forum' action is called
        }

        public function actionAddForumPost()
        {
            // do something when the 'addForumPost' action is called
        }

        public function actionSpecialPrivatePage()
        {
            // do something when the 'specialPrivatePage' action is called
        }
    }

Now, lets see what can be accessed and by who.

    main/users, main/admin - accessible by all users who are assigned the 'admin' role.
    main/forum, main/addForumPost - accessible by all users who are assigned the 'authenticated' role; 'authenticated'
        is a default role, which is assigned to a user based on a business rule; if a user is not a guest (i.e. he is
        authenticated), then he will be assigned the 'authenticated' role automatically.
    main/forum - accessible by all users who are assigned the 'guest' role; this role is also assigned to a user based
        on a business rule; if a user is a guest (i.e. he is not authenticated), he will be assigned the 'guest' role
        automatically.
    main/specialPrivatePage - accessible by users with a username 'username_a', or 'username_b', or 'username_c'.

The access rules are processed from top to bottom. If one rule is matched, then the process stops, and that rule's
action is executed (the action is either 'allow' or 'deny'). If none of the first four rules are matched, then we come
to the last rule:

    array(
        'deny',
        'actions' => array('users', 'admin', 'forum', 'addForumPost', 'specialPrivatePage',),
        'users'   => array('*',),
    ),

This will always match, because the '*' character targets all users. You could also use the '?' character to specify
guest users. If this was not added to the end, then, by default, everything would be accessible (since we did not
specify what to deny). Other possibilities are:

    // If we got here, deny everything to everyone.
    array(
        'deny',
    ),

or

    // If we got here, deny everything to users who are assigned the default 'guest' role.
    array(
        'deny',
        'roles' => array('guest',),
    ),

or

    // If we got here, deny everything to guest users (not authenticated users).
    array(
        'deny',
        'users' => array('?',),
    ),

NOTE: When specifying a list for the 'roles' parameter, not only can we include roles, but also the permissions. This
is because Yii recognizes roles, tasks (not used in this module), and permissions (operations in Yii's terminlogy) as
CAuthItem objects. Please see http://www.yiiframework.com/doc/api/1.1/CAuthItem for reference.

2.) Example of using fine grain authorization control.

One of the classes that this module defines is the SRUser class. To test for user authorization access details, SRUser
provides a static method checkAccess(). To check if the current user has some authorization item (role, or permission)
assigned to him, we can do the following:

    if (SRUser::checkAccess('admin')) {
        // The user is assigned the 'admin' authorization item (in this case, 'admin' is a role).
        // Do something that the adminsitrator can do.
    }

or

    if (SRUser::checkAccess('can_edit_commercial_block')) {
        // The user is assigned the 'can_edit_commercial_block' authorization item. This might be a permission.
        // Commercial block editing code (or HTML) goes here.
    }

3.) Accessing and managing of user information.

The SRUser class provides a method to retrieve a user and his additonal information with an easy to use method. Note,
if the additional information does not exist, you must first create it.

    $user = SRUser::getUser('admin');

    if (!isset($user->userInfo->user_id)) {
        echo 'userInfo relations is undefined; creating a new one...<br />';

        $userInfo = new SimpleRbacUsersInfoDbTable();
        $userInfo->user_id    = $user->id;
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
    echo 'Last name: '.$user->userInfo->lastName.'<br />';



==------------------------==
|| E - Database structure ||
==------------------------==

a.) General information
The tables '{{AuthItem}}', '{{AuthItemChild}}', and '{{AuthAssignment}}' are provided by The Yii framework. Their
defintions can be found in the file 'framework/web/auth/schema-mysql.sql'. It is not advisable to change the structure
of these tables, because the component authManager (class CDbAuthManager) was built specifically to work with them.

Table {{simple_rbac_users}}, {{simple_rbac_users_info}} are custom created user tables.and  stores the basic information about the user. (user ID, usrename, password, status,
last_access date, and registered date).

NOTE: The surrounding squgy brackets allow for the use of DB table prefixes.

b.) Details

AuthAssignment table: this table contains the connection between users and roles (more generally, authItems). Itemname
is the name of the role (defined in table {{AuthItem}}), and userid is the ID of the user (defined in
table {{simple_rbac_users}}).

AuthItem table: this table stores information about a single authItem. In the case of our module, only permissions, and
roles are used. name is how the authItem is referenced, type specifies if it is a role (type = 2) or a permission
(type = 0), description is used for a short description of what the authItem is for (it can be left blank), and bizRule
can be a PHP code snippet which is used to assign the authItem ot a user. BizRules are used for default roles.

AuthItemChild table: defines the relationship between authItems. I.e. multiple roles can be assigned to a role, and
multiple permissions can be asigned to a role. Assigning roles or permissions circularly is not allowed. Also, the
design of this module only provides for assigning roles to a role, and permissions to a role. Other combinations are
possible in theory, but are not implemented.

simple_rbac_users table: stores the basic information about the user - user ID, usrename, password, status, last access
date, and registered date.

simple_rbac_users_info table: additional information about a user. It has a one to one relationship with the table
simple_rbac_users. For now, extra columns (that will represent extra information) can be added ony via external DB
management software (for example PHPMyAdmin). However, editing the values for any available field from this table's
structure is possible from within the administration panel.



==----------------==
|| F - Change log ||
==----------------==

[08.10.2012]
+ Added static helper methods to SRUser class.
+ Rewrote existing SRUser methods, and form validation methods to use new helper methods. This way code duplication is
avoided.
+ Updated the documentation.

[23.09.2012]
+ Can see a list of roles assigned to a user.
+ Implemented assigning of roles to a user.
+ Can revoke a role from a user.
+ Editing values of attributes in user info.
+ Status chaning for a user account (active, inactive).
+ The admin can change any user password (old password is not required).
+ Implemented child role, and child permission management for a parent role.

[22.09.2012]
+ Added documentation to the README.md file.
+ Fixed bug where a non-static method was called statically.
+ If userInfo is not defined (i.e. there is no userInfo record connected to a user ID), we display empty strings for
attribute values.
+ Added static methods tableName_s() to models 'SimpleRbacUsersDbTable', and 'SimpleRbacUsersInfoDbTable'.
+ Simplified rules in the admin controller's accessRules() method.

[15.09.2012]
+ Added pages user roles, and user info.
+ Fixed bug in 'SimpleRbacUsersInfoDbTable' model - there was a typo in primary key name.
+ Fixed bug in DB table creation SQL queries - there was a typo in primary key name for table
used by 'SimpleRbacUsersInfoDbTable' model.
+ Methods 'deleteRole', and 'deleteUser' in SRUser now disable deletion of 'admin' user, 'guest' role, and
'authenticated' role.
+ Code clean up in views - removed old, commented-out code.
+ From the 'users' page you can now see related user information by clicking on his username. Also added a button that
will later allow for viewing the roles assigned to a specified user, and their management (deletion, and creation).
+ Removed default assignment of 'authenticated' role to the admin user. Such assignment is not necessary because this
role is automatically assigned if it's business rule returns a positive match.

[10.09.2012]
+ Replaced the Delete icon with a red cross instead of a minus sign.
+ Added ability to create a new role, and a new permission.
+ Modified validation of role and permission creation so that it includes cross checking between role and permission
names. This is important because in the system, roles and permissions are stored as the same object. This means that
you can not have a role and a permission named with the same name (the name is a unique key).
+ Enabled pagination for roles, and permissions.
+ Added cross checking against existing permissions/roles in actual method for creating a new permission, and a new
role.
+ Added ability to delete users, roles, and permissions.

[09.09.2012]
+ added ability to create a new user
+ added pages for creation of a new role, and a new permission; for now forms are missing
+ added image buttons to the users, roles, and privileges table; for now they are not linked to any action
+ added a unique key on the 'username' column in DB table '{{simple_rbac_users}}'

[08.09.2012]
+ creation of default admin user has been moved to a separate method
+ 'authenticated' role is now a child of 'admin' role
+ added to 'Simple_rbacModule' model the current version of the module
+ added to the main layout display of current module version, and a link to it's page on GitHub
+ changed 'Privileges' to 'Permissions'
+ added data provider classes for user roles, and user permissions
+ implemented proper display of 'Roles', and 'Permissions' pages
+ added methods 'createPermission', 'assignPermission', and 'assignChildRole' to SRUser model
+ SRUser method 'createRole' now accepts a parameter to set a description for the role
+ added action 'test' for the purpose of running several tests, and seeing results not in the main layout
+ updated README.md for easier setup process
+ added '.htaccess' files to 'css', and 'images' directories which allow the access of files inside
+ slight change to default role descriptions

[06.09.2012]
+ removed 'index' action; added 'users', 'roles', and 'privileges' actions
+ 'users' action displays a table of users
+ fixed bug where CSS stylesheet was not properly included
+ added a layout (blank page) to display system information (install status, etc.)
+ fixed bug where when you tried to install, it always tried to create default roles, and admin user
+ now when you uninstall, it first logs you out

[05.09.2012]
+ added logout action
+ added variables to AdminController which control the display of blocks in the template
+ added adminIndex.css - CSS styles for main template
+ created a simple admin interface - added ability logout, go to home, page, and menu buttons for future use
+ added checkAccess method to SRUser model

[03.09.2012]
+ made significant changes to install/uninstall process
+ renamed User class to SRUser; this way there will be less confusion with the Yii::app()->user
+ implemented createUser and createRole static methods in SRUser
+ removed unnecessary SimpleRbacInfo* stuff; no need to record to the DB that the module is installed
+ accessing actions in admin controller other than install/uninstall when setup flag is set raises 403 error

[30.08.2012]
+ moved 'UserIdentity.php' class to 'simple_rbac/components'; this way when installing, one does not need to modify
this file
+ moved 'LoginForm.php' form model to 'simple_rbac/models/forms'; this way when installing, one does not need to
modify this file, but needs to use it for login in his controller.
+ added translations for validation error messages to login form.

[29.08.2012]
+ Added automatic generation of a basic 'admin' role, and an administrator.

[28.08.2012]
+ renamed all of the models to include the module's name to minimize the possibility of model name collisions
+ separated models into folders; now they are logically placed by their intended actions
+ finished implementing install action
+ made it so that table names required by this Simpel RABC module have the module's name in them; again, this is to
minimize name collision problems
+ the SQL for table creation takes the table names from the appropriate models; the table names are not hard coded
anymore
+ added automatic creation of tables at install time required by the 'authManager' component.
