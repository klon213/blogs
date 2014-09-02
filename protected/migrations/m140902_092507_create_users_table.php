<?php

class m140902_092507_create_users_table extends CDbMigration
{
	public function up()
	{
            $this->createTable('tbl_users', array(
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'login' => 'string NOT NULL',
            'avatar' => 'string',
            'pass' => 'string',
            'email' => 'string',
            'is_verified' => 'int'
        ));
	}

	public function down()
	{
		echo "m140902_092507_create_users_table does not support migration down.\n";
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