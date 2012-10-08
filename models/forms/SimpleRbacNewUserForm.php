<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sat Sep  8 19:56:56 EEST 2012
 * 
 * File:      SimpleRbacNewUserForm.php
 * Full path: protected/modules/simple_rbac/models/forms/SimpleRbacNewUserForm.php
 *
 * Description: This model will provide a form for creating a new user.
 */

class SimpleRbacNewUserForm extends CFormModel
{
    public $username;
    public $password;

    public function rules()
    {
        return array(
            array('username', 'ValidatorUsername',),
            array('password', 'ValidatorPassword',),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username' => 'Username',
            'password' => 'Password',
        );
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorUsername($attribute, $params)
    {
        if ((!isset($this->username)) || ($this->username === ''))
            $this->addError($attribute, 'Username can\'t be empty.');
        else if (preg_match('/[^\w]/', $this->username))
            $this->addError($attribute, 'Username can contain only alphanumeric characters, and the "_" character.');
        else if (strlen($this->username) > 16)
            $this->addError($attribute, 'Username can contain a maximum of 16 characters.');
        else if (SRUser::userExists($this->username))
            $this->addError($attribute, 'The user with username "'.$this->username.'" already exists.');
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorPassword($attribute, $params)
    {
        if ((!isset($this->password)) || ($this->password === ''))
            $this->addError($attribute, 'Password can\'t be empty');
        else if (preg_match('/[\s]/', $this->password))
            $this->addError($attribute, 'Password can\'t contain whitespace characters (space, tab, or newline).');
    }
}
