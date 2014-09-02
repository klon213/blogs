<?php

class m140902_132440_users_set_default_verified extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('tbl_users','is_verified', 'int DEFAULT \'0\'');
	}

	public function down()
	{
		echo "m140902_132440_users_set_default_verified does not support migration down.\n";
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