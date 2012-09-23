<?php

class SimpleRbacUserIdentity extends CUserIdentity
{
    const ERROR_USERNAME_INACTIVE = 201;

    private $_id;

    public function authenticate()
    {
        $userRecord = SRUser::getUser($this->username);
        if ($userRecord === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } else if (!$userRecord->correctPassword($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else if (!$userRecord->usernameActive()) {
            $this->errorCode = self::ERROR_USERNAME_INACTIVE;
        } else {
            $this->_id = intval($userRecord->id);
            $this->errorCode = self::ERROR_NONE;

            $userRecord->updateLastAccessed();
        }

        return $this->errorCode === self::ERROR_NONE;
    }

    public function getId()
    {
        return $this->_id;
    }
}
