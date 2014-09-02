<?php

class m140902_083513_create_articles_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('tbl_articles', array(
            'id' => 'pk',
            'user_id' => 'int',
            'pic' => 'string',
            'title' => 'string NOT NULL',
            'text' => 'varchar(2048)',
            'pub_date' => 'string',
            'is_published' => 'int',
        ));

        //$this->addForeignKey('FK_articles', 'tbl_articles', 'user_id', 'tbl_users', 'id', 'SET NULL', 'NO ACTION');
	}

	public function down()
	{
		echo "m140902_083513_create_articles_table does not support migration down.\n";
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