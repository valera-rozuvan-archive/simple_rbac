<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 15:28:42 EEST 2012
 *
 * File:      SimpleRbacUserRolesDataP.php
 * Full path: protected/modules/simple_rbac/models/data_providers/SimpleRbacUserRolesDataP.php
 *
 * Description: Will provide roles that are directly assigned to a user. Will be used for grid display.
 */

class SimpleRbacUserRolesDataP extends CDataProvider
{
    private $_data;
    private $_data_keys;

    public function __construct($username, array $config = array())
    {
        $this->populateData($username);

        $_config = array();

        $_config['itemCount'] = count($this->_data);
        $_config['pageVar'] = 'SimpleRbacUserRolesDataP_page';
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

    public function populateData($username)
    {
        $this->_data = array();

        $user = SRUser::getUser($username);
        if ($user === null)
            return;

        $auth = Yii::app()->authManager;
        $userRoles = array_keys($auth->getAuthAssignments($user->id));

        foreach ($userRoles as $userRole) {
            $this->_data[] = array(
                'name' => $auth->roles[$userRole]->name,
                'description' => $auth->roles[$userRole]->description,
            );
        }
    }
}
