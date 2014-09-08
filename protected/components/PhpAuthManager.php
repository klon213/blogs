<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 08.09.14
 * Time: 18:51
 */
class PhpAuthManager extends CPhpAuthManager{
	public function init(){		//hierarchy is stored in config/auth.php
		if($this->authFile===null){
			$this->authFile=Yii::getPathOfAlias('application.config.auth').'.php';
		}

		parent::init();

		if(!Yii::app()->user->isGuest){
			$this->assign(Yii::app()->user->role, Yii::app()->user->id);
		}
	}
}