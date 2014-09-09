<?php
/**
 * Created by PhpStorm.
 * User: Александр
 * Date: 04.09.14
 * Time: 15:16
 */
class CommentsController extends ApiController
{
	/*
	 * @article_id
	 * @user_mail
	 * ||
	 * @token
	 *
	 */
	public function actionCommentArticle()
	{
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
	public function actionParentComment($id)
	{
		$comments = TblComments::model()->parentComment($id)->findAll();
		$this->sendResponse($comments);
	}

	public function actionChildComment($id)
	{
		$comments = TblComments::model()->childComments(2)->findAll();
		$this->sendResponse($comments);
	}

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
		if($article->user_id == Yii::app()->user->id){
			$article->delete();
			$this->sendResponse($article);
		}else{
			$this->sendResponse(403);
		}
	}

	public function actionSubscribeForComments()
	{
		$model = new TblComments();
		if($model->user_id == Yii::app()->user->id){
			$model->findByPk($_POST['id']);
			$model->notify_author = $model::SUBSCRIBED_FOR_COMMENTS;
			$model->save();
			$model->sendResponse($model);
		}else{
			$model->sendResponse(403);
		}
	}
}