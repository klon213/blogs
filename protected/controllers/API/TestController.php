<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 02.09.14
 * Time: 16:47
 */

class TestController extends CController
{
    public function actionTest()
    {
        $myCurl = curl_init();
        curl_setopt_array($myCurl, array(
            CURLOPT_URL => 'http://blogs.org/index.php/API/User/SignUp/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array('name'=>'testname', 'login'=>'testlogin'))
        ));
        $response = curl_exec($myCurl);
        curl_close($myCurl);

        echo "Response: ".$response;
    }
}