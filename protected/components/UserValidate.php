<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 03.09.14
 * Time: 18:27
 */
class UserValidate
{
	public static function sendValidationMail($login, $email)
	{
		//die(DCrypt::encrypt($email, Yii::app()->params['key']));
		$key= DCrypt::encrypt(array('login'=>$login,), Yii::app()->params['key']);
		//DBug::stop(DCrypt::decrypt($key,Yii::app()->params['key']), $key, urlencode($key), urldecode(urlencode($key)));
		//die($key);
		//echo $key;
		$body =Yii::app()->controller->createAbsoluteUrl('/user/getValidationMail', array('validationKey' => urldecode($key)));
		//$body = "http://blogs.org/index.php/api/user/getValidationMail/validationKey/" . DCrypt::encrypt($email, Yii::app()->params['key']);
		Yii::import('ext.yii-mail.YiiMailMessage');
		$message = new YiiMailMessage;
		$message->setBody($body);
		$message->subject = 'Validate your account';
		$message->addTo($email);
		$message->from = 'noreply@blogs.org';
		Yii::app()->mail->send($message);

		//echo DCrypt::encrypt('sent', Yii::app()->params['key']);
		//echo DCrypt::decrypt('uit6nnthHWsK8KaKt0qw/w==', Yii::app()->params['key']);
	}
	public static function validateUser($validationKey)
	{
		$model = new TblUsers();
		Yii::app()->params['key'];
		$crpt = DCrypt::decrypt(urldecode($validationKey), Yii::app()->params['key']);

		//DBug::stop($crpt);
		$dcrptEmail = $crpt['login'];
		$data = $model->find("login='".$dcrptEmail."'");
		//DBug::stop($data);
		echo print_r($data);
	}
}