<?php
class ScriptLoader
{	
	protected $scripts;
	
	public function __construct()
	{
		$this->scripts = new DataSet(false, array("dir", "uri", "file"));
	}
	
	public function addFile($dir, $uri, $file)
	{
		$this->scripts->addRow(compact("dir", "uri", "file"));
	}
	
	public function addFiles($dir, $uri, array $files)
	{
		foreach($files as $file) $this->addFile($dir, $uri, $file);
	}
	
	public function addDir($dir, $uri, $limit = 10)
	{
		foreach($this->recursiveFileLoader($dir, "js", $limit) as $file)
		{
			$fileRel = substr($file, strlen($dir));
			$this->addFile($dir, $uri, $fileRel);
		}
	}
	
	public function getDirList()
	{
		$list = array();
		foreach($this->scripts as $row) $list[] = $row["dir"] . $row["file"];
		return $list;
	}
	
	public function getUriList()
	{
		$list = array();
		foreach($this->scripts as $row) $list[] = $row["dir"] . $row["file"];
		return $list;
	}
	
	
	
	
	
	private function recursiveFileLoader($dir, $extention, $limit = 10)
	{
		$files = glob($dir . "*.js");
		if(!$files) return array();
		
		sort($files);
		foreach($files as $key => $file) if(is_dir($dir. $file)) unset($files[$key]);
	
		if($limit > 0)
		{
			$subDirs = glob($dir . "*", GLOB_ONLYDIR);
			if($subDirs)
			{
				sort($subDirs);
				foreach($subDirs as $subDir) $files = array_merge($files, recursiveFileLoader($subDir . "/", $extention, $limit-1));
			}
		}
		
		return $files;
	}
}
?>