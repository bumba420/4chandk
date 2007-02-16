<?php
class URL
{
	private $siteURL	=	'';
	
	/*
	function __construct($siteURL)
	{
		$this->siteURL	=	$siteURL;
	}
	*/
	
	static function thumbnailURL($filename)
	{
		return Config::get('page_url').Config::get('/tmp/thumbnails').'/'.$filename;
	}
}
?>