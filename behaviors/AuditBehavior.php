<?php
/**
 * AuditBehavior class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-audit.behaviors
 */

Yii::import('vendor.nordsoftware.yii-audit.models.*');

/**
 * An active record behavior for creating audit records.
 */
class AuditBehavior extends CActiveRecordBehavior {
	// Audit actions
	const ACTION_CREATE = 'create';
	const ACTION_UPDATE = 'update';
	const ACTION_DELETE = 'delete';

	/**
	 * @var string the name of the id column.
	 */
	public $idAttribute = 'id';
	/**
	 * @var array a list of columns that does not create an entry when they change.
	 */
	public $exclude = array();

	private $_oldAttributes;

	/**
	 * Actions to take after fetching the record.
	 * @param CEvent $event event parameter.
	 */
	public function afterFind($event) {
		$this->_oldAttributes = $this->owner->getAttributes();
	}

	/**
	 * Actions to take before saving the record.
	 * @param CModelEvent $event event parameter.
	 */
	public function beforeSave($event) {
		/* @var CActiveRecord $owner */
		$owner = $this->getOwner();
		if (!$owner->isNewRecord) {
			foreach ($owner->getAttributes() as $name => $value) {
				if (in_array($name, $this->exclude)) {
					continue;
				}
				if ((string)$value !== (string)$this->_oldAttributes[$name]) {
					list($changer, $changerId) = $this->getChanger();
					list($model, $modelId) = $this->getModel();

					$entry = new AuditAttribute;
					$entry->changer = $changer;
					$entry->changerId = $changerId;
					$entry->model = $model;
					$entry->modelId = $modelId;
					$entry->attribute = $name;
					$entry->oldValue = $this->_oldAttributes[$name];
					$entry->newValue = $owner->attributes[$name];
					$entry->created = date('Y-m-d H:i:s');
					$entry->save();
				}
			}
		}
	}

	/**
	 * Actions to take after saving the record.
	 * @param CModelEvent $event event parameter.
	 */
	public function afterSave($event) {
		if ($this->owner->isNewRecord) {
			$this->createModelEntry(self::ACTION_CREATE);
		} else {
			$this->createModelEntry(self::ACTION_UPDATE);
		}
	}

	/**
	 * Actions to take before deleting the record.
	 * @param CModelEvent $event event parameter
	 */
	public function beforeDelete($event) {
		$this->createModelEntry(self::ACTION_DELETE);
	}

	/**
	 * Creates an audit entry for the record.
	 * @param $action the audit action.
	 */
	protected function createModelEntry($action) {
		list($changer, $changerId) = $this->getChanger();
		list($model, $modelId) = $this->getModel();

		$entry = new AuditModel;
		$entry->action = $action;
		$entry->changer = $changer;
		$entry->changerId = $changerId;
		$entry->model = $model;
		$entry->modelId = $modelId;
		$entry->created = date('Y-m-d H:i:s');
		$entry->save(false);
	}

	/**
	 * Returns the changer for the audit record.
	 * @return array an array with the changer and changerId (changer, changerId).
	 */
	protected function getChanger() {
		$app = Yii::app();
		if ($app instanceof CWebApplication) {
			/* @var AuditChanger $user */
			$user = $app->getUser();
			$changer = $user->modelClass;
			$changerId = $user->{$user->idAttribute};
		} else {
			$changer = 'Console';
			$changerId = 0;
		}
		return array($changer, $changerId);
	}

	/**
	 * Returns the model for the audit record.
	 * @return array an array with the model and modelId (model, modelId).
	 */
	protected function getModel() {
		$owner = $this->getOwner();
		$model = get_class($owner);
		$modelId = $owner->{$this->idAttribute};
		return array($model, $modelId);
	}
}
