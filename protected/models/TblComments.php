<?php

/**
 * This is the model class for table "tbl_comments".
 *
 * The followings are the available columns in table 'tbl_comments':
 * @property integer $id
 * @property integer $user_id
 * @property integer $article_id
 * @property string $guestmail
 * @property string $text
 * @property string $comment_sdate
 * @property integer $notify_author
 * @property integer $parent_id
 */
class TblComments extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */

	const SUBSCRIBED_FOR_COMMENTS = 1;
	const UNSUBSCRIBED_FROM_COMMENTS = 0;

	public function tableName()
	{
		return 'tbl_comments';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('article_id', 'required'),
			//array('guestmail', 'required'),
			array('user_id, article_id, notify_author, parent_id', 'numerical', 'integerOnly'=>true),
			array('guestmail','length', 'max'=>255),
			array('guestmail', 'email'),
			array('text', 'length', 'max'=>2048),
			array('comment_sdate', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, article_id, guestmail, text, comment_sdate, notify_author, parent_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'user' => array(self::BELONGS_TO, 'TblUsers', 'user_id'),
			'article' => array(self::BELONGS_TO, 'TblArticles', 'article_id'),
			//'tcomment' => array(self::BELONGS_TO, 'TblComments', 'parent_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'article_id' => 'Article',
			'guestmail' => 'Guestmail',
			'text' => 'Text',
			'comment_sdate' => 'Comment Sdate',
			'notify_author' => 'Notify Author',
			'parent_id' => 'Parent',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('article_id',$this->article_id);
		$criteria->compare('guestmail',$this->guestmail,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('comment_sdate',$this->comment_sdate,true);
		$criteria->compare('notify_author',$this->notify_author);
		$criteria->compare('parent_id',$this->parent_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function scopes()
	{
		return array(
			'parentComment'=>array(),
			'childComments'=>array(),
		);
	}

	public function parentComment($id)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>"article_id=:article",
			'params'=> array(":article"=>$id
			)
		));
		return $this;
	}

	public function childComments($parent_id)
	{
		$this->getDbCriteria()->mergeWith(array(
				'condition'=>"id=:parent_id",
				'params'=>array(":parent_id"=>$parent_id)
			)
		);
	}

	public function beforeSave()
	{
		if(Yii::app()->user->id){
			$this->user_id = Yii::app()->user->id;
			return !$this->hasErrors();
		}else if ($this->guestmail) {
			return !$this->hasErrors();
		}
		else{
			return false;
		}
		/*if(Yii::app()->user->id){
			$this->user_id = Yii::app()->user->id;
			$this->guestmail = $this->user['email'];
			return !$this->hasErrors();
		}*/
	}
/*
	public function beforeValidation()
	{
		if(Yii::app()->user->id){
			$this->user_id = Yii::app()->user->id;
			$this->guestmail = 'val@email.com'; // '$this->user['email']';
			return false;
		}
	}*/

	public function beforeDelete()
	{
		if($this->user_id == Yii::app()->user->id)
		{
			return !$this->hasErrors();
		}
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TblComments the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
