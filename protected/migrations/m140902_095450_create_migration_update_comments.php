<?php

class m140902_095450_create_migration_update_comments extends CDbMigration
{
	public function up()
	{
        $this->addColumn('tbl_comments', 'parent_id', 'int');
	}

	public function down()
	{
		echo "m140902_095450_create_migration_update_comments does not support migration down.\n";
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