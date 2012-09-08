<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep  8 12:46:23 EEST 2012
 * 
 * File:      SimpleRbacPermissionsDataP.php
 * Full path: protected/modules/simple_rbac/models/data_providers/SimpleRbacPermissionsDataP.php
 *
 * Description: Will provide permissions and their information. Will be used for grid display.
 */

class SimpleRbacPermissionsDataP extends CDataProvider
{
    private $_data;
    private $_data_keys;

    public function __construct()
    {
        $this->refreshData();

        $this->_data_keys = array(
            'name', 'description',
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

        foreach ($auth->getAuthItems(0) as $permission) {
            $this->_data[] = array(
                'name'        => $permission->name,
                'description' => $permission->description,
            );
        }
    }
}
