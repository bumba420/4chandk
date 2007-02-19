<?php
class URL
{
	private $siteURL	=	'http://127.0.0.1';
	
	/*
	function __construct($siteURL)
	{
		$this->siteURL	=	$siteURL;
	}
	*/
	
	static function thumbnail($filename)
	{
		return Config::get('page_url').Config::get('/tmp/thumbnails').'/'.$filename;
	}
	
	static function current()
	{
		$url	=	$_SERVER['PHP_SELF'];
		$url	.=	'?';
		
		foreach ($_GET as $key => $value)
		{
			$url	.=	$key.'='.$value.'&';
		}
		
		return $url;
	}
	
	static function board($boardID)
	{
		return Config::get('page_url').'?p=board&id='.$boardID;
	}
	
	static function page($boardID, $page)
	{
		return self::board($boardID).'&page='.$page;
	}
	
	static function home()
	{
		return '';//$this->siteURL;
	}
	
	static function banner(Board $board)
	{
		if ($board->getBanner())
		{
			return $board->getBanner();
		}
		
		return 'http://kei.iichan.net/bnr/banner.pl';
	}
	
	static function admin()
	{
		return ;
	}
}
?>