<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 04.09.14
 * Time: 15:16
 */
class CommentsController extends ApiController
{
	public function actionCommentArticle()
	{
	/*
	 * @article_id
	 * @user_mail
	 * ||
	 * @token
	 *
	 */
		if(!isset($_POST['parent_id']))
		{
			$model = new TblComments();
			$model->setAttributes($_POST);
			$model->save();
		}
			$this->sendResponse($model);
	}
	/*
 * @comment_id
 * @user_mail
	 * ||
	 @token
 */
	public function actionComment()
	{
		if(!isset($_POST['article_id']))
		{
			$model = new TblComments();
			$model->setAttributes($_POST);
			$model->save();
		}
		$this->sendResponse($model);
	}
		/*
		 * $id
		 */

	public function actionDelete()
	{
		$article = TblComments::model()->findByPk($_POST['id']);
		$article->delete();
		$this->sendResponse($article);
	}

}