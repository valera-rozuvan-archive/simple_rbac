<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep 15 19:04:54 EEST 2012
 * 
 * File:      SimpleRbacUserInfoDataP.php
 * Full path: protected/modules/simple_rbac/models/data_providers/SimpleRbacUserInfoDataP.php
 *
 * Description: Will provide the related user information in two columns to be used for grid display. The first column
 * will contain attribute names, the second column will contain the values stored for each attribute.
 */

class SimpleRbacUserInfoDataP extends CDataProvider
{
    private $_data;
    private $_data_keys;

    public function __construct($username, array $config = array())
    {
        $this->populateData($username);

        $_config = array();

        $_config['itemCount'] = count($this->_data);
        $_config['pageVar'] = 'SimpleRbacUserInfoDataP_page';
        if (isset($config['pagination']['pageSize']))
            $_config['pageSize'] = $config['pagination']['pageSize'];

        $this->setPagination($_config);

        $this->_data_keys = array(
            'attribute', 'value',
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

        $attributes = array_keys(SimpleRbacUsersInfoDbTable::model()->getAttributes());

        foreach ($attributes as $attribute) {
            $this->_data[] = array(
                'attribute' => $attribute,
                'value'     => $user->userInfo->{$attribute},
            );
        }
    }
}