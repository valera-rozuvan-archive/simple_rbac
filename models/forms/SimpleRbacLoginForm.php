<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class SimpleRbacLoginForm extends CFormModel
{
    public $username;
    public $password;
    public $rememberMe;

    private $_identity;

    /**
     * Declares the validation rules.
     * The rules state that username and password are required,
     * and password needs to be authenticated.
     */
    public function rules()
    {
        return array(
            // username and password are required
            array('username, password', 'required'),
            // rememberMe needs to be a boolean
            array('rememberMe', 'boolean'),
            // password needs to be authenticated
            array('password', 'authenticate'),
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'username'   => Yii::t('login', 'login-form-username'),
            'password'   => Yii::t('login', 'login-form-password'),
            'rememberMe' => Yii::t('login', 'login-form-remember-me'),
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_identity = new SimpleRbacUserIdentity($this->username, $this->password);
            if (!$this->_identity->authenticate()) {
                switch ($this->_identity->errorCode) {
                    case SimpleRbacUserIdentity::ERROR_USERNAME_INVALID:
                        $this->addError('username', Yii::t('login', 'login-form-incorrect-username'));
                        break;
                    case SimpleRbacUserIdentity::ERROR_PASSWORD_INVALID:
                        $this->addError('password', Yii::t('login', 'login-form-incorrect-password'));
                        break;
                    case SimpleRbacUserIdentity::ERROR_USERNAME_INACTIVE:
                        $this->addError('username', Yii::t('login', 'login-form-inactive-username'));
                        break;
                    default:
                        $this->addError('username', Yii::t('login', 'login-form-general-error'));
                        $this->addError('password', Yii::t('login', 'Something went wrong!'));
                        break;
                }
            }
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new SimpleRbacUserIdentity($this->username, $this->password);
            $this->_identity->authenticate();
        }

        if ($this->_identity->errorCode !== SimpleRbacUserIdentity::ERROR_NONE)
            return false;

        $duration = $this->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
        Yii::app()->user->login($this->_identity, $duration);

        return true;
    }
}
