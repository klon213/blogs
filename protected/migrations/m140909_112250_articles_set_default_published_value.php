<?php

class m140909_112250_articles_set_default_published_value extends CDbMigration
{
	public function up()
	{
		$this->alterColumn('tbl_articles','is_published', 'integer NOT NULL DEFAULT 0');

	}

	public function down()
	{
		echo "m140909_112250_articles_set_default_published_value does not support migration down.\n";
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