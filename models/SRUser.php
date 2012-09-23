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

    public static function createRole($role, $description = '')
    {
        $auth = Yii::app()->authManager;

        if (in_array($role, $auth->defaultRoles)) {
            // $role is a default role, not creating
            return;
        } else if (in_array($role, array_keys($auth->roles))) {
            // $role already exists, not creating
            return;
        } else if (in_array($role, array_keys($auth->getAuthItems(0)))) {
            // $role is already a permission; can't have the same named role and permission
            return;
        }

        $auth->createRole($role, $description);

        $auth->save();
    }

    public static function deleteRole($role)
    {
        if (in_array($role, array('guest', 'authenticated',))) {
            // The 'guest', and 'authenticated' roles can't be deleted.
            return;
        }

        $auth = Yii::app()->authManager;

        if (in_array($role, $auth->defaultRoles)) {
            // $role is a default role, not deleting
            return;
        } else if (!in_array($role, array_keys($auth->roles))) {
            // $role does not exists, not deleting
            return;
        }

        $auth->removeAuthItem($role);

        // NOTE: We must not call $auth->save() because then it restores to the DB the initial state of authManager.
        // I.e. - it will un-delete the role we just deleted! We certainly don't want that happening =)
    }

    public static function assignRoleToUser($username, $role)
    {
        $user = self::getUser($username);
        if ($user === null) {
            // The specified $username does not belong to a user.
            return;
        }

        $auth = Yii::app()->authManager;

        if ($auth->isAssigned($role, $user->id)) {
            // The specified $role is already assigned to the specified $username
            return;
        }

        // We can't assign a default role manually.
        if (in_array($role, array('guest', 'authenticated',))) {
            // Can't assign default roles. They are assigned and revoked automatically based on their business rule.
            return;
        }

        $auth->assign($role, $user->id);
    }

    public static function revokeRoleFromUser($username, $role)
    {
        $user = self::getUser($username);
        if ($user === null) {
            // The specified $username does not belong to a user.
            return;
        }

        $auth = Yii::app()->authManager;

        if (!$auth->isAssigned($role, $user->id)) {
            // The specified $role is not assigned to the specified $username
            return;
        }

        // This is probably an unnecessary check, because we can't assign a default role manually, and therefore
        // the isAssigned() method above will not return true for it. I.e. the above test will handle the
        // case when $role is a default role. But just for completeness, we will also have a specific test for
        // default roles.
        if (in_array($role, array('guest', 'authenticated',))) {
            // Can't revoke default roles. They are assigned and revoked automatically based on a business rule.
            return;
        }

        if ((intval($user->id) === 1) && ($role === 'admin')) {
            // Revoking the 'admin' role from user with ID of 1 is not allowed. He is the top administrator, and
            // there always needs ot be at least one administrator user.
            return;
        }

        $auth->revoke($role, $user->id);
    }

    public static function createPermission($permission, $description = '')
    {
        $auth = Yii::app()->authManager;

        if (in_array($permission, array_keys($auth->getAuthItems(0)))) {
            // $permission already exists, not creating
            return;
        } else if (in_array($permission, array_keys($auth->roles))) {
            // $permission is already a role; can't have the same named permission and role
            return;
        }

        $auth->createOperation($permission, $description);

        $auth->save();
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

    public static function assignPermission($role, $permission)
    {
        $auth = Yii::app()->authManager;

        if (!in_array($role, array_keys($auth->roles))) {
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

    public static function removeChildPermission($role, $permission)
    {
        $auth = Yii::app()->authManager;

        if (!in_array($role, array_keys($auth->roles))) {
            // $role does not exist, not removing
            return;
        } else if (!in_array($permission, array_keys($auth->getAuthItems(0)))) {
            // $permission does not exist, not removing
            return;
        } else if (!in_array($permission, array_keys($auth->roles[$role]->children))) {
            // $permission is not assigned to $role, not removing
            return;
        }

        $auth->removeItemChild($role, $permission);

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
        } else if (in_array($childRole, array('guest', 'authenticated',))) {
            // Assigning default roles as child oles is not allowed.
            return;
        }

        $auth->addItemChild($role, $childRole);

        $auth->save();
    }

    public static function removeChildRole($role, $childRole)
    {
        $auth = Yii::app()->authManager;

        if (!in_array($role, array_keys($auth->roles))) {
            // $role does not exist, not removing
            return;
        } else if (!in_array($childRole, array_keys($auth->roles))) {
            // $childRole does not exist, not removing
            return;
        } else if (!in_array($childRole, array_keys($auth->roles[$role]->children))) {
            // $childRole is not assigned to $role, not removing
            return;
        }

        $auth->removeItemChild($role, $childRole);

        $auth->save();
    }

    public static function getUserInfoAttributeValue($username, $attribute)
    {
        $user = SRUser::getUser($username);
        if ($user === null) {
            // The $username does not belong to any user.
            return;
        }

        if (!in_array($attribute, array_keys(SimpleRbacUsersInfoDbTable::model()->getAttributes()))) {
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
