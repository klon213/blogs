<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 04.09.14
 * Time: 10:57
 */
class HUsers
{
	public static  function isActiveUser($login)
	{
		$model = new TblUsers();
		$data = $model->find("login='" . $login . "'");
		if($data['is_verified']==1){
			return true;
		}else{
			return false;
		}
	}
}