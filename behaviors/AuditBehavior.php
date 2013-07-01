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
            $this->createModelEntry(AuditModel::ACTION_CREATE);
        } else {
            $this->createModelEntry(AuditModel::ACTION_UPDATE);
        }
    }

    /**
     * Actions to take before deleting the record.
     * @param CModelEvent $event event parameter
     */
    public function beforeDelete($event) {
        $this->createModelEntry(AuditModel::ACTION_DELETE);
    }

    /**
     * Checks the an attribute has changed since the model was loaded.
     * @param string $name the name of the attribute to check.
     * @return boolean the result.
     */
    public function hasAttributeChanged($name) {
        return $this->owner->{$name} !== $this->_oldAttributes[$name];
    }

    /**
     * Returns the model that created the record.
     * @return AuditChanger the model.
     */
    public function getCreator() {
        $model = $this->loadModel(AuditModel::ACTION_CREATE);
        return $model !== null ? $this->loadChangerModel($model) : null;
    }

    /**
     * Returns the creation date time for the record.
     * @return string the date time.
     */
    public function getCreatedAt() {
        $model = $this->loadModel(AuditModel::ACTION_CREATE);
        return $model !== null ? $model->created : null;
    }

    /**
     * Returns the model that updated of the record.
     * @return AuditChanger the model.
     */
    public function getUpdater() {
        $model = $this->loadModel(AuditModel::ACTION_UPDATE);
        return $model !== null ? $this->loadChangerModel($model) : null;
    }

    /**
     * Returns the last edit time for the record.
     * @return string the date time.
     */
    public function getUpdatedAt() {
        $model = $this->loadModel(AuditModel::ACTION_UPDATE);
        return $model !== null ? $model->created : null;
    }

    /**
     * Returns the model that deleted the record.
     * @return AuditChanger the model.
     */
    public function getDeleter() {
        $model = $this->loadModel(AuditModel::ACTION_DELETE);
        return $model !== null ? $this->loadChangerModel($model) : null;
    }

    /**
     * Returns the deletion date time for the record.
     * @return string the date time.
     */
    public function getDeletedAt() {
        $model = $this->loadModel(AuditModel::ACTION_DELETE);
        return $model !== null ? $model->created : null;
    }

    /**
     * Returns the audit model for the record.
     * @return AuditModel the model.
     */
    protected function loadModel($action) {
        list($model, $modelId) = $this->getModel();
        $criteria = new CDbCriteria();
        $criteria->addCondition('model=:model');
        $criteria->addCondition('modelId=:modelId');
        $criteria->addCondition('action=:action');
        $criteria->params = array(
            ':model' => $model,
            ':modelId' => $modelId,
            ':action' => $action,
        );
        $criteria->order = 't.created DESC';
        return AuditModel::model()->find($criteria);
    }

    /**
     * Creates an audit entry for the record.
     * @param string $action the audit action.
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

    /**
     * Loads the changer model for the given audit model.
     * @param AuditModel $model the model.
     * @return AuditChanger the model.
     */
    protected function loadChangerModel($model) {
        return CActiveRecord::model($model->changer)->findByPk($model->changerId);
    }
}
