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
    public static function userExists($username)
    {
        $sru = new SimpleRbacUsersDbTable();

        if ($sru->findByAttributes(array('username' => $username,)) === null) {
            // The user was not found.
            return false;
        }

        // The user exits.
        return true;
    }

    public static function getUser($username)
    {
        $sru = new SimpleRbacUsersDbTable();

        return $sru->findByAttributes(array('username' => $username,));
    }

    public static function getUserId($username)
    {
        $sru = new SimpleRbacUsersDbTable();

        $user = $sru->findByAttributes(array('username' => $username,));
        if ($user === null)
            return null;

        return intval($user->id);
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

    public static function deleteUser($username)
    {
        $user = SRUser::getUser($username);
        if ($user === null) {
            // $username does not belong to any user
            return;
        }

        if (intval($user->id) === 1) {
            // Deleting user with ID of 1 is not allowed.
            return;
        }

        $auth = Yii::app()->authManager;
        $userRoles = array_keys($auth->getAuthItems(2, $user->id));
        foreach ($userRoles as $userRole)
            $auth->revoke($userRole, $user->id);
        $auth->save();

        $user->delete();
    }

    /*
     * Roles are carried over from the Yii RBAC definition of 'roles'. They are type 2 authorization items.
     *
     * http://www.yiiframework.com/doc/api/1.1/IAuthManager#getAuthItems-detail
     */
    public static function isRole($authItem)
    {
        return in_array($authItem, array_keys(Yii::app()->authManager->getAuthItems(2)));
    }

    /*
     * Check against a list of role names that are assigned to all users implicitly. These roles do not need to be
     * explicitly assigned to any user.
     *
     * http://www.yiiframework.com/doc/api/1.1/CAuthManager#defaultRoles-detail
     */
    public static function isDefaultRole($authItem)
    {
        return in_array($authItem, Yii::app()->authManager->defaultRoles);
    }

    /*
     * These roles are assumed to be always present by this module. They are created at install time. They should
     * never be deleted.
     */
    public static function isSpecialRole($role)
    {
        return in_array($role, array('admin', 'guest', 'authenticated',));
    }

    /*
     * Permissions are carried over from the Yii RBAC definition of 'operations'. They are type 0 authorization items.
     *
     * http://www.yiiframework.com/doc/api/1.1/IAuthManager#getAuthItems-detail
     */
    public static function isPermission($authItem)
    {
        return in_array($authItem, array_keys(Yii::app()->authManager->getAuthItems(0)));
    }

    public static function createRole($role, $description = '')
    {
        if (self::isRole($role)) {
            // $role already exists, not creating
            return;
        } else if (self::isDefaultRole($role)) {
            // $role is a default role, not creating
            return;
        } else if (self::isPermission($role)) {
            // $role is already a permission; can't have the same named role and permission
            return;
        }

        Yii::app()->authManager->createAuthItem($role, 2, $description);
        Yii::app()->authManager->save();
    }

    public static function deleteRole($role)
    {
        if (self::isSpecialRole($role)) {
            // Some roles are required by this module. You can't delete them.
            return;
        } else if (self::isDefaultRole($role)) {
            // $role is a default role, not deleting
            return;
        } else if (!self::isRole($role)) {
            // $role does not exists, not deleting
            return;
        }

        Yii::app()->authManager->removeAuthItem($role);

        // NOTE: We must not call Yii::app()->authManager->save() because then it restores to the DB the initial
        // state of authManager. I.e. - it will un-delete the role we just deleted! We certainly don't want that
        // happening =)
    }

    public static function roleIsAssignedToUser($username, $role)
    {
        if (!Yii::app()->authManager->isAssigned($role, self::getUserId($username))) {
            // The specified $role is already assigned to the specified $username
            return false;
        }

        // The specified $role is assigned to the specified $username
        return true;
    }

    public static function assignRoleToUser($username, $role)
    {
        if (!self::userExists($username)) {
            // The specified $username does not belong to a user.
            return;
        }

        if (self::roleIsAssignedToUser($username, $role)) {
            // The specified $role is already assigned to the specified $username
            return;
        }

        // We can't assign a default role manually.
        if (self::isDefaultRole($role)) {
            // Can't assign default roles. They are assigned and revoked automatically based on their business rule.
            return;
        }

        Yii::app()->authManager->assign($role, self::getUserId($username));
    }

    public static function revokeRoleFromUser($username, $role)
    {
        if (!self::userExists($username)) {
            // The specified $username does not belong to a user.
            return;
        }

        if (!self::roleIsAssignedToUser($username, $role)) {
            // The specified $role is not assigned to the specified $username
            return;
        }

        if (self::isDefaultRole($role)) {
            // Can't revoke default roles. They are assigned and revoked automatically based on a business rule.
            return;
        }

        if ((intval(self::getUserId($username)) === 1) && ($role === 'admin')) {
            // Revoking the 'admin' role from user with ID of 1 is not allowed. He is the top administrator, and
            // there always needs ot be at least one administrator user.
            return;
        }

        Yii::app()->authManager->revoke($role, self::getUserId($username));
    }

    public static function createPermission($permission, $description = '')
    {
        if (self::isPermission($permission)) {
            // $permission already exists, not creating
            return;
        } else if (self::isRole($permission)) {
            // $permission is already a role; can't have the same named permission and role
            return;
        }

        Yii::app()->authManager->createAuthItem($permission, 0, $description);
        Yii::app()->authManager->save();
    }

    public static function deletePermission($permission)
    {
        $auth = Yii::app()->authManager;

        if (!in_array($permission, array_keys($auth->getAuthItems(0)))) {
            // $permission does not exists, not deleting
            return;
        }

        $auth->removeAuthItem($permission);

        // NOTE: We must not call $auth->save() because then it restores to the DB the initial state of authManager.
        // I.e. - it will un-delete the permission we just deleted! We certainly don't want that happening =)
    }

    public static function isChildOfRole($role, $permission)
    {
        $availableRoles = Yii::app()->authManager->getAuthItems(2);
        return in_array($permission, array_keys($availableRoles[$role]->children));
    }

    public static function assignChildPermission($role, $permission)
    {
        if (!self::isRole($role)) {
            // $role does not exist, not assigning
            return;
        } else if (!self::isPermission($permission)) {
            // $permission does not exist, not assigning
            return;
        } else if (self::isChildOfRole($role, $permission)) {
            // $permission is already assigned to $role, not assigning
            return;
        }

        Yii::app()->authManager->addItemChild($role, $permission);
        Yii::app()->authManager->save();
    }

    public static function removeChildPermission($role, $permission)
    {
        if (!self::isRole($role)) {
            // $role does not exist, not removing
            return;
        } else if (!self::isPermission($permission)) {
            // $permission does not exist, not removing
            return;
        } else if (!self::isChildOfRole($role, $permission)) {
            // $permission is not assigned to $role, not removing
            return;
        }

        Yii::app()->authManager->removeItemChild($role, $permission);
        Yii::app()->authManager->save();
    }

    public static function assignChildRole($role, $childRole)
    {
        if (!self::isRole($role)) {
            // $role does not exist, not assigning
            return;
        } else if (!self::isRole($childRole)) {
            // $childRole does not exist, not assigning
            return;
        } else if (self::isChildOfRole($role, $childRole)) {
            // $childRole is already assigned to $role, not assigning
            return;
        } else if (self::isDefaultRole($childRole)) {
            // Assigning default roles as child oles is not allowed.
            return;
        }

        Yii::app()->authManager->addItemChild($role, $childRole);
        Yii::app()->authManager->save();
    }

    public static function removeChildRole($role, $childRole)
    {
        if (!self::isRole($role)) {
            // $role does not exist, not removing
            return;
        } else if (!self::isRole($childRole)) {
            // $childRole does not exist, not removing
            return;
        } else if (!self::isChildOfRole($role, $childRole)) {
            // $childRole is not assigned to $role, not removing
            return;
        }

        Yii::app()->authManager->removeItemChild($role, $childRole);
        Yii::app()->authManager->save();
    }

    /*
     * Special UserInfo attributes are required by this module. Their value can't be modified by the user. They
     * can't be deleted from the DB table definition.
     */
    public static function isSpecialUserInfoAttribute($attribute)
    {
        return in_array($attribute, array('user_id',));
    }

    public static function userInfoAttributeExists($attribute)
    {
        if (!in_array($attribute, array_keys(SimpleRbacUsersInfoDbTable::model()->getAttributes()))) {
            // The attribute does not exist in the DB table.
            return false;
        }

        return true;
    }

    public static function getUserInfoAttributeValue($username, $attribute)
    {
        $user = SRUser::getUser($username);
        if ($user === null) {
            // The $username does not belong to any user.
            return;
        }

        if (self::userInfoAttributeExists($attribute)) {
            // The attribute does not exist in the DB table.
            return;
        }

        // If userInfo has not been initialized yet, return empty string.
        if (!isset($user->userInfo->user_id)) {
            return '';
        }

        return $user->userInfo->{$attribute};
    }

    public static function changeUserInfoAttributeValue($username, $attribute, $value)
    {
        $user = SRUser::getUser($username);
        if ($user === null) {
            // The $username does not belong to any user.
            return;
        }

        if ($attribute === 'user_id') {
            // This attribute can't be changed. It contains the ID of the user.
            return;
        }

        if (!in_array($attribute, array_keys(SimpleRbacUsersInfoDbTable::model()->getAttributes()))) {
            // The attribute does not exist in the DB table.
            return;
        }

        // If userInfo has not been initialized yet, initialize it.
        if (!isset($user->userInfo->user_id)) {
            $userInfo = new SimpleRbacUsersInfoDbTable();
            $userInfo->user_id = $user->id;
            $userInfo->save();

            // Reload the $user object.
            $user = SRUser::getUser($username);
        }

        $user->userInfo->{$attribute} = $value;
        $user->userInfo->save();
    }

    public static function switchUserStatus($username, $status)
    {
        $user = SRUser::getUser($username);
        if ($user === null) {
            // The $username does not belong to any user.
            return;
        }

        if (!in_array(intval($status), array(0, 1,))) {
            // Currently, only status of 0 (inactive), and 1 (active) is accepted.
            return;
        }

        if (intval($user->id) === 1) {
            // Changing the status of user with ID of 1 is not allowed. He is the top administrator, and
            // there always needs ot be at least one active administrator user.
            return;
        }

        $user->status = $status;
        $user->save();
    }

    public static function changePassword($username, $newPassword)
    {
        $user = SRUser::getUser($username);
        if ($user === null) {
            // The $username does not belong to any user.
            return;
        }

        $user->password = $user->hashPassword($newPassword);
        $user->save();
    }

    public static function getChildRoles($roleName)
    {
        $auth = Yii::app()->authManager;

        if (!in_array($roleName, array_keys($auth->roles))) {
            // The $role does not exist.
            return;
        }

        $role = $auth->roles[$roleName];

        $allRoles = array_keys($auth->getAuthItems(2));

        $children = array_keys($role->children);
        $childRoles = array();

        foreach ($children as $child) {
            if (in_array($child, $allRoles)) {
                $childRoles[] = $child;
            }
        }

        return $childRoles;
    }

    public static function getChildPermissions($roleName)
    {
        $auth = Yii::app()->authManager;

        if (!in_array($roleName, array_keys($auth->roles))) {
            // The $role does not exist.
            return array();
        }

        $role = $auth->roles[$roleName];

        $allPermissions = array_keys($auth->getAuthItems(0));

        $children = array_keys($role->children);
        $childPermissions = array();

        foreach ($children as $child) {
            if (in_array($child, $allPermissions)) {
                $childPermissions[] = $child;
            }
        }

        return $childPermissions;
    }
}
