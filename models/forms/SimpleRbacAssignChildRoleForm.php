<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 22:48:10 EEST 2012
 * 
 * File:      SimpleRbacAssignChildRoleForm.php
 * Full path: protected/modules/simple_rbac/models/forms/SimpleRbacAssignChildRoleForm.php
 *
 * Description: This model will provide a form for assigning a child role to a parent role.
 */

class SimpleRbacAssignChildRoleForm extends CFormModel
{
    public $parentRole;
    public $childRole;

    public function rules()
    {
        return array(
            array('parentRole', 'ValidatorParentRole',),
            array('childRole', 'ValidatorChildRole',),
        );
    }

    public function attributeLabels()
    {
        return array(
            'parentRole' => 'Parent role',
            'childRole'  => 'Child role',
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
    public function ValidatorChildRole($attribute, $params)
    {
        if ((!isset($this->childRole)) || ($this->childRole === ''))
            $this->addError($attribute, 'Child role is not specified.');
        else if ($this->parentRole === $this->childRole)
            $this->addError($attribute, 'Parent role can\'t be a child of itself.');
        else if (!SRUser::isRole($this->childRole))
            $this->addError($attribute, 'Child role does not exist.');
        else if (SRUser::isChildOfRole($this->parentRole, $this->childRole))
            $this->addError($attribute, 'This child role is already assigned to the parent role.');
        else if (SRUser::isChildOfRole($this->childRole, $this->parentRole))
            $this->addError($attribute, 'This parent role is already assigned to the child role.');
        else if (SRUser::isDefaultRole($this->childRole))
            $this->addError($attribute, 'Assigning default roles is not allowed.');
    }
}
