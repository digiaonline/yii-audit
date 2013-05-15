<?php

class m130227_224428_create_audit_model_table extends CDbMigration {
	public function up() {
		$this->execute("CREATE TABLE `audit_model` (
			`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`action` VARCHAR(255) NOT NULL,
			`changer` VARCHAR(255) NOT NULL,
			`changerId` INT(10) UNSIGNED NOT NULL,
			`model` VARCHAR(255) NOT NULL,
			`modelId` INT(10) UNSIGNED NOT NULL,
			`created` DATETIME NOT NULL,
			PRIMARY KEY (`id`)
		) COLLATE='utf8_general_ci' ENGINE=InnoDB;");
	}

	public function down() {
		$this->dropTable('audit_model');
	}
}