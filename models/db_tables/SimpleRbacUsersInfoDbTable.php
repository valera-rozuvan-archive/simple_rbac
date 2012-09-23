<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Aug 25 21:07:41 EEST 2012
 * 
 * File:      SimpleRbacUsersInfoDbTable.php
 * Full path: protected/modules/simple_rbac/models/db_tables/SimpleRbacUsersInfoDbTable.php
 *
 * Description: This model will work with user's information records. It will provide methods for modifying the various
 * information fields of a user.
 */

class SimpleRbacUsersInfoDbTable extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /*
     * This function is necessary for when we want to get the table name without initializing the class. Why? Beucase
     * if the table does not exist in the DB, and we initialize the class, it will give you an error (the constructor
     * tries to read the table specified by tableName() method).
     */
    public static function tableName_s()
    {
        return '{{simple_rbac_users_info}}';
    }

    public function tableName()
    {
        return self::tableName_s();
    }

    public function primaryKey()
    {
        return 'user_id';
    }

    public function rules()
    {
        // The only field that is required is the `users_id`. It points to the user for whom the information is stored.
        // All of the other fields contain extra information, and can be freely added to and/or removed. That is why
        // no other rules are defined by this method.
        return array(
            array(
                'user_id',
                'numerical',
                'integerOnly' => true,
            ),
            array(
                'user_id',
                'required',
            ),
        );
    }

    public function relations()
    {
        return array(
            'user' => array(
                self::BELONGS_TO,
                'SimpleRbacUsersDbTable',
                'user_id',
            ),
        );
    }
}
