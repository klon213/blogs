<?php

class m140902_093420_create_foreign_keys extends CDbMigration
{
	public function up()
	{
        $this->addForeignKey('FK_articles', 'tbl_articles', 'user_id', 'tbl_users', 'id', 'SET NULL', 'NO ACTION');
	}

	public function down()
	{
		echo "m140902_093420_create_foreign_keys does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}