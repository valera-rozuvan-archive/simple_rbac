<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Aug 25 17:08:54 EEST 2012
 * 
 * File:      AdminController.php
 * Full path: protected/modules/simple_rbac/controllers/AdminController.php
 *
 * Description: This is the main controller for the Simple RBAC module. It will provide actions for installing the
 * module, and managing users along with their roles and permissions.
 */

class AdminController extends CController
{
    public $layout = 'main_rbac';

    public $defaultAction = 'users';

    public $modulePath = '/protected/modules/simple_rbac';

    public $useHeader = true;
    public $useBody   = true;
    public $useMenu   = true;
    public $useChart  = true;
    public $useFooter = true;

    public function actions()
    {
        return array();
    }

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        // is setup parameter is set, we don't process the access rules defined below; beforeAction() will handle it
        if (Yii::app()->controller->module->setupMode())
            return array();

        return array(
            array(
                'allow',
                'actions' => array(
                    'users', 'roles', 'permissions', 'userInfo', 'userRoles',
                    'logout', 'test',
                    'newUser', 'newRole', 'newPermission',
                    'delete',
                ),
                'roles'   => array('admin',),
            ),
            array(
                'deny',
                'actions' => array(
                    'users', 'roles', 'permissions', 'userInfo', 'userRoles',
                    'logout', 'test',
                    'newUser', 'newRole', 'newPermission',
                    'delete',
                ),
                'users'   => array('*',),
            ),
        );
    }

    public function beforeAction($action)
    {
        if (Yii::app()->controller->module->setupMode()) {
            if (!in_array($action->id, array('install', 'uninstall',))) {
                throw new CHttpException(403, "The configuration parameter 'setup' from this module's settings sub-array ['modules']['simple_rbac'] in 'protected/config/main.php' is set.");

                return false;
            }
            $this->layout = 'blank';
        } else {
            if (in_array($action->id, array('install', 'uninstall',))) {
                throw new CHttpException(403, 'The specified page cannot be found.');

                return false;
            }
        }

        return true;
    }

    public function actionUsers()
    {
        $dataProvider = new CActiveDataProvider(
            'SimpleRbacUsersDbTable',
            array(
                 'criteria' => array(
                     'select' => 'id, username, status, last_access, registered',
                 ),
                 'pagination' => array(
                     'pageSize' => 4,
                 ),
            )
        );

        $this->render(
            'users',
            array(
                 'modulePath'   => $this->modulePath,
                 'dataProvider' => $dataProvider,
            )
        );
    }

    public function actionRoles()
    {
        $rolesDP = new SimpleRbacRolesDataP(
            array(
                 'pagination' => array(
                     'pageSize' => 4,
                 ),
            )
        );

        $this->render(
            'roles',
            array(
                 'modulePath'   => $this->modulePath,
                 'rolesDP' => $rolesDP,
            )
        );
    }

    public function actionPermissions()
    {
        $permissionsDP = new SimpleRbacPermissionsDataP(
            array(
                 'pagination' => array(
                     'pageSize' => 4,
                 ),
            )
        );

        $this->render(
            'permissions',
            array(
                 'modulePath'   => $this->modulePath,
                 'permissionsDP' => $permissionsDP,
            )
        );
    }

    public function actionUserInfo()
    {
        if (isset($_GET['username'])) {
            $userInfoDP = new SimpleRbacUserInfoDataP(
                $_GET['username'],
                array(
                     'pagination' => array(
                         'pageSize' => 4,
                     ),
                )
            );

            $this->render(
                'userInfo',
                array(
                     'modulePath' => $this->modulePath,
                     'userInfoDP' => $userInfoDP,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a user.');
        }
    }

    public function actionUserRoles()
    {
        if (isset($_GET['username'])) {
            /*
            $userRolesDP = new SimpleRbacUserRolesDataP(
                $_GET['username'],
                array(
                     'pagination' => array(
                         'pageSize' => 4,
                     ),
                )
            );
            */

            $this->render(
                'userRoles',
                array(
                     'modulePath'  => $this->modulePath,
                     // 'userRolesDP' => $userRolesDP,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a username.');
        }
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionInstall()
    {
        $tableStatus = Yii::app()->controller->module->install();

        $this->render(
            'moduleTableStatus',
            array(
                 'tableStatus' => $tableStatus,
            )
        );
    }

    public function actionUninstall()
    {
        $tableStatus = Yii::app()->controller->module->uninstall();

        $this->render(
            'moduleTableStatus',
            array(
                 'tableStatus' => $tableStatus,
            )
        );
    }

    public function actionTest()
    {
        $this->layout = 'blank';

        $this->render(
            'test',
            array()
        );
    }

    public function actionNewUser()
    {
        $model = new SimpleRbacNewUserForm();

        if (isset($_POST['SimpleRbacNewUserForm'])) {
            $model->attributes = $_POST['SimpleRbacNewUserForm'];

            if ($model->validate()) {
                SRUser::createUser($model->username, $model->password);
                $this->redirect(array('admin/users',));
            }
        }

        $this->render(
            'newUser',
            array(
                 'model' => $model,
            )
        );
    }

    public function actionNewRole()
    {
        $model = new SimpleRbacNewRoleForm();

        if (isset($_POST['SimpleRbacNewRoleForm'])) {
            $model->attributes = $_POST['SimpleRbacNewRoleForm'];

            if ($model->validate()) {
                SRUser::createRole($model->roleName, $model->description);
                $this->redirect(array('admin/roles',));
            }
        }

        $this->render(
            'newRole',
            array(
                 'model' => $model,
            )
        );
    }

    public function actionNewPermission()
    {
        $model = new SimpleRbacNewPermissionForm();

        if (isset($_POST['SimpleRbacNewPermissionForm'])) {
            $model->attributes = $_POST['SimpleRbacNewPermissionForm'];

            if ($model->validate()) {
                SRUser::createPermission($model->permissionName, $model->description);
                $this->redirect(array('admin/permissions',));
            }
        }

        $this->render(
            'newPermission',
            array(
                 'model' => $model,
            )
        );
    }

    public function actionDelete()
    {
        if (isset($_GET['ajax'])) {
            if ((isset($_GET['type'])) && (isset($_GET['name']))) {
                switch ($_GET['type']) {
                    case 'user':
                        SRUser::deleteUser($_GET['name']);
                        break;
                    case 'role':
                        SRUser::deleteRole($_GET['name']);
                        break;
                    case 'permission':
                        SRUser::deletePermission($_GET['name']);
                        break;
                }
            }

            Yii::app()->end();
        } else {
            throw new CHttpException(403, 'You can\'t access this page directly.');
        }
    }
}
