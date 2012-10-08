<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 23:34:49 EEST 2012
 * 
 * File:      SimpleRbacAssignChildPermissionForm.php
 * Full path: protected/modules/simple_rbac/models/forms/SimpleRbacAssignChildPermissionForm.php
 *
 * Description: This model will provide a form for assigning a child role to a parent role.
 */

class SimpleRbacAssignChildPermissionForm extends CFormModel
{
    public $parentRole;
    public $childPermission;

    public function rules()
    {
        return array(
            array('parentRole', 'ValidatorParentRole',),
            array('childPermission', 'ValidatorChildPermission',),
        );
    }

    public function attributeLabels()
    {
        return array(
            'parentRole'       => 'Parent role',
            'childPermission'  => 'Child permission',
        );
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorParentRole($attribute, $params)
    {
        if ((!isset($this->parentRole)) || ($this->parentRole === ''))
            $this->addError($attribute, 'Parent role is not specified.');
        else if (!SRUser::isRole($this->parentRole))
            $this->addError($attribute, 'Parent role does not exist.');
    }

    /**
     * @param string $attribute the name of the attribute to be validated
     * @param array $params options specified in the validation rule
     */
    public function ValidatorChildPermission($attribute, $params)
    {
        if ((!isset($this->childPermission)) || ($this->childPermission === ''))
            $this->addError($attribute, 'Child permission is not specified.');
        else if (!SRUser::isPermission($this->childPermission))
            $this->addError($attribute, 'Child permission does not exist.');
        else if (SRUser::isChildOfRole($this->parentRole, $this->childPermission))
            $this->addError($attribute, 'This child permission is already assigned to the parent role.');
    }
}
