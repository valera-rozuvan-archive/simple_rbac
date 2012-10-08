<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep  9 17:55:01 EEST 2012
 * 
 * File:      SimpleRbacNewRoleForm.php
 * Full path: protected/modules/simple_rbac/models/forms/SimpleRbacNewRoleForm.php
 *
 * Description: This model will provide a form for creating a new role.
 */

class SimpleRbacNewRoleForm extends CFormModel
{
    public $roleName;
    public $description = '';

    public function rules()
    {
        return array(
            array('roleName', 'ValidatorRoleName',),
            array('description', 'default',),
        );
    }

    public function attributeLabels()
    {
        return array(
            'roleName' => 'Role name',
            'description' => 'Description',
        );
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorRoleName($attribute, $params)
    {
        if ((!isset($this->roleName)) || ($this->roleName === ''))
            $this->addError($attribute, 'Role name can\'t be empty.');
        else if (preg_match('/[^\w]/', $this->roleName))
            $this->addError($attribute, 'Role name can contain only alphanumeric characters, and the "_" character.');
        else if (strlen($this->roleName) > 20)
            $this->addError($attribute, 'Role name can contain a maximum of 20 characters.');
        else if (SRUser::isRole($this->roleName))
            $this->addError($attribute, 'Role with the name "'.$this->roleName.'" already exists.');
        else if (SRUser::isPermission($this->roleName))
            $this->addError($attribute, 'Permission with the name "'.$this->roleName.'" already exists. Role can\'t have the same name.');
    }
}
