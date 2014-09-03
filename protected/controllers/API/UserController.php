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

/*test*/
	/*
    public function actionSendMail()
    {
		UserValidate::sendValidationMail('bead@ukr.net');
    }*/

	public function actionGetValidationMail($validationKey)
	{
		UserValidate::validateUser($validationKey);
	}
}
