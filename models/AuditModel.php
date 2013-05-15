<?php
/**
 * AuditModel class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-audit.models
 */

/**
 * This is the model class for table "audit_model".
 *
 * The followings are the available columns in table 'audit_model':
 * @property string $id
 * @property string $action
 * @property string $changer
 * @property string $changerId
 * @property string $model
 * @property string $modelId
 * @property string $created
 */
class AuditModel extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AuditModel the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'audit_model';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('action, changer, changerId, model, modelId, created', 'required'),
			array('action, changer, model', 'length', 'max' => 255),
			array('changerId, modelId', 'length', 'max' => 10),
			// The following rule is used by search().
			array('id, action, changer, changerId, model, modelId, created', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations() {
		return array();
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array(
			'id' => 'ID',
			'action' => 'Action',
			'changer' => 'Changer',
			'changerId' => 'Changer',
			'model' => 'Model',
			'modelId' => 'Model',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('action', $this->action, true);
		$criteria->compare('changer', $this->changer, true);
		$criteria->compare('changerId', $this->changerId);
		$criteria->compare('model', $this->model, true);
		$criteria->compare('modelId', $this->modelId);
		$criteria->compare('created', $this->created, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}