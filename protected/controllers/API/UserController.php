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

    public function __construct()
    {
        $this->model = new TblUsers();
    }

    public function actionSignUp()
    {
        $data = 'Account for ' . $_POST['name'] . ' created successfully.';
        $this->model->name = $_POST['name'];
        $this->model->login = $_POST['login'];
        $this->model->pass = $_POST['pass'];
        $this->model->email = $_POST['email'];
        if($this->model->save()){
            return $this->sendResponse(200, $data);
        }else{
            return $this->sendResponse(400);
        }

    }

}