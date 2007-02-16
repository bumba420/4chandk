<?php
/*
This is slow! many many subquerys when only one is needed.
anyway - it's going to be cached so I'm really not 
going to spend my time on fixing it - right now.
*/
class Writer
{
	static function thread($threadId)
	{
		$thread	=	new Thread($threadId);
		$output =	'';
		$i		= 	1;
		
		//$output	.=	'<div id="'.$thread->getId().'">';
		
		foreach ($thread->posts() as $post)
		{
			/*
			if ($i == 2)
			{
				$output .=	'<div class="secondpost">';
			}
			else 
			{
				$output .=	'<div class="post">';
			}
			*/
			
			if ($i == 1) 
			{
				$output	.=	self::firstPost($thread, $post);	
			}
			else 
			{
				$output	.=	self::nthPost($thread, $post);
			}
			
			++$i;
		}
		
		//$output	.=	'</div>';
				
		return $output;
	}
	
	static function firstPost(Thread $thread, Post $post)
	{
		$image = $post->getFile();
		
		$output	.=	"\n\n\n\n";
		
		//$output .=	'<div class="firstpost">';
		$output	.=	self::postHeader($post);
		
		//$output .=	'<div id="'.$post->getId().'">';
		
		$output	.= '<a target="_blank" href="'.$image->getURL().'">';
		$output	.= '<img src="'.$image->getThumbnailURL().'" class="thumb" />';
		$output	.= '</a>';
		
		$output	.=	'<a name="'.$post->getId().'"></a> <label><input type="checkbox" name="delete" value="'.$post->getId().'" /> ';
		$output	.=	'<span class="filetitle">'.$post->getTitle().'</span> ';
		$output .=	'<span class="postername">'.$post->getName().'</span> ';
		$output	.=	'07/02/14(Wed)12:28</label>';
		$output	.=	'<span class="reflink"><a href="'.$thread->getReplyURL().'">No.'.$post->getId().'</a></span>';
		$output	.=	'&nbsp; [<a href="'.$thread->getReplyURL().'">'.Language::get('post:reply').'</a>]';
		
		$output	.=	'<blockquote>';
		$output .= 	'<p>'.$post->getMessage().'</p>';
		$output	.=	'</blockquote>';
		
		//$output .=	'</div>';
		//$output	.=	'</div>';
		
		$output	.=	"\n\n\n\n";
		
		return $output;
	}
	
	static function nthPost(Thread $thread, Post $post)
	{
		$output	.=	"\n\n\n\n";
		
		//$output	.=	'<div style="border: 2px solid red">';
		
		$image = $post->getFile();
		
		$output	.=	'<table><tbody><tr>';
		$output	.=	'<td class="doubledash">&gt;&gt;</td>';
		$output	.=	'<td class="reply" id="reply'.$post->getId().'"><a name="'.$post->getId().'"></a> <label><input type="checkbox" name="delete" value="'.$post->getId().'" /> ';
		$output	.=	'<span class="commentpostername">'.$post->getName().'</span>';
		$output	.=	'07/02/14(Wed)12:28</label> ';
		$output	.=	'<span class="reflink"><a href="'.$thread->getReplyURL().'">No.'.$post->getId().'</a></span> &nbsp;<br />';
		
		$output	.=	self::postHeader($post);
		
		$output	.= '<a target="_blank" href="'.$image->getURL().'">';
		$output	.= '<img src="'.$image->getThumbnailURL().'" class="thumb" />';
		$output	.= '</a>';
		
		$output	.=	'<blockquote>';
		$output .= 	'<p>'.$post->getMessage().'</p>';
		$output	.=	'</blockquote>';
		
		$output	.=	'</div>';
		
		$output	.=	'</td></tr></tbody></table>';
		
		//$output	.=	'</div>';
		
		$output	.=	"\n\n\n\n";
		
		return $output;
	}
	
	static function board($boardId)
	{
		$board	=	new Board($boardId);
		$output = '';
		
		foreach ($board->threads() as $thread)
		{
			$output .=	self::thread($thread->getId());
			$output	.=	'<br clear="left" /><hr />';
		}
		
		return $output;
	}
	
	static function menu()
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
		$output	.=	'<li><a target="_self" href="?showdirs">[Show Directories]</a></li>';
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
	
	static function form($boardId, $destination = null)
	{
		$destination = is_null($destination) ? $_SERVER['PHP_SELF'] : $destination;
		/*	
		$output .= '<form action="'.$destination.'" method="post" enctype="multipart/form-data" />';
		
		if (!Config::get('fored_anonymous'))
		{
			$output .= '<div><span>'.Language::get('form:name').'</span><input type="text" name="name" size="28" maxlength="75" /></div>';
			$output .= '<div><span>'.Language::get('form:email').'</span><input type="text" name="email" size="28" maxlength="75" /></div>';
		}
		
		$output .= '<div><span>'.Language::get('form:subject').'</span><input type="text" name="subject" size="35" maxlength="75" />';
		$output .= '<input type="submit" name="submit" value="'.Language::get('form:submit').'" /></div>';
		$output .= '<div><span>'.Language::get('form:message').'</span><textarea name="message" cols="48" rows="4"></textarea></div>';
		$output .= '<div><span>'.Language::get('form:file').'</span><input type="file" name="file" size="35" /></div>';
		$output .= '</div><span>'.Language::get('form:password').'</span><input type="password" name="password" size="8" /></div>';
		$output .= '</form>';
		
		return $output;
		*/
		$output .= '<form action="'.$destination.'?p=board&id='.$boardId.'" method="post" enctype="multipart/form-data" />';
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
		$output	.=	'[<label><input type="checkbox" name="nofile" value="on" />'.Language::get('form:nofile').']</label></td>';
		$output	.=	'</tr><tr>';
		$output	.=	'<td class="postblock">'.Language::get('form:password').'</td>';
		$output	.=	'<td><input type="password" name="password" size="8" /> ('.Language::get('form:delete').')</td>';
		$output	.=	'</tr><tr>';
		$output	.=	'<td colspan="2"><div class="rules">';
		/* the list */
		$output	.=	'</div></td></tr></tbody></table>';
		$output	.=	'</form>';
		
		return $output;
		
		/*
		<ul>
		<li>Supported file types are: GIF, JPG, PNG</li>
		<li>Maximum file size allowed is 2048 KB.</li>
		<li>Images greater than 200x200 pixels will be thumbnailed.</li>
		<li>This board is for the posting of anime and other
		crossover/parody images and comics.</li>
		</ul>
		*/
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
	
	static function boardTop()
	{
		
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