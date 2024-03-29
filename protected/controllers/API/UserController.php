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

	/*
	 * @name
	 * @login
	 * @avatar (file)
	 * @pass
	 * @email
	 */

    public function actionSignUp()
    {
        $model = new TblUsers();
        $model->setAttributes($_POST);
        if($model->save()){
			$this->sendResponse(200, 'success');
		}else{
			$this->sendResponse($model);
		}
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
			/*	if(Yii::app()->user->checkAccess('administrator')){
					echo "hello, I'm administrator";
				}*/
				//$this->sendResponse($token);
				//$model->
			}else
				$identity->errorMessage;


			//Yii::app()->user->login($identity);
// Выходим
			//Yii::app()->user->logout();
		}

	}

	private function storeToken($login, $token)
	{
		$user = TblUsers::model()->find("login='" . $login . "'");
		$user->token_api = $token;
		$user->update();
	}
}
