<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	private $_id;
	public function authenticate()
	{
		$record=TblUsers::model()->findByAttributes(array('login'=>$this->username));
		if($record===null)
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		//else if($record->pass!==crypt($this->password,$record->pass))
		else if($record->pass!==md5($this->password))
		{
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		}
        else if($record->is_verified==0)
        {
            $this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
        }
		else
		{
			$this->_id=$record->id;
			$this->setState('title', $record->login);
			$this->errorCode=self::ERROR_NONE;
		}
		return !$this->errorCode;
	}
	public function apiAuth()
	{
		$record=TblUsers::model()->findByAttributes(array('token_api'=>$this->password));
		if($record===null)
        {
			$this->errorCode=self::ERROR_USERNAME_INVALID;
        }
		//else if($record->pass!==crypt($this->password,$record->pass))
		else if($record->token_api!==$this->password)
		{
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		}
		else if($record->is_verified==0)
		{
			$this->errorCode=self::ERROR_UNKNOWN_IDENTITY;
		}
		else
		{
			$this->_id=$record->id;
			$this->setState('title', $record->login);
			$this->errorCode=self::ERROR_NONE;
		}
		return !$this->errorCode;
	}
		public function getId()
		{
			return $this->_id;
		}
}

/*$users=array(
	// username => password
	'demo'=>'demo',
	'admin'=>'admin',
);
if(!isset($users[$this->username]))
	$this->errorCode=self::ERROR_USERNAME_INVALID;
elseif($users[$this->username]!==$this->password)
	$this->errorCode=self::ERROR_PASSWORD_INVALID;
else
	$this->errorCode=self::ERROR_NONE;
return !$this->errorCode;*/