<?php

class m130227_110832_create_audit_attribute_table extends CDbMigration {
	public function up() {
		$this->execute("CREATE TABLE `audit_attribute` (
			`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`changer` VARCHAR(255) NOT NULL,
			`changerId` INT(10) UNSIGNED NOT NULL,
			`model` VARCHAR(255) NOT NULL,
			`modelId` INT(10) UNSIGNED NOT NULL,
			`attribute` VARCHAR(255) NOT NULL,
			`oldValue` LONGTEXT NOT NULL,
			`newValue` LONGTEXT NOT NULL,
			`created` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
		) COLLATE='utf8_general_ci' ENGINE=InnoDB;");
	}

	public function down() {
		$this->dropTable('audit_attribute');
	}
}