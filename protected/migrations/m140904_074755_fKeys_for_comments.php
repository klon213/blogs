<?php

class m140904_074755_fKeys_for_comments extends CDbMigration
{
	public function up()
	{
		$this->addForeignKey('FK_comments_articles', 'tbl_comments', 'article_id', 'tbl_articles', 'id', 'SET NULL', 'NO ACTION');
		$this->addForeignKey('FK_comments_users', 'tbl_comments', 'user_id', 'tbl_users', 'id', 'SET NULL', 'NO ACTION');
	}

	public function down()
	{
		echo "m140904_074755_fKeys_for_comments does not support migration down.\n";
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