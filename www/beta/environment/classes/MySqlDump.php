<?php
class MySqlDump
{
	protected $output = "";
	protected $comment = "";
	protected $dropTables = false;
	
	public function __construct($comment)
	{
		$this->comment = $comment;
	}
	
	public function __toString()
	{
		return $this->dump();
	}
	
	public function dump()
	{
		$this->output = "";
		
		$this->createInfo();
		
		$query = MySql::query("SHOW TABLES");
		
		while($row = MySql::fetchRow($query))
		{
			$tableName = $row[0];
			
			$this->dumpStructure($tableName);
			$this->dumpValues($tableName);
		}
		
		MySql::freeResult($query);
		
		return $this->output;
	}
	
	protected function createInfo()
	{
		$this->output("
-- Jong Designs Admin MySql Dump
-- Host: ". Uri::abs_host . "
-- Date: " . date("r")  ."
");
		
		foreach(explode("\n", $this->comment) AS $comment) $this->output("-- " . $comment . "\n");
		
		$this->output("\n\n\n");
	}
	
	protected function dumpStructure($tableName)
	{
		if($this->dropTables) $this->ouput("DROP TABLE IF EXISTS `$tableName`;\n\n");
		
		$create = MySql::selectRow("SHOW CREATE TABLE `$tableName`");
		$this->output($create["Create Table"]);
		$this->output(";\n\n");
	}
	
	protected function dumpValues($tableName)
	{
		/*
		 * fields eruit trekken, en naar een query schrijven die in een keer 
		 * alle waarden van een rij zó uitprint dat deze direct een insert inkunnen
		 * - alle velden worden gequote
		 * - tussen alle velden worden comma's gezet
		 * - alle velden woren aan elkaar geplakt met een concat
		 */
		$Columns = MySql::select("SHOW COLUMNS FROM `$tableName`");
		$Columns->wrap("QUOTE(`", "`)", "Field");
		$fieldsQuery = "CONCAT('(', " . implode(", ', ', ", $Columns->getColumn("Field")) . ", ')')";
				
		
		$Values = MySql::select("SELECT $fieldsQuery AS dumpvalues FROM `$tableName`");
		
		if($Values->count())
		{
			$this->output("INSERT INTO `$tableName` VALUES \n");
			$this->output(implode(", \n", $Values->getColumn("dumpvalues")));
			$this->output(";\n\n");	
		}
	}
	
	
	protected function output($output = "")
	{
		$this->output .= $output;
	}
}
?>