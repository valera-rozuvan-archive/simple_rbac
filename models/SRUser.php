<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Aug 25 18:17:02 EEST 2012
 * 
 * File:      SRUser.php
 * Full path: protected/modules/simple_rbac/models/SRUser.php
 *
 * Description: This class wil provide methods for working with users. Creation, deletion, and other stuff will be
 * handled here.
 */

class SRUser
{
    public static function getUser($username)
    {
        $sru = new SimpleRbacUsersDbTable();

        return $sru->findByAttributes(array('username' => $username,));
    }

    public static function createUser($username, $password, $roles = array())
    {
        $user = new SimpleRbacUsersDbTable();

        $user->username    = $username;
        $user->password    = $user->hashPassword($password);
        $user->last_access = date('Y-m-d H:i:s', time());

        if ($user->save()) {
            if (!empty($roles)) {
                $auth = Yii::app()->authManager;

                foreach ($roles as $role) {
                    if (in_array($role, $auth->defaultRoles)) {
                        // $role is a default role, not assigning
                        continue;
                    } else if (!in_array($role, array_keys($auth->roles))) {
                        // $role does not exist, not assigning
                        continue;
                    } else if ($auth->checkAccess($role, $user->id)) {
                        // $role is already assigned to the user
                        continue;
                    }

                    $auth->assign($role, $user->id);
                }

                $auth->save();
            }
        }
    }

    public static function createRole($role)
    {
        $auth = Yii::app()->authManager;

        if (in_array($role, $auth->defaultRoles)) {
            // $role is a default role, not creating
            return;
        } else if (in_array($role, array_keys($auth->roles))) {
            // $role already exists, not creating
            return;
        }

        $auth->createRole($role);

        $auth->save();
    }

    public static function checkAccess($operation)
    {
        return Yii::app()->authManager->checkAccess($operation, Yii::app()->user->getId());
    }
}