<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep  8 09:57:58 EEST 2012
 * 
 * File:      SimpleRbacRolesDataP.php
 * Full path: protected/modules/simple_rbac/models/data_providers/SimpleRbacRolesDataP.php
 *
 * Description: Will provide roles and their information. Will be used for grid display.
 */

class SimpleRbacRolesDataP extends CDataProvider
{
    private $_data;
    private $_data_keys;

    public function __construct()
    {
        $this->refreshData();

        $this->_data_keys = array(
            'name', 'default', 'description', 'childRoles', 'permissions',
        );
    }

    /**
     * Fetches the data from the persistent data storage.
     * @return array list of data items
     */
    protected function fetchData()
    {
        $this->refreshData();

        return $this->_data;
    }

    /**
     * Fetches the data item keys from the persistent data storage.
     * @return array list of data item keys.
     */
    protected function fetchKeys()
    {
        return array_keys($this->_data_keys);
    }

    /**
     * Calculates the total number of data items.
     * @return integer the total number of data items.
     */
    protected function calculateTotalItemCount()
    {
        return count($this->_data);
    }

    public function refreshData()
    {
        $auth = Yii::app()->authManager;

        $this->_data = array();

        foreach ($auth->roles as $role) {
            $default = '';
            if (in_array($role->name, $auth->defaultRoles))
                $default = 'y';

            $allPermissions = array_keys($auth->getAuthItems(0));
            $allRoles = array_keys($auth->getAuthItems(2));

            $children = array_keys($role->children);
            $childRoles = array();
            $permissions = array();

            foreach ($children as $child) {
                if (in_array($child, $allPermissions)) {
                    $permissions[] = $child;
                } else if (in_array($child, $allRoles)) {
                    $childRoles[] = $child;
                }
            }

            $this->_data[] = array(
                'name'        => $role->name,
                'default'     => $default,
                'description' => $role->description,
                'childRoles'  => implode(', ', $childRoles),
                'permissions' => implode(', ', $permissions),
            );
        }
    }
}
