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
        // is setup parameter is set, we don't process the access rules defined below; beforeAction() will handle it;
        // this is important because before this module is installed, there are no roles, users, or permissions;
        if (Yii::app()->controller->module->setupMode())
            return array();

        return array(
            array(
                // If the user has the 'admin' roles assigned to him, allow all actions.
                'allow',
                'roles'   => array('admin',),
            ),
            array(
                // If none of the above rules match, deny all access to all actions.
                'deny',
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
                     'username'   => $_GET['username'],
                     'userInfoDP' => $userInfoDP,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a user.');
        }
    }

    public function actionChangeUserInfoAttributeValue()
    {
        if ((isset($_GET['username'])) && (isset($_GET['attribute']))) {
            $model = new SimpleRbacChangeUserInfoAttributeValueForm();

            if (isset($_POST['SimpleRbacChangeUserInfoAttributeValueForm'])) {
                $model->attributes = $_POST['SimpleRbacChangeUserInfoAttributeValueForm'];

                // We must validate username before we validate the role. This is because if username is invaid, then
                // role validation will raise an error (we check if the role is already assigned to the specified
                // username).
                if ($model->validate()) {
                    SRUser::changeUserInfoAttributeValue($model->username, $model->attribute, $model->value);
                    $this->redirect(array('admin/userInfo', 'username' => $model->username,));
                }
            }

            $this->render(
                'changeUserInfoAttributeValue',
                array(
                     'username'  => $_GET['username'],
                     'attribute' => $_GET['attribute'],
                     'oldValue'  => SRUser::getUserInfoAttributeValue($_GET['username'], $_GET['attribute']),
                     'model'     => $model,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a user and/or attribute.');
        }
    }

    public function actionUserRoles()
    {
        if (isset($_GET['username'])) {
            $userRolesDP = new SimpleRbacUserRolesDataP(
                $_GET['username'],
                array(
                     'pagination' => array(
                         'pageSize' => 4,
                     ),
                )
            );

            $this->render(
                'userRoles',
                array(
                     'modulePath'  => $this->modulePath,
                     'username'    => $_GET['username'],
                     'userRolesDP' => $userRolesDP,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a username.');
        }
    }

    public function actionChildRoles()
    {
        if (isset($_GET['roleName'])) {
            $childRolesDP = new SimpleRbacChildRolesDataP(
                $_GET['roleName'],
                array(
                     'pagination' => array(
                         'pageSize' => 4,
                     ),
                )
            );

            $this->render(
                'childRoles',
                array(
                     'modulePath'  => $this->modulePath,
                     'roleName'    => $_GET['roleName'],
                     'childRolesDP' => $childRolesDP,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a role.');
        }
    }

    public function actionAssignChildRole()
    {
        if (isset($_GET['parentRole'])) {
            $model = new SimpleRbacAssignChildRoleForm();

            if (isset($_POST['SimpleRbacAssignChildRoleForm'])) {
                $model->attributes = $_POST['SimpleRbacAssignChildRoleForm'];

                // We must validate the parent role before we validate the child role. This is because if parent role
                // is invalid, validation will raise an error (we check if child role is already assigned to parent
                // role).
                if (($model->validate(array('parentRole'))) && ($model->validate(array('childRole')))) {
                    SRUser::assignChildRole($model->parentRole, $model->childRole);
                    $this->redirect(array('admin/childRoles', 'roleName' => $model->parentRole,));
                }
            }

            $this->render(
                'assignChildRole',
                array(
                     'parentRole' => $_GET['parentRole'],
                     'model'      => $model,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a parent role.');
        }
    }

    public function actionRemoveChildRole()
    {
        if (isset($_GET['ajax'])) {
            if ((isset($_GET['parentRole'])) && (isset($_GET['childRole']))) {
                SRUser::removeChildRole($_GET['parentRole'], $_GET['childRole']);
            }

            Yii::app()->end();
        } else {
            throw new CHttpException(403, 'You can\'t access this page directly.');
        }
    }

    public function actionChildPermissions()
    {
        if (isset($_GET['roleName'])) {
            $childPermissionsDP = new SimpleRbacChildPermissionsDataP(
                $_GET['roleName'],
                array(
                     'pagination' => array(
                         'pageSize' => 4,
                     ),
                )
            );

            $this->render(
                'childPermissions',
                array(
                     'modulePath' => $this->modulePath,
                     'roleName'   => $_GET['roleName'],
                     'childPermissionsDP' => $childPermissionsDP,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a role.');
        }
    }

    public function actionAssignChildPermission()
    {
        if (isset($_GET['parentRole'])) {
            $model = new SimpleRbacAssignChildPermissionForm();

            if (isset($_POST['SimpleRbacAssignChildPermissionForm'])) {
                $model->attributes = $_POST['SimpleRbacAssignChildPermissionForm'];

                // We must validate the parent role before we validate the child permission. This is because if parent
                // role is invalid, validation will raise an error (we check if child permission is already assigned
                // to parent role).
                if (($model->validate(array('parentRole'))) && ($model->validate(array('childPermission')))) {
                    SRUser::assignChildPermission($model->parentRole, $model->childPermission);
                    $this->redirect(array('admin/childPermissions', 'roleName' => $model->parentRole,));
                }
            }

            $this->render(
                'assignChildPermission',
                array(
                     'parentRole' => $_GET['parentRole'],
                     'model'      => $model,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a parent role.');
        }
    }

    public function actionRemoveChildPermission()
    {
        if (isset($_GET['ajax'])) {
            if ((isset($_GET['parentRole'])) && (isset($_GET['childPermission']))) {
                SRUser::removeChildPermission($_GET['parentRole'], $_GET['childPermission']);
            }

            Yii::app()->end();
        } else {
            throw new CHttpException(403, 'You can\'t access this page directly.');
        }
    }

    public function actionAssignRoleToUser()
    {
        if (isset($_GET['username'])) {
            $model = new SimpleRbacAssignRoleToUserForm();

            if (isset($_POST['SimpleRbacAssignRoleToUserForm'])) {
                $model->attributes = $_POST['SimpleRbacAssignRoleToUserForm'];

                // We must validate username before we validate the role. This is because if username is invaid, then
                // role validation will raise an error (we check if the role is already assigned to the specified
                // username).
                if (($model->validate(array('username'))) && ($model->validate(array('role')))) {
                    SRUser::assignRoleToUser($model->username, $model->role);
                    $this->redirect(array('admin/userRoles', 'username' => $model->username,));
                }
            }

            $this->render(
                'assignRoleToUser',
                array(
                     'username' => $_GET['username'],
                     'model'    => $model,
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a username.');
        }
    }

    public function actionRevokeRoleFromUser()
    {
        if (isset($_GET['ajax'])) {
            if ((isset($_GET['username'])) && (isset($_GET['role']))) {
                SRUser::revokeRoleFromUser($_GET['username'], $_GET['role']);
            }

            Yii::app()->end();
        } else {
            throw new CHttpException(403, 'You can\'t access this page directly.');
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

    public function actionSwitchUserStatus()
    {
        if ((isset($_GET['username'])) && (isset($_GET['status']))) {
            SRUser::switchUserStatus($_GET['username'], $_GET['status']);

            $this->redirect(array('admin/users',));
        } else {
            throw new CHttpException(403, 'Username and/opr status was not specified.');
        }
    }

    public function actionChangePassword()
    {
        if (isset($_GET['username'])) {
            $model = new SimpleRbacChangePasswordForm();

            if (isset($_POST['SimpleRbacChangePasswordForm'])) {
                $model->attributes = $_POST['SimpleRbacChangePasswordForm'];

                if (
                    ($model->validate(array('username'))) &&
                    ($model->validate(array('newPassword1'))) &&
                    ($model->validate(array('newPassword2')))
                ) {
                    SRUser::changePassword($model->username, $model->newPassword1);
                    $this->redirect(array('admin/userInfo', 'username' => $model->username,));
                }
            }

            $this->render(
                'changePassword',
                array(
                     'model' => $model,
                     'username' => $_GET['username'],
                )
            );
        } else {
            throw new CHttpException(403, 'You did not specify a username.');
        }
    }
}
