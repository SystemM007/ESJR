<?php

class DatabaseBackup
{
	protected $database;
	
	public function __construct($comment = "")
	{
		ClockLog::$version = "beta";
		$Clock = new ClockLog();
			
		extract( include(Dir::environment . "mysql.php"));
		$this->database = $mysql_database; 
				
		$data = $this->makeDump($comment);
		$data = $this->makeGz($data);
		$this->putData($destination, $data);
		
		Settings::update("last_backup", time());
		
		$Clock->finish("backup");
	}
	
	protected function makeDump($comment)
	{
		//$Clock = new ClockLog();
		//$data = (string) new MySqlDump($comment);
		$Dump = new MySqlDump($comment);
		$data = $Dump->dump();
		
		
		//$Clock->finish("dump");
		return $data;
	}
	
	protected function makeGz($data)
	{
		//$Clock = new ClockLog();
		$data = gzencode($data);
		//$Clock->finish("encode");
		return $data;
	}
	
	protected function putData($destination, $data)
	{
		//$Clock = new ClockLog();
		
		$server = Settings::get("backup_server");
		list($user, $server) = explode(":", $server, 2);
		list($passwd, $server) = explode("@", $server, 2);
		list($server, $path)  = explode("/", $server, 2);
		
		// verbinden met 10 seconden timeout
		if(!$conn_id = ftp_connect($server, 0, 10))
		{
			throw new Exception("Kan niet verbinden met backupserver");
		}
		
		if(!ftp_login($conn_id, $user, $passwd))
		{
			throw new Exception("Kan niet inloggen op backupserver");
		}
		
		$backupDir = $path . Uri::abs_host . '-' . $this->database . '/';
		$fileName = date("Y-m-d H-i-s") . ".sql.gz";
		
		try
		{
			ftp_mkdir($conn_id, $backupDir);
		}
		catch(Exception $E){};
				
		$tmp = tmpfile();
		
		/*
		 * hele lompe hack voor mijn localhost die geen tmpfiles aan wil maken
		 * @todo fix tmpfile
		 */
		if(!$tmp)
		{
			// comment out omdat dit op de server borkt
			
			// putenv("TMPDIR=/tmp/php");
			// Response::msg("allo");
			// $tmp = tmpfile();	
		}
	
		fwrite($tmp, $data);
		fseek($tmp, 0);
		ftp_fput($conn_id, $backupDir . $fileName, $tmp, FTP_BINARY);
		fclose($tmp); 
				
		ftp_close($conn_id);
		
		//$Clock->finish("put");	
	}
}