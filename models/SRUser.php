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

    public static function checkAccess($operation)
    {
        return Yii::app()->authManager->checkAccess($operation, Yii::app()->user->getId());
    }

    public static function createUser($username, $password, $roles = array())
    {
        if (self::getUser($username) !== null)
            return;

        $user = new SimpleRbacUsersDbTable();

        $user->username    = $username;
        $user->password    = $user->hashPassword($password);
        $user->last_access = date('Y-m-d H:i:s', 0);

        if ($user->save()) {
            if (!empty($roles)) {
                $auth = Yii::app()->authManager;

                foreach ($roles as $role) {
                    if (in_array($role, $auth->defaultRoles)) {
                        // The $role is a default role, not assigning.
                        continue;
                    } else if (!in_array($role, array_keys($auth->roles))) {
                        // The role $role does not exist, not assigning.
                        continue;
                    } else if ($auth->checkAccess($role, $user->id)) {
                        // The $role is already assigned to the user with $username.
                        continue;
                    }

                    $auth->assign($role, $user->id);
                }

                $auth->save();
            }
        }
    }

    public static function createRole($role, $description = '')
    {
        $auth = Yii::app()->authManager;

        if (in_array($role, $auth->defaultRoles)) {
            // $role is a default role, not creating
            return;
        } else if (in_array($role, array_keys($auth->roles))) {
            // $role already exists, not creating
            return;
        }

        $auth->createRole($role, $description);

        $auth->save();
    }

    public static function createPermission($permission, $description = '')
    {
        $auth = Yii::app()->authManager;

        if (in_array($permission, array_keys($auth->getAuthItems(0)))) {
            // $permission already exists, not creating
            return;
        }

        $auth->createOperation($permission, $description);

        $auth->save();
    }

    public static function assignPermission($role, $permission)
    {
        $auth = Yii::app()->authManager;

        if (in_array($role, $auth->defaultRoles)) {
            // $role is a default role, not assigning
            return;
        } else if (!in_array($role, array_keys($auth->roles))) {
            // $role does not exist, not assigning
            return;
        } else if (!in_array($permission, array_keys($auth->getAuthItems(0)))) {
            // $permission does not exist, not assigning
            return;
        } else if (in_array($permission, array_keys($auth->roles[$role]->children))) {
            // $permission is already assigned to $role, not assigning
            return;
        }

        $auth->addItemChild($role, $permission);

        $auth->save();
    }

    public static function assignChildRole($role, $childRole)
    {
        $auth = Yii::app()->authManager;

        if (!in_array($role, array_keys($auth->roles))) {
            // $role does not exist, not assigning
            return;
        } else if (!in_array($childRole, array_keys($auth->roles))) {
            // $childRole does not exist, not assigning
            return;
        } else if (in_array($childRole, array_keys($auth->roles[$role]->children))) {
            // $childRole is already assigned to $role, not assigning
            return;
        }

        $auth->addItemChild($role, $childRole);

        $auth->save();
    }
}
