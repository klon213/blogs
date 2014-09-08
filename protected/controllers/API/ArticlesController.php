<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 02.09.14
 * Time: 18:13
 */
class ArticlesController extends ApiController
{

	public function accessRules()
	{
		return array(
			array(
				'deny',
				'actions' => array(
				'index',
				),
				'roles' => array(
					TblUsers::ROLE_ADMIN, // 'user'
				),
			),
			array(
				'allow',
				'actions' => array(
					'view',
				),
				'roles' => array(
					User::ROLE_GUEST,
				),
			),
		);
	}

	public function actionIndex()
    {
		if (isset($_POST['begin_date']) && isset($_POST['end_date'])){
			$data = TblArticles::model()->published($_POST['begin_date'], $_POST['end_date'])->findAll();
		}else{
			$data = TblArticles::model()->findAll('is_published=' . TblArticles::IS_PUBLISHED);
		}
        return $this->sendResponse($data);

    }

	public function actionView($id)
	{
		$data = TblArticles::model()->findByPk($id);
		return $this->sendResponse($data);
	}

	/*
		 * token : string
		 * title : string
		 * text : string
		 * pic : image
		 * */
	public function actionCreate()
	{
		$model = new TblArticles();
		$model->setAttributes($_POST);
		$model->save();
		$this->sendResponse($model);
	}
	/*
	 * id : int
	 * token : string
	 * title : string
	 * text : string
	 * pic : image
	 * */
	public function actionEdit()
	{
		$article = TblArticles::model()->findByPk($_POST['id']);
		$article->setAttributes($_POST);
		$article->save();
		$this->sendResponse($article);
	}

		/*
	 	* id : int
	 	* token : string
		 */
	public function actionDelete()
	{
		$article = TblArticles::model()->findByPk($_POST['id']);
		$article->delete();
		$this->sendResponse($article);
	}
}