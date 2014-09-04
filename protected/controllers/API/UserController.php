<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 02.09.14
 * Time: 15:28
 */

class UserController extends ApiController
{
    private  $model;


    public function actionSignUp()
    {
        $model = new TblUsers();
        $model->setAttributes($_POST);
        $model->save();
        $this->sendResponse($model);
    }

	public function actionGetValidationMail($validationKey)
	{
		UserValidate::validateUser($validationKey);
	}

	public function actionGetToken()
	{
		if(HUsers::isActiveUser($_POST['login'])){
			$model = new TblUsers();
			$identity=new UserIdentity($_POST['login'], $_POST['pass']);
			if($identity->authenticate()){
				$token = DCrypt::encrypt($_POST['login'] . time(), Yii::app()->params['key']);
				$this->storeToken($_POST['login'], $token);
				$this->sendResponse($token);
				//$model->
			}else
				$identity->errorMessage;
			//Yii::app()->user->login($identity);
// Выходим
			//Yii::app()->user->logout();
		}
	}

	public function actionAuthByToken()
	{
		if($this->verifyToken($_POST['token'])){
			$user = TblUsers::model()->find("token_api='" . $_POST['token'] . "'");
			$identity=new UserIdentity($user['login'], $_POST['token']);
			if($identity->apiAuth()){
				Yii::app()->user->login($identity);
			  echo 'verified';
			}
		}else{
			echo 'verification failure';
		}
	}

	private function storeToken($login, $token)
	{
		$user = TblUsers::model()->find("login='" . $login . "'");
		$user->token_api = $token;
		$user->update();
	}

	private function verifyToken($token)
	{
		$user = TblUsers::model()->find("token_api='" . $token . "'");
		if($user['id']){
			return true;
		}else
			return false;
	}
}
