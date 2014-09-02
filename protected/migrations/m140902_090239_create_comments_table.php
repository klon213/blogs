<?php

class m140902_090239_create_comments_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('tbl_comments', array(
        'id' => 'pk',
        'user_id' => 'int',
        'article_id'=>'int',
        'guestmail' => 'string',
        'text' => 'varchar(2048)',
        'comment_sdate' => 'datetime',
        'notify_author' => 'int',
        ));
	}

	public function down()
	{
		echo "m140902_090239_create_comments_table does not support migration down.\n";
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