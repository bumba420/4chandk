<?php
/*
This is slow! many many subquerys when only one is needed.
anyway - it's going to be cached so I'm really not 
going to spend my time on fixing it - right now.
*/
class Writer
{
	static function thread($threadId, $short = false)
	{
		$thread			=	new Thread($threadId);
		$posts			=	$thread->posts();
		$output 		=	'';
		$i				= 	1;
		$omitted_posts	=	0;
		$omitted_images	=	0;
		
		if ($short)
		{
			$all_posts 		= $posts;
			
			$old_size		=	count($posts);
			array_splice($posts, 1, -Config::get('thread_length'));
			$omitted_posts	= $old_size - count($posts);
			
			// Hack - find the number of omitted images
			$omitted = array_diff($all_posts, $posts);
			foreach ($omitted as $post)
			{
				if ($post->hasFile())
				{
					++$omitted_images;
				}
			}
		}
		
		foreach ($posts as $post)
		{
			
			if ($i == 1) 
			{
				$output	.=	self::post($thread, $post, true, $short);
				
				if	($omitted_posts > 0)
				{
					$output	.=	'<span class="omittedposts">';
					$output	.=	sprintf(Language::get('post:omitted'), $omitted_posts, $omitted_images);
					$output	.=	'</span>';
				}
			}
			else 
			{
				$output	.=	self::post($thread, $post, false, $short);
			}
			
			++$i;
		}
		
		return $output;
	}
	
	static function post(Thread $thread, Post $post, $first_post = false, $short = false)
	{
		$image = $post->getFile();
		
		if (!$first_post)
		{
			$output	.=	'<table><tbody><tr>';
			$output	.=	'<td class="doubledash">&gt;&gt;</td>';
			$output	.=	'<td class="reply" id="reply'.$post->getId().'"><a name="'.$post->getId().'"></a> <label><input type="checkbox" name="delete" value="'.$post->getId().'" /> ';
		
			if ($post->getEmail())
			{
				$output	.=	'<a href="mailto:'.$post->getEmail().'">';
			}
			
			$output	.=	'<span class="commentpostername">';
			$output	.=	$post->getName();
			$output	.=	'</span>';
			
			if ($post->getTripecode()) 
			{
				$output	.=	'<span class="postertrip">';
				$output	.=	'!';
				$output	.=	$post->getTripecode();
				$output	.=	'</span>';
			}
			
			if ($post->getEmail())
			{
				$output	.=	'</a>';
			}
		
			$output	.=	$post->getDate().'</label> ';
			$output	.=	'<span class="reflink"><a href="'.$thread->getReplyURL().'">No.'.$post->getId().'</a></span> &nbsp;<br />';
		}
		
		if (!is_null($image))
		{
			$output	.=	self::postHeader($post);
		
			$output	.= '<a target="_blank" href="'.$image->getURL().'">';
			$output	.= '<img src="'.$image->getThumbnailURL().'" class="thumb" />';
			$output	.= '</a>';
		}
		
		if ($first_post)
		{
			$output	.=	'<a name="'.$post->getId().'"></a> <label><input type="checkbox" name="delete" value="'.$post->getId().'" /> ';
			$output	.=	'<span class="filetitle">'.$post->getTitle().'</span> ';
			$output .=	'<span class="postername">'.$post->getName().'</span> ';
			$output	.=	$post->getDate().'</label> ';
			$output	.=	'<span class="reflink"><a href="'.$thread->getReplyURL().'">No.'.$post->getId().'</a></span>';
			$output	.=	'&nbsp; [<a href="'.$thread->getReplyURL().'">'.Language::get('post:reply').'</a>]';
		}
		
		$output	.=	'<blockquote>';
		$output .= 	'<p>';

		if (strlen($post->getMessage()) > Config::get('comment_length') && $short)
		{
			$output	.=	Parser::boardMessage(substr($post->getMessage(), 0, Config::get('comment_length')));
			$output	.=	'<div class="abbrev">';
			$output	.=	Language::get('post:too_long_1');
			$output	.=	' <a href="'.$thread->getReplyURL().'">'.Language::get('post:too_long_2').'</a> ';
			$output	.=	Language::get('post:too_long_3');
			$output	.=	'</div>';
		}
		else 
		{
			$output .= 	'<p>'.Parser::boardMessage($post->getMessage()).'</p>';
		}
		
		$output	.=	'</p>';
		$output	.=	'</blockquote>';
		
		if (!$first_post)
		{
			$output	.=	'</div>';
			$output	.=	'</td></tr></tbody></table>';
		}
		
		return $output;
	}

	static function board(Board $board, $page = 0)
	{
		$output = '';
		
		$amount		=	Config::get('threads_pr_page');
		$offset		=	$page * $amount;
		
		foreach ($board->threads($amount, $offset) as $thread)
		{
			$output .=	self::thread($thread->getId(), true);
			$output	.=	'<br clear="left" /><hr />';
		}
		
		return $output;
	}
	
	static function pager(Board $board, $page = 0)
	{
		$page_amout		= ceil($board->getPostAmount() / Config::get('threads_pr_page'));
		
		$output	.=	'<table border="1"><tbody><tr>';
		if ($page == 0)
		{
			$output	.=	'<td>';
			$output	.=	Language::get('bottom:previous');
			$output	.=	'</td>';
		}
		else 
		{
			$output	.=	'<td>';
			$output	.=	'<form method="get" action="'.URL::page($board->getId(), $page - 1).'">';
			$output	.=	'<input value="'.Language::get('bottom:previous').'" type="submit" />';
			
			// Soooo ugly - FIXME
			//foreach ($_GET as $key => $get)
			//{
			//	$output	.=	'<input type="hidden" name="'.$key.'" value="'.$value.'" />';
			//}
			
			$output	.=	'</form>';
			$output	.=	'</td>';
		}
		
		$output	.=	'<td>';
		
		for ($i = 0; $i < $page_amout; ++$i)
		{
			$output	.=	'&#91;';
			if ($i == $page)
			{
				$output	.=	$i;	
			}
			else 
			{
				$output	.=	'<a href="'.URL::page($board->getId(), $i).'">'.$i.'</a>';
			}
			$output	.=	'&#93;';
		}
			
		$output	.=	'</td>';
		
		if ($page == $page_amout-1)
		{
			$output	.=	'<td>';
			$output	.=	Language::get('bottom:next');
			$output	.=	'</td>';
		}
		else 
		{
			$output	.=	'<td>';
			$output	.=	'<form method="get" action="'.URL::page($board->getId(), $page + 1).'">';
			$output	.=	'<input value="'.Language::get('bottom:next').'" type="submit" />';
			
			// Soooo ugly - FIXME
			//foreach ($_GET as $key => $get)
			//{
			//	$output	.=	'<input type="hidden" name="'.$key.'" value="'.$value.'" />';
			//}
			
			$output	.=	'</form>';
			$output	.=	'</td>';
		}
		
		$output	.=	'</tr></tbody></table>';
		
		return $output;
	}
	
	static function menu($dirs = false)
	{
		$query		=	"SELECT * FROM ".Config::get('section_relation');
		$sections	= array();
		$output = '';
		
		if ($result = Database::singleton()->query($query)) {
			
			while ($row = $result->fetch_assoc()) 
			{
				$section	=	array();
				foreach ($row as $key => $value)
				{
					$section[$key]	= $value;
				}
				$sections[]	=	new Section($row['id']);
				end($sections)->setData($section);
			}
		}
		
		$output	.=	'<h1>4chandk</h1>';
		$output .=	'<ul>';
		$output .=	'<li><a href="http://127.0.0.1" target="_top">Front Page</a></li>';
		$output	.=	'<li><a target="_self" href="?p=menu&showdirs">[Show Directories]</a></li>';
		$output .=	'</ul>';
		
		foreach ($sections as $section)
		{
			$output .= 	'<h2>';
			$output .=	'<span class="plus" onclick="toggle(this,\''.$section->getId().'\');" title="Click to show/hide">&minus;</span>';
			$output	.= 	$section->getName();
			$output .= 	'</h2>';
			
			$output .=	'<div id="'.$section->getId().'">';
			$output .=	'<ul>';

			foreach ($section->boards() as $board)
			{
				$output .=	'<li>';
				$output	.=	'<a href="?p=board&id='.$board->getId().'" target="main">';
				
				$output .=	$board->getName();
				$output	.=	'</a>';
				$output .= 	'</li>';
			}
			
			$output .=	'</ul>';
			$output	.=	'</div>';
		}
		
		return $output;
	}
	
	static function form(Board $board)
	{
		$destination = is_null($destination) ? $_SERVER['PHP_SELF'] : $destination;
		
		// not good
		if (isset($_GET['thread_id']))
		{
			$output	.=	'[<a href="'.$board->getURL().'">'.Language::get('top:return').'</a>]';
			$output	.=	'<div class="theader">'.Language::get('top:mode').'</div>';
		}
		
		$output .= '<form action="'.URL::current().'" method="post" enctype="multipart/form-data" />';
		$output	.=	'<table><tbody><tr>';
		
		if (!Config::get('fored_anonymous'))
		{
			$output	.=	'<td class="postblock">'.Language::get('form:name').'</td>';
			$output	.=	'<td><input type="text" name="name" size="28" /></td>';
			$output	.=	'</tr><tr>';
			$output	.=	'<td class="postblock">'.Language::get('form:email').'</td>';
			$output	.=	'<td><input type="text" name="email" size="28" /></td>';
			$output	.=	'</tr><tr>';
		}
		
		$output	.=	'<td class="postblock">'.Language::get('form:subject').'</td>';
		$output	.=	'<td><input type="text" name="subject" size="35" /> <input type="submit" name="submit" value="'.Language::get('form:submit').'" /></td>';
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('form:message').'</td>';
		$output	.=	'<td><textarea name="message" cols="48" rows="4"></textarea></td>';
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('form:file').'</td>';
		$output	.=	'<td><input type="file" name="file" size="35" />';
		//$output	.=	'[<label><input type="checkbox" name="nofile" value="on" />'.Language::get('form:nofile').']</label>';
		$output	.=	'</td>';
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('form:password').'</td>';
		$output	.=	'<td><input type="password" name="password" value="'.mt_rand(100000000, 999999999).'" size="8" /> ('.Language::get('form:delete').')</td>';
		$output	.=	'</tr><tr>';
		$output	.=	'<td colspan="2"><div class="rules">';
		$output	.=	'</div>';
		
		$output	.=	'<div class="rules">';
		$output	.=	'<ul>';
		$output	.=	'<li>Supported file types are: GIF, JPG, PNG</li>';
		$output	.=	'<li>Maximum file size allowed is '.$board->getFilesizeInKB().' KB.</li>';
		$output	.=	'<li>Images greater than '.Config::get('image_max_width').'x'.Config::get('image_max_height').' pixels will be thumbnailed.</li>';
		$output	.=	'<li>'.$board->getDescription().'</li>';
		$output	.=	'</ul>';
		$output	.=	'</div>';
		
		$output	.=	'</td></tr></tbody></table>';
		$output	.=	'</form>';
		
		return $output;
	}

	static function headerStart()
	{
		$output		=	'<html>';
		$output		.=	'<head>';
		$output		.= 	'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		//$output		.=	'<link rel="Start" title="Home" href="/" />';
		
		return $output;
	}
	
	static function headerEnd()
	{
		$output		.=	'</head>';
		$output		.=	'<body>';
		
		return $output;
	}
	
	static function header()
	{
		return self::headerStart() . self::headerEnd();
	}
	
	static function footer()
	{
		$output		=	'</body>';
		$output		.=	'</html>';
		
		return $output;
	}
	
	static function frameset()
	{
		$output		=	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
		$output		.=	'<html>';
		$output		.=	'<head><title>'.Config::get('page_title').'</title>';
		$output		.=	'</head>';
		$output		.=	'<frameset cols="18%,*" frameborder="0" border="0">';
		$output		.=	'<frame src="?p=menu">';
		$output		.=	'<frame src="?p=board" name="main">';
		$output		.=	'<noframes>';
		$output		.=	'</noframes>';
		$output		.=	'</frameset>';
		$output		.=	'</html>';
		
		return $output;
	}
	
	static function menuCSS()
	{
		$output		.=	'<style type="text/css">';
		$output		.=	'body { font-family: sans-serif; font-size: 75%; background: #ffe }';
		$output		.=	'a { text-decoration: none; color: #550 }';
		$output		.=	'h1,h2 { margin: 0px; background: #fca }';
		$output		.=	'h1 { font-size: 150% }';
		$output		.=	'h2 { font-size: 100%; margin-top: 1em }';
		$output		.=	'.hl { font-style: italic }';
		$output		.=	'.plus { float: right; font-size: 8px; font-weight: normal; padding: 1px 4px 2px 4px; margin: 0px 0px; background: #eb9; color: #000; border: 1px solid #da8; cursor: hand; cursor: pointer }';
		$output		.=	'.plus:hover { background: #da8; border: 1px solid #c97 }';
		$output		.=	'ul { list-style: none; padding-left: 0px; margin: 0px }';
		$output		.=	'li { margin: 0px }';
		$output		.=	'li:hover { background: #fec; }';
		$output		.=	'li a { display: block; width: 100%; }';
		$output		.=	'</style>';
		
		return $output;
	}
	
	static function menuJavascript()
	{
		$output		.=	'<script type="text/javascript">';
		$output		.=	'function toggle(button,area) {';
		$output		.=	'    var tog=document.getElementById(area);';
		$output		.=	'    if(tog.style.display)    {';
		$output		.=	'        tog.style.display="";';
		$output		.=	'    }    else {';
		$output		.=	'        tog.style.display="none";';
		$output		.=	'    }';
		$output		.=	"    button.innerHTML=(tog.style.display)?'+':'&minus;';";
		$output		.=	"    createCookie('nav_show_'+area, tog.style.display?'0':'1', 365);";
		$output		.=	'}';
		$output		.=	'</script>';
		
		return $output;
	}
	
	static function boardCSS()
	{
		$output		.=	'<style type="text/css">';
    	$output		.=	'body { margin: 0; padding: 8px; margin-bottom: auto; } blockquote blockquote { margin-left: 0em } form { margin-bottom: 0px } form .trap { display:none } .postarea { text-align: center } .postarea table { margin: 0px auto; text-align: left } .thumb { border: none; float: left; margin: 2px 20px } .nothumb { float: left; background: #eee; border: 2px dashed #aaa; text-align: center; margin: 2px 20px; padding: 1em 0.5em 1em 0.5em; } .reply blockquote, blockquote :last-child { margin-bottom: 0em } .reflink a { color: inherit; text-decoration: none } .reply .filesize { margin-left: 20px } .userdelete { float: right; text-align: center; white-space: nowrap } .replypage .replylink { display: none }';
    	$output		.=	'.admin { color: purple;    font-weight:normal; }';
    	$output		.=	'.mod { color: red; font-weight:normal; }';
    	$output		.=	'</style>';
    	
    	$output		.=	'<link rel="stylesheet" type="text/css" href="stylesheets/futaba.css" title="Futaba">';
    	$output		.=	'<link rel="alternate stylesheet" type="text/css" href="stylesheets/burichan.css" title="Burichan">';
    	$output		.=	'<link rel="alternate stylesheet" type="text/css" href="stylesheets/gurochan.css" title="Gurochan">';
    	$output		.=	'<link rel="alternate stylesheet" type="text/css" href="stylesheets/photon.css" title="Photon">';
    	$output		.=	'<link rel="alternate stylesheet" type="text/css" href="stylesheets/fuhrerchan.css" title="Fuhrerchan">';
    	
    	return $output;
	}
	
	static function boardJavascript()
	{
		$output	.=	'<script type="text/javascript">var style_cookie="wakabastyle";</script>';
		$output	.=	'<script type="text/javascript" src="'.Config::get('javascript_url').'"></script>';
		
		return $output;
	}
	
	static function navigationBar()
	{
		$query		=	"SELECT * FROM ".Config::get('section_relation');
		$sections	= array();
		$output = '';
		
		if ($result = Database::singleton()->query($query)) {
			
			while ($row = $result->fetch_assoc()) 
			{
				$section	=	array();
				foreach ($row as $key => $value)
				{
					$section[$key]	= $value;
				}
				$sections[]	=	new Section($row['id']);
				end($sections)->setData($section);
			}
		}
		
		$output	.=	'<div class="navbar">';
		
		foreach ($sections as $section)
		{
			$output	.=	'[';

			$not_first_rund	= false;
			foreach ($section->boards() as $board)
			{
				if ($not_first_rund)
				{
					$output	.=	' / ';
					$not_first_rund	= true;
				}
				
				$output	.=	'<a href="'.URL::board($board->getId()).'" title="'.$board->getName().'">'.$board->getDirectory().'</a>';
				$not_first_rund	= true;
			}
			
			$output	.=	']';
		}
		
		$output	.=	'</div>';
		
		return $output;
	}
	
	static function boardTop(Board $board)
	{
		$output	.= self::navigationBar();
		
		
		$output	.=	'<div class="adminbar">  ';
		$output	.=	'[<a href="javascript:set_stylesheet(\'Burichan\')">Burichan</a>]  ';
		$output	.=	'[<a href="javascript:set_stylesheet(\'Futaba\')">Futaba</a>]  ';
		$output	.=	'[<a href="javascript:set_stylesheet(\'Gurochan\')">Gurochan</a>]  ';
		$output	.=	'[<a href="javascript:set_stylesheet(\'Photon\')">Photon</a>]  - ';
		$output	.=	'[<a href="'.URL::home().'" target="_top">'.Language::get('top:home').'</a>] ';
		$output	.=	'[<a href="'.URL::admin().'">'.Language::get('top:manage').'</a>] ';
		$output	.=	'</div> ';
		
		$output	.=	'<div class="logo">';
		$output	.=	'<img src="'.URL::banner($board).'" alt="iiChan - Female/Female" /><br />';
		$output	.=	$board->getName();
		$output	.=	'</div>';
		$output	.=	'<hr />';
		
		return $output;
	}
	
	static function boardBottom(Board $board)
	{
		$output	.=	'<table class="userdelete"><tbody><tr><td>';
		$output	.=	Language::get('bottom:delete_post').' ';
		$output	.=	'[<label><input type="checkbox" name="fileonly" value="on" />'.Language::get('bottom:file_only').'</label>]';
		$output	.=	'<br>'.Language::get('bottom:password').' ';
		$output	.=	'<input type="password" name="postpassword" size="8" />&nbsp;';
		$output	.=	'<input name="deletepost" value="'.Language::get('bottom:delete').'" type="submit" />';
		$output	.=	'<input name="reportpost" value="'.Language::get('bottom:report').'" type="submit" />';
		$output	.=	'</td></tr></tbody></table></form>';
		$output	.=	'<script type="text/javascript">set_delpass("delform")</script>';

		$output	.=	self::pager($board, intval($_GET['page']));
		
		$output	.=	'<br />';
		
		$output	.=	self::navigationBar();
		
		$output	.=	'<p class="footer">- <a href="http://code.google.com/p/4chandk/" target="_top">4chandk</a> Created by <a href="http://www.bottiger.com/" target="_top">Bottiger</a> - Delete this if you want, I don\'t care, this is board is not under some nazilicense<br>Took 0.25s</p>';

		return $output;
	}
	
	private static function postHeader(Post $post)
	{
		// Speed it up a bit
		$image = $post->getFile();
		
		$output	.=	'<span class="filesize">';
		$output	.=	Language::get('post:file').': ';
		$output	.=	'<a target="_blank" href="'.$image->getURL().'">';
		$output	.=	$image->getFilename();
		$output	.=	'</a>';
		$output	.=	' -';
		$output	.=	'(<em>'.$image->getFilesize().'KB, '.$image->getWidth().'x'.$image->getHeight().'</em>)</span> ';
		$output	.=	'<span class="thumbnailmsg">'.Language::get('post:thumbnail').'</span>';
		$output	.=	'<br />';
		
		return	$output;
	}
}
?>