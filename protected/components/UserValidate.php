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
		$body =Yii::app()->controller->createAbsoluteUrl('/api/user/getValidationMail'/*, array('validationKey' => urldecode($key))*/) . '?validationKey=' . urlencode($key);  //issue here
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
		$dcrptEmail = $crpt['login'];
		$data = $model->find("login='".$dcrptEmail."'");
		$data->is_verified = 1;
        $data->save();
	}

    public function AuthByToken()
    {
        $headers = apache_request_headers();
        if($this->verifyToken($headers['Authorization'])){
          //  $user = TblUsers::model()->find("token_api='" . $headers['Authorization'] . "'");
            $user = TblUsers::model()->find("token_api=:token", array(':token'=>$headers['Authorization']));
            $identity=new UserIdentity($user['login'], $headers['Authorization']);
            if($identity->apiAuth()){
               return Yii::app()->user->login($identity);
           //     echo 'verified';
            }
        }else{
            return false;
        }
    }

    private function verifyToken($token)
    {
        $user = TblUsers::model()->find("token_api=:token", array(':token'=>$token));
        if($user['id']){
            return true;
        }else
            return false;
    }
}

