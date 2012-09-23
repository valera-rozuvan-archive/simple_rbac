<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 16:41:07 EEST 2012
 * 
 * File:      SimpleRbacAssignRoleToUserForm.php
 * Full path: protected/modules/simple_rbac/models/forms/SimpleRbacAssignRoleToUserForm.php
 *
 * Description: This model will provide a form for assigning a role to a user.
 */

class SimpleRbacAssignRoleToUserForm extends CFormModel
{
    public $username;
    public $role;

    public function rules()
    {
        return array(
            array('username', 'ValidatorUsername',),
            array('role', 'ValidatorRole',),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username' => 'Username',
            'role'     => 'Role name',
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
        else {
            $user = SRUser::getUser($this->username);
            if ($user === null)
                $this->addError($attribute, 'The specified username does not belong to a user.');
        }
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorRole($attribute, $params)
    {
        if ($this->role === '')
            $this->addError($attribute, 'Role name can\'t be empty.');
        else if (in_array($this->role, array('guest', 'authenticated',)))
            $this->addError($attribute, 'You can\'t assign a default role.');
        else if (!in_array($this->role, array_keys(Yii::app()->authManager->roles)))
            $this->addError($attribute, 'Role with the name "'.$this->role.'" does not exists.');
        else {
            $user = SRUser::getUser($this->username);
            if (Yii::app()->authManager->isAssigned($this->role, $user->id))
                $this->addError($attribute, 'Role with the name "'.$this->role.'" is already assigned to the username "'.$this->username.'".');
        }
    }
}
