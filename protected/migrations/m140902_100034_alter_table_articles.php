<?php

class m140902_100034_alter_table_articles extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('tbl_articles', 'pub_date');
        $this->addColumn('tbl_articles', 'pub_date', 'datetime');
	}

	public function down()
	{
		echo "m140902_100034_alter_table_articles does not support migration down.\n";
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