<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 02.09.14
 * Time: 18:13
 */
class ArticlesController extends ApiController
{
    private $model;

    public function __construct()
    {
        $this->model = new TblArticles();
    }
    public function actionListArticles()
    {
       // $dateBegin = $_POST['date_begin'];
        $dateEnd = $_POST['date_end'];
       // echo $dateBegin;
        $data = $this->model->findAll('is_published = 1');
       // echo print_r ($data);
        return $this->sendResponse($dateEnd);
    }
}