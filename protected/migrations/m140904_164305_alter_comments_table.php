<?php

class m140904_164305_alter_comments_table extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('tbl_comments','comment_sdate', 'timestamp NOT NULL');
	}

	public function down()
	{
		echo "m140904_164305_alter_comments_table does not support migration down.\n";
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