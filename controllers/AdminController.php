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

    // public $defaultAction = 'index';

    public $modulePath = '/protected/modules/simple_rbac/';

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
                'actions' => array('index', 'logout',),
                'roles'   => array('admin',),
            ),
            array(
                'deny',
                'actions' => array('index', 'logout'),
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
        } else {
            if (in_array($action->id, array('install', 'uninstall',))) {
                throw new CHttpException(403, 'The specified page cannot be found.');

                return false;
            }
        }

        return true;
    }

    public function actionIndex()
    {
        $this->render(
            'index',
            array()
        );
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
}
