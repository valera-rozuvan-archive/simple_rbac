<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 22:07:13 EEST 2012
 * 
 * File:      SimpleRbacChildRolesDataP.php
 * Full path: protected/modules/simple_rbac/models/data_providers/SimpleRbacChildRolesDataP.php
 *
 * Description: Will provide child roles of a parent role, and their information. Will be used for grid display.
 */

class SimpleRbacChildRolesDataP extends CDataProvider
{

    private $_data;
    private $_data_keys;

    public function __construct($roleName, array $config = array())
    {
        $this->populateData($roleName);

        $_config = array();

        $_config['itemCount'] = count($this->_data);
        $_config['pageVar'] = 'SimpleRbacRolesDataP_page';
        if (isset($config['pagination']['pageSize']))
            $_config['pageSize'] = $config['pagination']['pageSize'];

        $this->setPagination($_config);

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
        $start = $this->pagination->pageSize * $this->pagination->currentPage;
        $end = $start + $this->pagination->pageSize;

        $dataOnPage = array_slice($this->_data, $start, $end);

        return $dataOnPage;
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

    public function populateData($roleName)
    {
        $auth = Yii::app()->authManager;

        $this->_data = array();

        $childRoles = SRUser::getChildRoles($roleName);

        foreach ($childRoles as $childRole) {
            $authItem = $auth->getAuthItem($childRole);

            $this->_data[] = array(
                'name'        => $childRole,
                'description' => $authItem->description,
            );
        }

    }
}
