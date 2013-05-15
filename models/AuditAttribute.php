<?php
/**
 * AuditAttribute class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-audit.models
 */

/**
 * This is the model class for table "audit_attribute".
 *
 * The followings are the available columns in table 'audit_attribute':
 * @property string $id
 * @property string $changer
 * @property string $changerId
 * @property string $model
 * @property string $modelId
 * @property string $attribute
 * @property string $oldValue
 * @property string $newValue
 * @property string $created
 */
class AuditAttribute extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return AuditAttribute the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return 'audit_attribute';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		return array(
			array('changer, changerId, model, modelId, attribute, oldValue, newValue', 'required'),
			array('changer, model, attribute', 'length', 'max' => 255),
			array('changerId, modelId', 'length', 'max' => 10),
			// The following rule is used by search().
			array('id, changer, changerId, model, modelId, attribute, oldValue, newValue, created', 'safe', 'on' => 'search'),
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
			'changer' => 'Changer',
			'changerId' => 'Changer ID',
			'model' => 'Model',
			'modelId' => 'Model ID',
			'attribute' => 'Attribute',
			'oldValue' => 'Old Value',
			'newValue' => 'New Value',
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
		$criteria->compare('changer', $this->changer, true);
		$criteria->compare('changerId', $this->changerId);
		$criteria->compare('model', $this->model, true);
		$criteria->compare('modelId', $this->modelId);
		$criteria->compare('attribute', $this->attribute, true);
		$criteria->compare('oldValue', $this->oldValue, true);
		$criteria->compare('newValue', $this->newValue, true);
		$criteria->compare('created', $this->created, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}