<?php

class m140902_093201_create_migration_clean_up extends CDbMigration
{
	public function up()
	{
        $this->dropTable('tbl_news');
	}

	public function down()
	{
		echo "m140902_093201_create_migration_clean_up does not support migration down.\n";
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