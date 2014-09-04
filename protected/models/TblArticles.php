<?php

/**
 * This is the model class for table "tbl_articles".
 *
 * The followings are the available columns in table 'tbl_articles':
 * @property integer $id
 * @property integer $user_id
 * @property string $pic
 * @property string $title
 * @property string $text
 * @property integer $is_published
 * @property string $pub_date
 *
 * The followings are the available model relations:
 * @property TblUsers $user
 */
class TblArticles extends CActiveRecord
{
	const IS_PUBLISHED = 1;
	public $pic;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_articles';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'required'),
			array('user_id, is_published', 'numerical', 'integerOnly'=>true),
			array('pic, title', 'length', 'max'=>255),
			array('text', 'length', 'max'=>2048),
			array('pub_date', 'safe'),
			array('pic', 'file', 'types'=>'jpg, jpeg, gif, png', 'safe'=>true, 'allowEmpty'=>true),
			array('pic', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, pic, title, text, is_published, pub_date', 'safe', 'on'=>'search'),
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
			'pic' => 'Pic',
			'title' => 'Title',
			'text' => 'Text',
			'is_published' => 'Is Published',
			'pub_date' => 'Pub Date',
		);
	}

	public function scopes()
	{
		return array(
			'published'=>array(
				'condition' => 'is_published=' . self::IS_PUBLISHED
			),

		);
	}

	public function beforeSave()
	{
		if ($this->isNewRecord){
			$this->user_id = Yii::app()->user->id;
			$pic = CUploadedFile::getInstanceByName('pic');
			if(isset($pic)){
				$picName = Yii::getPathOfAlias('webroot.images.articles') . DIRECTORY_SEPARATOR . $this->title . '.' . $pic->extensionName;
				$pic->saveAs($picName);
			}
		}
		return !$this->hasErrors();
	}

	public function beforeDelete()
	{
		if($this->user_id == Yii::app()->user->id)
		{
			return !$this->hasErrors();
		}
	}

	public function published($begin, $end)
	{
		$this->getDbCriteria()->mergeWith(array(
			'condition'=>"pub_date between :beginDate AND :endDate",
				'params'=> array(":beginDate"=>$begin, ":endDate"=>$end
				)
		));
		return $this;
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
		$criteria->compare('pic',$this->pic,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('is_published',$this->is_published);
		$criteria->compare('pub_date',$this->pub_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public function searchByDate($beginDate, $endDate)
	{
		$data = TblArticles::model()->findAll("(pub_date between :beginDate AND :endDate )",
												array('beginDate'=>$beginDate, 'endDate'=>$endDate));
		return $data;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TblArticles the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
