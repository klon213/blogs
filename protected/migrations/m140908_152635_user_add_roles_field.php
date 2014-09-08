<?php

class m140908_152635_user_add_roles_field extends CDbMigration
{
	public function up()
	{
		$this->addColumn('tbl_users', 'role', 'varchar(128)');
	}

	public function down()
	{
		echo "m140908_152635_user_add_roles_field does not support migration down.\n";
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