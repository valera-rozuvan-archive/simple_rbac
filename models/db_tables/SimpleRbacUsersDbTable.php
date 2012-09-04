<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Aug 25 20:26:35 EEST 2012
 * 
 * File:      SimpleRbacUsersDbTable.php
 * Full path: protected/modules/simple_rbac/models/db_tables/SimpleRbacUsersDbTable.php
 *
 * Description: This model will work with user records. It will provide methods for creating, editing, and deleting
 * a user.
 */

class SimpleRbacUsersDbTable extends CActiveRecord
{

/*
$hashed_password = crypt('mypassword'); // let the salt be automatically generated

// You should pass the entire results of crypt() as the salt for comparing a
// password, to avoid problems when different hashing algorithms are used. (As
// it says above, standard DES-based password hashing uses a 2-character salt,
// but MD5-based hashing uses 12.)

if (crypt($user_input, $hashed_password) == $hashed_password) {
    echo "Password verified!";
}
*/

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{simple_rbac_users}}';
    }

    public function primaryKey()
    {
        return 'id';
    }

    public function rules()
    {
        return array(
            array(
                'id, status',
                'numerical',
                'integerOnly' => true,
            ),
            array(
                'username, password',
                'required',
            ),
            array(
                'username',
                'length',
                'min' => 1,
                'max' => 16,
            ),
            array(
                'password',
                'length',
                'min' => 1,
                'max' => 128,
            ),
            array(
                'id, username, status, last_access, registered',
                'safe',
                'on' => 'search',
            ),
            array(
                'last_access, registered',
                'date',
                'format' => 'yyyy-MM-dd HH:mm:ss',
            ),
        );
    }

    public function relations()
    {
        return array(
            'userInfo' => array(
                self::HAS_ONE,
                'SimpleRbacUsersInfoDbTable',
                'user_id',
            ),
        );
    }

    public function correctPassword($password)
    {
        if (crypt($password, $this->password) !== $this->password)
            return false;

        return true;
    }

    public static function hashPassword($password)
    {
        return crypt($password);
    }

    public function updateLastAccessed()
    {
        $this->last_access = date('Y-m-d H:i:s');
        $this->save();
    }

    public function usernameActive()
    {
        if (intval($this->status) !== 1)
            return false;

        return true;
    }
}
