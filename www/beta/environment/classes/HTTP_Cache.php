<?php
class HTTP_Cache
{
	const CACHE_PUBLIC = 0;
	const CACHE_PRIVATE = 1;
	const CACHE_NO_CACHE = 2;
	
	/*
	 * 	inspiratie: http://nedmartin.org/site/caching
	 */
	
	public function __construct()
	{
	}
	
	public function useLastModification($timestamp)
	{
		$modifiedSince = $this->getModifiedSince();
		
		if($modifiedSince && $modifiedSince >= $timestamp)
		{
			$this->sendNotModified();
		}
		else
		{
			$this->sendLastModified($timestamp);
		}
	}
	
	
	public function useEtag($etag)
	{
		$header = $this->getETag();
		
		if($header && ($etag == $header || "\"$etag\"-gzip" == $header))
		{
			$this->sendNotModified();
		}
		else
		{
			$this->sendETag($etag);
		}
	}
	

	public function getModifiedSince()
	{
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
		{
			return strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']); 	
		}
		else
		{
			return false;
		}
	}
	
	public function getETag()
	{
		if(isset($_SERVER["HTTP_IF_NONE_MATCH"]))
		{
			return $_SERVER["HTTP_IF_NONE_MATCH"];
		}
		else
		{
			return false;
		}
	}
	
	public function sendNotModified()
	{
		header("HTTP/1.0 304 Not Modified");
		exit;
	}
	
	public function sendLastModified($timestamp)
	{
		$gmt_mtime = gmdate('D, d M Y H:i:s', $timestamp).' GMT';
		header('Last-Modified: '.$gmt_mtime);	
	}
	
	public function sendETag($etag)
	{
		header('ETag: "'.$etag.'"');	
	}
		
	public function sendCacheControl($cacheType)
	{
		switch($cacheType)
		{
			case self::CACHE_PUBLIC : header('Cache-Control: public'); break;
			case self::CACHE_PRIVATE : header('Cache-Control: private'); break;
			case self::CACHE_NO_CACHE : header('Cache-Control: no-cache'); break;
			default : throw new Exception("Unknown cacheType: " . print_r($cacheType, true));
		}
	}
	
	public function sendExpires($timestamp)
	{
		header('Expires: '.gmdate('D, d M Y H:i:s', $timestamp).' GMT');
	}
}


?>