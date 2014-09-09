<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 02.09.14
 * Time: 18:13
 */
class ArticlesController extends ApiController
{
/*
	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	public function accessRules()
	{
		return array(

			array('deny', // deny all other actions
				'actions'=>array('index'),
				'roles' => array('*'),
			),
		);
	}
*/
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

	public function actionGetAll()
	{
		$data = TblArticles::model()->findAll();
		return $this->sendResponse($data);
	}

	public function actionGetUnpublished()
	{
		$data = TblArticles::model()->findAll('is_published=' . TblArticles::IS_NOT_PUBLISHED);
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
		if(Yii::app()->user->checkAccess('user')){
			$article = TblArticles::model()->findByPk($_POST['id']);
			$article->setAttributes($_POST);
			$article->save();
			$this->sendResponse($article);
		}else
			$this->sendResponse(401);

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