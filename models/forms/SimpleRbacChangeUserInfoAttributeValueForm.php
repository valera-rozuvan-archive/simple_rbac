<?php
/*
 * Author    Valera Rozuvan
 * Created:  Sun Sep 23 19:59:05 EEST 2012
 * 
 * File:      SimpleRbacChangeUserInfoAttributeValueForm.php
 * Full path: protected/modules/simple_rbac/models/forms/SimpleRbacChangeUserInfoAttributeValueForm.php
 *
 * Description: This model will provide a form for changing the value of an attribute for userInfo of a user.
 */

class SimpleRbacChangeUserInfoAttributeValueForm extends CFormModel
{
    public $username;
    public $attribute;
    public $value;

    public function rules()
    {
        return array(
            array('username', 'ValidatorUsername',),
            array('attribute', 'ValidatorAttribute',),
            array('value', 'default',),
        );
    }

    public function attributeLabels()
    {
        return array(
            'username'  => 'Username',
            'attribute' => 'Attribute',
            'value'     => 'Value',
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
    public function ValidatorAttribute($attribute, $params)
    {
        if ((!isset($this->attribute)) || ($this->attribute === ''))
            $this->addError($attribute, 'Attribute can\'t be empty.');
        else if (SRUser::isSpecialUserInfoAttribute($this->attribute))
            $this->addError($attribute, 'You can\'t change "user_id" attribute.');
        else if (!SRUser::userInfoAttributeExists($this->attribute))
            $this->addError($attribute, 'Attribute does not exist in the DB table.');
    }
}
