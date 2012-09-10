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

    public function __construct(array $config = array())
    {
        $this->populateData();

        $_config = array();

        $_config['itemCount'] = count($this->_data);
        $_config['pageVar'] = 'SimpleRbacPermissionsDataP_page';
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

    public function populateData()
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
