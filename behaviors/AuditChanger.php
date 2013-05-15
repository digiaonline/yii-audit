<?php
/**
 * AuditChanger class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-audit.behaviors
 */

/**
 * A behavior for a model that can change a record.
 */
class AuditChanger extends CBehavior {
	/**
	 * @var string the name of the id column.
	 */
	public $idAttribute = 'id';
	/**
	 * @var string the name of the model class.
	 */
	public $modelClass;
}