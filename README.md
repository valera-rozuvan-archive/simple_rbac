----------------------------------------------
---++++====== Simple RBAC module ======++++---

---++++================= for Yii ======++++---

Version:

    1.0

Date:

    Mon Sep 10 06:38:11 EEST 2012

Authors:

    REMEDY
    Valera Rozuvan



==-----------------==
|| A - Description ||
==-----------------==



==------------------==
|| B - Installation ||
==------------------==

1.) Extract and place the folder 'simple_rbac' (with all it's contents) into 'protected/modules' folder. If the folder
'modules' does not exist, create it.

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
    ),
    ...
    'modules' => array(
       ...
       'simple_rbac' => array(
           'setup' => true,
       ),
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

This assumes that there is a MysSQL server running at localhost (port 3306), and that a database named 'test_database'
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
'D - Uninstall' below), and repeating the whole process.

If the installation action was successful, comment out (or remove) the following configuration setting:

    'setup' => true,

in the sub-array 'simple_rbac' in file 'protected/config/main.php'. Until you do that, you will not be able to access
the administration panel of Simple RABC module. It is a safety percussion.

4.) Modify the site's controller default login action (or whatever action used for logging in at your site) to use
this module's 'SimpleRbacLoginForm' model. It should look something similar to the following:

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new SimpleRbacLoginForm();

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['SimpleRbacLoginForm'])) {
            $model->attributes = $_POST['SimpleRbacLoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        // display the login form
        $this->render('login', array('model'=> $model));
    }

5.) Optionally, add a menu entry for easy access to the Simple RBAC module's administration page:

    $this->widget(
        'zii.widgets.CMenu',
        array(
            'items' => array(
                ...
                array(
                    'label'   => Yii::t('main','Admin'),
                    'url'     => array('/simple_rbac/admin/users'),
                    'visible' => SRUser::checkAccess('admin')
                ),
                ...
            ),
        )
    );



==---------------==
|| C - Uninstall ||
==---------------==

To uninstall (i.e. remove all created tables and their data), go to:

    http://your-site.com/simple_rbac/admin/uninstall

You should get a list of tables, along with their statuses. If everything went well, each table should be marked as
'does not exist'.

After the uninstall action has run successfully, you can remove the module's directory

    protected/modules/simple_rbac/

and undo all the changes that you have made to the 'protected/config/main.php' file, and your login action.



==-----------==
|| D - Usage ||
==-----------==



==----------------==
|| E - Change log ||
==----------------==

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
