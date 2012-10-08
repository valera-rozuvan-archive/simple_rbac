<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep  9 18:09:09 EEST 2012
 *
 * File:      SimpleRbacNewPermissionForm.php
 * Full path: protected/modules/simple_rbac/models/forms/SimpleRbacNewPermissionForm.php
 *
 * Description: This model will provide a form for creating a new permission.
 */

class SimpleRbacNewPermissionForm extends CFormModel
{
    public $permissionName;
    public $description;

    public function rules()
    {
        return array(
            array('permissionName', 'ValidatorPermissionName',),
            array('description', 'default',),
        );
    }

    public function attributeLabels()
    {
        return array(
            'permissionName' => 'Permission name',
            'description' => 'Description',
        );
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorPermissionName($attribute, $params)
    {
        if ((!isset($this->permissionName)) || ($this->permissionName === ''))
            $this->addError($attribute, 'Permission name can\'t be empty.');
        else if (preg_match('/[^\w]/', $this->permissionName))
            $this->addError($attribute, 'Permission name can contain only alphanumeric characters, and the "_" character.');
        else if (strlen($this->permissionName) > 20)
            $this->addError($attribute, 'Permission name can contain a maximum of 20 characters.');
        else if (SRUser::isPermission($this->permissionName))
            $this->addError($attribute, 'Permission with the name "'.$this->permissionName.'" already exists.');
        else if (SRUser::isRole($this->permissionName))
            $this->addError($attribute, 'Role with the name "'.$this->permissionName.'" already exists. Permission can\'t have the same name.');
    }
}
