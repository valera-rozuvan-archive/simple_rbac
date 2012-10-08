<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 21:02:08 EEST 2012
 * 
 * File:      SimpleRbacChangePasswordForm.php
 * Full path: protected/modules/simple_rbac/models/forms/SimpleRbacChangePasswordForm.php
 *
 * Description: This model will provide a from for changing user's password.
 */

class SimpleRbacChangePasswordForm extends CFormModel
{
    public $username;
    public $newPassword1;
    public $newPassword2;

    public function rules()
    {
        return array(
            array('username', 'ValidatorUsername',),
            array('newPassword1', 'ValidatorNewPassword1',),
            array('newPassword2', 'ValidatorNewPassword2',),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username'     => 'Username',
            'newPassword1' => 'New password',
            'newPassword2' => 'Retype new password',
        );
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorUsername($attribute, $params)
    {
        if ((!isset($this->username)) || ($this->username === ''))
            $this->addError($attribute, 'Username is not specified.');
        else if (!SRUser::userExists($this->username))
            $this->addError($attribute, 'The specified username does not belong to a user.');
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorNewPassword1($attribute, $params)
    {
        if ((!isset($this->newPassword1)) || ($this->newPassword1 === ''))
            $this->addError($attribute, 'New password can\'t be blank.');
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorNewPassword2($attribute, $params)
    {
        if ((!isset($this->newPassword2)) || ($this->newPassword1 !== $this->newPassword2))
            $this->addError($attribute, 'The second time you typed the new password incorrectly.');
    }
}
