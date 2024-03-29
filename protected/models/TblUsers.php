<?php

/**
 * This is the model class for table "tbl_users".
 *
 * The followings are the available columns in table 'tbl_users':
 * @property integer $id
 * @property string $name
 * @property string $login
 * @property string $avatar
 * @property string $pass
 * @property string $email
 * @property integer $is_verified
 *
 * The followings are the available model relations:
 * @property TblArticles[] $tblArticles
 */
class TblUsers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	const ROLE_USER = 'user';
	const ROLE_GUEST = 'guest';
	const ROLE_ADMIN = 'administrator';

    public $pic;
    public $repeatPass;

	public function tableName()
	{
		return 'tbl_users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, login, email, pass', 'required'),
			array('is_verified', 'numerical', 'integerOnly'=>true),
			array('name, login, avatar, pass, email', 'length', 'max'=>255),
            array('login, email', 'unique'),
            array('pic', 'file', 'types'=>'jpg, jpeg, gif, png', 'safe'=>true, 'allowEmpty'=>true),
            array('pic', 'safe'),
           // array('pass', 'compare', 'compareAttribute'=>'repeatPass'),
           // array('repeatPass'=>'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, login, avatar, pass, email, is_verified', 'safe', 'on'=>'search'),
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
			'tblArticles' => array(self::HAS_MANY, 'TblArticles', 'user_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'login' => 'Login',
			'avatar' => 'Avatar',
			'pass' => 'Pass',
			'email' => 'Email',
			'is_verified' => 'Is Verified',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('login',$this->login,true);
		$criteria->compare('avatar',$this->avatar,true);
		$criteria->compare('pass',$this->pass,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('is_verified',$this->is_verified);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function beforeSave()
    {
        if ($this->isNewRecord){
            //DBug::stop($_POST);
            $this->pass=md5($this->pass);
            $this->is_verified = 0;
            $this->token_api = sha1(time());
            $pic = CUploadedFile::getInstanceByName('pic');
            if(isset($pic)){
                $picName = Yii::getPathOfAlias('webroot.images') . DIRECTORY_SEPARATOR . $this->login . '.' . $pic->extensionName;
                $pic->saveAs($picName);
            }
			UserValidate::sendValidationMail($this->login, $this->email);
        }
        return !$this->hasErrors();
    }

    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TblUsers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
