<?php

/**
 * This is the model class for table "subscriptions".
 *
 * The followings are the available columns in table 'subscriptions':
 * @property integer        $modelId
 * @property integer        $modelName
 * @property integer        $uId
 * @property integer        $ctime
 */
class Subscription extends EActiveRecord {

	public $cacheTime = 3600;

	/**
	 * Returns the static model of the specified AR class.
	 *
	 * @param string $className active record class name.
	 *
	 * @return Subscription the static model class
	 */
	public static function model ( $className = __CLASS__ ) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName () {
		return 'subscriptions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules () {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return CMap::mergeArray(parent::rules(),
			array(
			     array(
				     'modelId, modelName',
				     'required'
			     ),
			     array(
				     'modelId',
				     'exists',
				     'className' => $this->modelName
			     )
			));
	}

	public function behaviors () {
		return CMap::mergeArray(parent::behaviors(),
			array());
	}

	public function relations () {
		return CMap::mergeArray(parent::relations(),
			array(
			     'user' => array(
				     'User',
				     self::BELONGS_TO,
				     'uId'
			     )
			));
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels () {
		return array(
			'ctime'     => Yii::t('subscriptionsModule.common', 'Добавлено'),
			'modelId'   => Yii::t('subscriptionsModule.common', 'Model Id'),
			'modelName' => Yii::t('subscriptionsModule.common', 'Название'),
			'uId'       => Yii::t('subscriptionsModule.common', 'User id'),
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search () {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria = new CDbCriteria;

		$criteria->compare('modelId', $this->modelId);
		$criteria->compare('ctime', $this->ctime);
		$criteria->compare('modelName', $this->modelName);
		$criteria->compare('uId', $this->uId);

		return new CActiveDataProvider($this, array(
		                                           'criteria' => $criteria,
                'sort' => [
                    'defaultOrder' => 'ctime DESC'
                ],
                'pagination' => [
                    'pageVar' => 'page'
                ]
		                                      ));
	}


	protected function beforeValidate () {
		if ( parent::beforeValidate() ) {
			$validator = CValidator::createValidator('unique',
				$this,
				'modelId',
				array(
				     'criteria' => array(
					     'condition' => 'modelName = :modelName AND uId = :uId',
					     'params'    => array(
						     ':modelName' => $this->modelName,
						     ':uId'       => Yii::app()->getUser()->getId(),
					     ),
				     ),
				     'message'  => Yii::t('subscriptionsModule.common', 'Такая подписка уже существует.')
				));
			$this->getValidatorList()->insertAt(0, $validator);

			return true;
		}
	}

	protected function beforeSave () {
		if ( parent::beforeSave() ) {

			if ( $this->getIsNewRecord() ) {
				$this->ctime = time();
				$this->uId = Yii::app()->getUser()->getId();
			}

			return true;
		}
	}

	public function primaryKey () {
		return array(
			'modelId',
			'modelName',
			'uId'
		);
	}

    public function getSubscriptionModelInstance () {
        /**
         * TODO: Костыль
         */
        if ( $this->modelName == 'modules_torrents_models_TorrentGroup_comments' ) {
            return new \modules\torrents\models\TorrentGroup;
        }
        else {
            $modelName = EActiveRecord::classNameToNamespace($this->modelName);
            return new $modelName;
        }
    }

    /**
     * @param string $modelName
     * @param integer $primaryKey
     * @return bool|CActiveRecord
     */
    public static function check ( $modelName, $primaryKey ) {
		if ( Yii::app()->getUser()->getIsGuest() ) {
			return false;
		}
		return self::model()->findByPk(array(
		                                    'modelId'   => $primaryKey,
		                                    'modelName' => $modelName,
		                                    'uId'       => Yii::app()->getUser()->getId()
		                               ));
	}
}