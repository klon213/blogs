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
        $data = $this->model->findAll('is_published = 0');
        return $this->sendResponse(200, $data);
    }
}