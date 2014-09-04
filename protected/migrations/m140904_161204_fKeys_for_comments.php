<?php

class m140904_161204_fKeys_for_comments extends CDbMigration
{
	public function up()
	{
		$this->addForeignKey('FK_comments_comments', 'tbl_comments', 'parent_id', 'tbl_comments', 'id', 'SET NULL', 'NO ACTION');
	}

	public function down()
	{
		echo "m140904_161204_fKeys_for_comments does not support migration down.\n";
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