<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 08.09.14
 * Time: 18:42
 */
class WebUser extends CWebUser {
	private $_model = null;

	function getRole() {
		if($user = $this->getModel()){
			return $user->role;
		}
	}

	private function getModel(){
		if (!$this->isGuest && $this->_model === null){
			$this->_model = TblUsers::model()->findByPk($this->id, array('select' => 'role'));
		}
		return $this->_model;
	}
}