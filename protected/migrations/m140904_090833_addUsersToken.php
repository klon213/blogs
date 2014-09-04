<?php

class m140904_090833_addUsersToken extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_users', 'token_api', 'varchar(512)');
	}

	public function down()
	{
		echo "m140904_090833_addUsersToken does not support migration down.\n";
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