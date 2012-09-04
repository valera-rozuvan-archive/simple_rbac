----------------------------------------------
---++++====== Simple RBAC module ======++++---
__________________________for_Yii_____________


Version: 0.4
Date: Mon Sep  3 16:47:22 EEST 2012

Authors:
    REMEDY
    Valera Rozuvan

Contact emails:
    farady@mail.ru
    valera.rozuvan@gmail.com


==-----------------==
|| A - Description ||
==-----------------==



==------------------==
|| B - Installation ||
==------------------==

1.) Extract and place the folder 'simple_rbac' (with all it's contents) into 'protected/modules' folder. If the folder
'modules' does not exist, create it.

2.) In the file 'protected/config/main.php', a few sub-arrays should be modified.

a.) Setup Yii where to find new models, and also use Simple RBAC module:
    ...
    'import' => array(
        ...
        'application.modules.simple_rbac.models.*',
        'application.modules.simple_rbac.components.*',
        'application.modules.simple_rbac.models.forms.*',
        'application.modules.simple_rbac.models.db_tables.*',
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
connection to store the necessary tables and data. For example (in the file 'protected/config/main.php'):

    ...
    'components' => array(
        ...
        'db' => array(
            'class'            => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;port=3306;dbname=test_database',
            'emulatePrepare'   => true,
            'username'         => 'username',
            'password'         => 'password',
            'charset'          => 'utf8',
            'tablePrefix'      => 'tbl_',
        ),
        ...
    ),
    ...

NOTE: Simple RBAC module will work with a database prefix or without it. So, a table prefix is optional, and is not
required. If your database tables do not use a table prefix, set it to an empty string. I.e.:

    'tablePrefix' => '',

This is a must, because when table names are specified wrapped in {{ and }}, and the 'tablePrefix' is not set, Yii
behaves strangely.

c.) Tell the 'authManager' component to use table prefixes by specifying the necessary configuration settings (in the
file 'protected/config/main.php'):

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

3.) Commence the internal install procedure by going to

    http://your-site.com/simple_rbac/admin/install

Modify the URL according to the URL schema used in your Yii project. For example, if you don't have friendly URLs
enabled, then you probably will have to access the install action via:

    http://your-site.com/index.php?r=simple_rbac/admin/install

You will get a list of tables, along with their status ('exists', or 'does not exist'). If all tables are marked as
'exists', then the installation went successfully. If something went wrong, you can try uninstalling (see step 5
below), and repeating the whole process.

4.) If the installation action was successful, comment out, or remove configuration setting:

    'setup' => true,

in the sub-array 'simple_rbac' in file 'protected/config/main.php'. Until you do that, you will not be able to access
the administration panel of Simple RABC module. It is a safety percussion.

5.) To uninstall (i.e. remove all created tables and their data), go to

    http://your-site.com/simple_rbac/admin/uninstall

You should get a list of tables, along with their statuses. If everything went well, each table should be marked as
'does not exist'.

6.) Last but not least, modify the default login action to use this module's SimpleRbacLoginForm model. It should
look something similar to the following:

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

==----------------==
|| C - Change log ||
==----------------==

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
